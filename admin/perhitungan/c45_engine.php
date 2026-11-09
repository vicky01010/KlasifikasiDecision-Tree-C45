<?php

class C45 {
    private $targetCol;
    private $continuousAttrs = [];
    private $categoricalAttrs = [];
    private $connection;
    public $rules = [];

    public function __construct($connection, $targetCol, $continuous, $categorical) {
        $this->connection = $connection;
        $this->targetCol = $targetCol;
        $this->continuousAttrs = $continuous;
        $this->categoricalAttrs = $categorical;
    }

    public function getEntropy($data) {
        if(count($data) == 0) return 0;
        $counts = [];
        foreach($data as $row) {
            $val = $row[$this->targetCol];
            if(!isset($counts[$val])) $counts[$val] = 0;
            $counts[$val]++;
        }
        $entropy = 0;
        $total = count($data);
        foreach($counts as $count) {
            $p = $count / $total;
            $entropy -= $p * log($p, 2);
        }
        return $entropy;
    }

    public function getGainCategorical($data, $attrCol) {
        $baseEntropy = $this->getEntropy($data);
        $total = count($data);
        $subsets = [];
        foreach($data as $row) {
            $val = $row[$attrCol];
            if(!isset($subsets[$val])) $subsets[$val] = [];
            $subsets[$val][] = $row;
        }

        $newEntropy = 0;
        foreach($subsets as $subset) {
            $newEntropy += (count($subset) / $total) * $this->getEntropy($subset);
        }
        return [
            'gain' => $baseEntropy - $newEntropy,
            'subsets' => $subsets,
            'split_type' => 'categorical'
        ];
    }

    public function getGainContinuous($data, $attrCol) {
        if(count($data) == 0) return ['gain' => 0];

        $baseEntropy = $this->getEntropy($data);
        $total = count($data);

        usort($data, function($a, $b) use ($attrCol) {
            return $a[$attrCol] <=> $b[$attrCol];
        });

        $bestGain = -1;
        $bestThreshold = null;
        $bestSubsets = [];

        for($i = 0; $i < count($data) - 1; $i++) {
            if($data[$i][$this->targetCol] != $data[$i+1][$this->targetCol] && $data[$i][$attrCol] != $data[$i+1][$attrCol]) {
                $threshold = ($data[$i][$attrCol] + $data[$i+1][$attrCol]) / 2;
                
                $left = [];
                $right = [];
                foreach($data as $row) {
                    if($row[$attrCol] <= $threshold) $left[] = $row;
                    else $right[] = $row;
                }

                $newEntropy = (count($left) / $total) * $this->getEntropy($left) + (count($right) / $total) * $this->getEntropy($right);
                $gain = $baseEntropy - $newEntropy;

                if($gain > $bestGain) {
                    $bestGain = $gain;
                    $bestThreshold = $threshold;
                    $bestSubsets = ['<=' . $threshold => $left, '>' . $threshold => $right];
                }
            }
        }

        if($bestGain == -1) {
            return ['gain' => 0];
        }

        return [
            'gain' => $bestGain,
            'threshold' => $bestThreshold,
            'subsets' => $bestSubsets,
            'split_type' => 'continuous'
        ];
    }

    public function isHomogeneous($data) {
        if(count($data) == 0) return true;
        $first = $data[0][$this->targetCol];
        foreach($data as $row) {
            if($row[$this->targetCol] != $first) return false;
        }
        return true;
    }

    public function getMajorityClass($data) {
        $counts = [];
        foreach($data as $row) {
            $val = $row[$this->targetCol];
            if(!isset($counts[$val])) $counts[$val] = 0;
            $counts[$val]++;
        }
        arsort($counts);
        return array_key_first($counts);
    }

    public function buildTree($data, $availableAttrs) {
        if(empty($data)) return null;

        if($this->isHomogeneous($data)) {
            return ['type' => 'leaf', 'class' => $data[0][$this->targetCol]];
        }

        if(empty($availableAttrs)) {
            return ['type' => 'leaf', 'class' => $this->getMajorityClass($data)];
        }

        $bestGain = -1;
        $bestAttr = null;
        $bestSplit = null;

        foreach($availableAttrs as $attr) {
            if(in_array($attr, $this->continuousAttrs)) {
                $split = $this->getGainContinuous($data, $attr);
            } else {
                $split = $this->getGainCategorical($data, $attr);
            }

            if($split['gain'] > $bestGain) {
                $bestGain = $split['gain'];
                $bestAttr = $attr;
                $bestSplit = $split;
            }
        }

        if($bestGain == 0 || !$bestSplit) {
            return ['type' => 'leaf', 'class' => $this->getMajorityClass($data)];
        }

        $node = [
            'type' => 'node',
            'attribute' => $bestAttr,
            'split_type' => $bestSplit['split_type'],
            'children' => []
        ];

        if($bestSplit['split_type'] == 'continuous') {
            $node['threshold'] = $bestSplit['threshold'];
        }

        $newAttrs = array_filter($availableAttrs, function($a) use ($bestAttr) {
            return $a != $bestAttr;
        });

        foreach($bestSplit['subsets'] as $key => $subset) {
            $node['children'][$key] = $this->buildTree($subset, $newAttrs);
        }

        return $node;
    }

    public function generateRules($tree, $currentPath = "") {
        if(!$tree) return;
        if($tree['type'] == 'leaf') {
            $currentPath = rtrim($currentPath, " AND ");
            $this->rules[] = ['rule' => $currentPath, 'class' => $tree['class']];
        } else {
            foreach($tree['children'] as $key => $child) {
                if($tree['split_type'] == 'continuous') {
                    $condition = $tree['attribute'] . " " . $key;
                } else {
                    $condition = $tree['attribute'] . " == '" . $key . "'";
                }
                $this->generateRules($child, $currentPath . $condition . " AND ");
            }
        }
    }

    public function saveRulesToDB() {
        mysqli_query($this->connection, "TRUNCATE c45_rules");
        foreach($this->rules as $rule) {
            $r = mysqli_real_escape_string($this->connection, $rule['rule']);
            $c = mysqli_real_escape_string($this->connection, $rule['class']);
            mysqli_query($this->connection, "INSERT INTO c45_rules (rule_path, kelas) VALUES ('$r', '$c')");
        }
    }

    public static function predictTree($tree, $input) {
        if (!$tree) return "Unknown";
        if ($tree['type'] == 'leaf') {
            return $tree['class'];
        }
        
        $attr = $tree['attribute'];
        $val = isset($input[$attr]) ? $input[$attr] : null;

        if ($tree['split_type'] == 'continuous') {
            $threshold = $tree['threshold'];
            if ($val <= $threshold) {
                return self::predictTree($tree['children']['<=' . $threshold], $input);
            } else {
                return self::predictTree($tree['children']['>' . $threshold], $input);
            }
        } else {
            if (isset($tree['children'][$val])) {
                return self::predictTree($tree['children'][$val], $input);
            } else {
                return "Unknown";
            }
        }
    }
}
?>
