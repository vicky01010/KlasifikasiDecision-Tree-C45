<?php

class SMOTE {
    private $k;
    private $targetCol;
    private $continuousAttrs = [];
    private $categoricalAttrs = [];

    public function __construct($targetCol, $continuous, $categorical, $k = 5) {
        $this->targetCol = $targetCol;
        $this->continuousAttrs = $continuous;
        $this->categoricalAttrs = $categorical;
        $this->k = $k;
    }

    public function balance($data) {
        if (empty($data)) {
            return $data;
        }

        // 1. Group data by class mapping to standard 'Ya' and 'Tidak'
        $classes = [];
        foreach ($data as $row) {
            $val = $row[$this->targetCol];
            $c = isYes($val) ? 'Ya' : 'Tidak';
            $classes[$c][] = $row;
        }

        if (count($classes) < 2) {
            return $data; // Cannot balance if only 1 class is present
        }

        // Find majority and minority classes
        $classCounts = array_map('count', $classes);
        arsort($classCounts);
        $majorityClass = array_key_first($classCounts);
        $minorityClass = array_key_last($classCounts);

        $majorityCount = $classCounts[$majorityClass];
        $minorityCount = $classCounts[$minorityClass];

        if ($majorityCount === $minorityCount) {
            return $data; // Already balanced
        }

        $minorityData = $classes[$minorityClass];
        $synthetic = [];
        $numToGenerate = $majorityCount - $minorityCount;

        // Calculate Min-Max bounds for continuous features to normalize distances
        $minMax = $this->getMinMax($data);

        // Adjust K if we have fewer minority class instances than requested K
        $actualK = min($this->k, count($minorityData) - 1);

        if ($actualK < 1) {
            // If we don't have enough instances to perform KNN, fall back to random duplication
            while (count($synthetic) < $numToGenerate) {
                $idx = array_rand($minorityData);
                $synthetic[] = $this->createDuplicateSample($minorityData[$idx]);
            }
        } else {
            // Generate synthetic samples using SMOTE algorithm
            while (count($synthetic) < $numToGenerate) {
                // Randomly select a minority sample to generate from
                $randIdx = array_rand($minorityData);
                $sample = $minorityData[$randIdx];

                // Find KNN among minority class instances
                $neighbors = $this->getKNN($sample, $minorityData, $randIdx, $actualK, $minMax);

                if (!empty($neighbors)) {
                    // Randomly select one of the KNN
                    $neighbor = $neighbors[array_rand($neighbors)];
                    $synthetic[] = $this->createSyntheticSample($sample, $neighbor);
                } else {
                    // Fall back to duplicating the sample
                    $synthetic[] = $this->createDuplicateSample($sample);
                }
            }
        }

        // Merge original data with the synthesized samples
        return array_merge($data, $synthetic);
    }

    private function getMinMax($data) {
        $minMax = [];
        foreach ($this->continuousAttrs as $attr) {
            $vals = array_column($data, $attr);
            if (!empty($vals)) {
                $minMax[$attr] = [
                    'min' => min($vals),
                    'max' => max($vals)
                ];
            }
        }
        return $minMax;
    }

    private function getKNN($sample, $minorityData, $sampleIdx, $k, $minMax) {
        $distances = [];
        foreach ($minorityData as $idx => $other) {
            if ($idx === $sampleIdx) continue;
            $dist = $this->calculateDistance($sample, $other, $minMax);
            $distances[] = [
                'index' => $idx,
                'distance' => $dist
            ];
        }

        // Sort by distance ascending
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $knn = [];
        for ($i = 0; $i < $k && $i < count($distances); $i++) {
            $knn[] = $minorityData[$distances[$i]['index']];
        }
        return $knn;
    }

    private function calculateDistance($x, $y, $minMax) {
        $sum = 0;
        // Continuous attribute distance using normalized values
        foreach ($this->continuousAttrs as $attr) {
            if (isset($minMax[$attr])) {
                $min = $minMax[$attr]['min'];
                $max = $minMax[$attr]['max'];
                $range = $max - $min;
                if ($range > 0) {
                    $x_norm = ($x[$attr] - $min) / $range;
                    $y_norm = ($y[$attr] - $min) / $range;
                    $sum += pow($x_norm - $y_norm, 2);
                }
            }
        }
        // Categorical attribute distance: 0 if same, 1 if different
        foreach ($this->categoricalAttrs as $attr) {
            if ($x[$attr] !== $y[$attr]) {
                $sum += 1;
            }
        }
        return sqrt($sum);
    }

    private function createSyntheticSample($x, $y) {
        $newSample = [];
        foreach ($x as $key => $val) {
            $newSample[$key] = $val;
        }

        // Generate unique donor ID and suffix name
        $newSample['donor_id'] = $x['donor_id'] . '_syn_' . bin2hex(random_bytes(3));
        $newSample['nama'] = $x['nama'] . ' (SMOTE)';

        // Interpolation for continuous attributes
        foreach ($this->continuousAttrs as $attr) {
            $lambda = mt_rand() / mt_getrandmax();
            $diff = $y[$attr] - $x[$attr];
            $newSample[$attr] = $x[$attr] + ($lambda * $diff);

            if ($attr === 'berat_badan' || $attr === 'hemoglobin') {
                $newSample[$attr] = round($newSample[$attr], 1);
            } else {
                $newSample[$attr] = round($newSample[$attr]);
            }
        }

        // Randomly pick categorical attribute values from either parent
        foreach ($this->categoricalAttrs as $attr) {
            $newSample[$attr] = (rand(0, 1) === 1) ? $y[$attr] : $x[$attr];
        }

        $newSample['status_data'] = 'DATA TRAINING';

        return $newSample;
    }

    private function createDuplicateSample($x) {
        $newSample = [];
        foreach ($x as $key => $val) {
            $newSample[$key] = $val;
        }
        $newSample['donor_id'] = $x['donor_id'] . '_syn_' . bin2hex(random_bytes(3));
        $newSample['nama'] = $x['nama'] . ' (SMOTE Dupl)';
        $newSample['status_data'] = 'DATA TRAINING';
        return $newSample;
    }
}
