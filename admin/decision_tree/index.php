<?php
  include('../../cekadmin.php');
  include('../../connection.php');
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Aplikasi Kelayakan Donor Darah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/css/metisMenu.css">
    <link rel="stylesheet" href="../../assets/css/slicknav.min.css">
    <link rel="stylesheet" href="../../assets/css/typography.css">
    <link rel="stylesheet" href="../../assets/css/default-css.css?v=2">
    <link rel="stylesheet" href="../../assets/css/styles.css?v=2">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        .rule-card { background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .rule-if { color: #d9534f; font-weight: bold; }
        .rule-then { color: #5cb85c; font-weight: bold; }
        .rule-cond { color: #0275d8; font-weight: bold; }

        /* CSS Tree Styles */
        .css-tree {
            text-align: left;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: auto;
        }
        .css-tree ul {
            list-style: none;
            padding-left: 30px;
            position: relative;
            margin: 0;
            display: block;
        }
        /* Vertical line connecting siblings */
        .css-tree ul::before {
            content: "";
            position: absolute;
            top: 0;
            left: 10px;
            width: 2px;
            height: 100%;
            background: #cbd5e1;
        }
        .css-tree li {
            position: relative;
            margin: 12px 0;
            padding-left: 20px;
            display: block;
            list-style-type: none;
        }
        /* Horizontal line connecting node to branch */
        .css-tree li::before {
            content: "";
            position: absolute;
            top: 18px;
            left: -20px;
            width: 25px;
            height: 2px;
            background: #cbd5e1;
        }
        /* Mask the bottom of the vertical line for the last child */
        .css-tree li:last-child::after {
            content: "";
            position: absolute;
            top: 18px;
            left: -20px;
            width: 3px;
            height: 100%;
            background: #fff;
        }
        /* Remove lines for the root list */
        .css-tree > ul::before {
            display: none;
        }
        .css-tree > ul > li {
            padding-left: 0;
        }
        .css-tree > ul > li::before {
            display: none;
        }
        .css-tree > ul > li:last-child::after {
            display: none;
        }

        .css-tree .edge-label {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 4px;
            margin-right: 8px;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .css-tree li a {
            display: inline-block;
            padding: 8px 14px;
            text-decoration: none;
            font-size: 13px;
            border-radius: 6px;
            transition: all 0.2s;
            font-weight: 600;
            white-space: nowrap;
            vertical-align: middle;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .css-tree .node-attr {
            background-color: #fff7ed;
            border: 2px solid #fdba74;
            color: #c2410c;
        }
        .css-tree .node-attr:hover {
            background-color: #ffedd5;
            border-color: #f97316;
        }
        .css-tree .node-leaf {
            color: #fff;
            border: none;
        }
        .css-tree .leaf-yes {
            background-color: #10b981;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }
        .css-tree .leaf-yes:hover {
            background-color: #059669;
        }
        .css-tree .leaf-no {
            background-color: #ef4444;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
        }
        .css-tree .leaf-no:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>
    <div id="preloader"><div class="loader"></div></div>
    <div class="page-container">
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                    <h3><b>Administrator</b></h3>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li><a href="../"><i class="ti-dashboard"></i><span>Dashboard</span></a></li>
                            <li><a href="../data_donor"><i class="ti-server"></i><span>Data Donor</span></a></li>
                            <li><a href="../partisi_data"><i class="ti-files"></i><span>Partisi Data</span></a></li>
                            <li><a href="../perhitungan"><i class="ti-pencil-alt"></i><span>Perhitungan</span></a></li>
                            <li class="active"><a href="../decision_tree"><i class="ti-vector"></i><span>Pohon Keputusan</span></a></li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-sidebar-left"></i><span>Kinerja</span></a>
                                <ul class="collapse">
                                    <li><a href="../perbandingan">Tabel Perbandingan</a></li>
                                    <li><a href="../tabel_penilaian">Tabel Penilaian</a></li>
                                </ul>
                            </li>
                            <li><a href="../hasil_prediksi"><i class="ti-announcement"></i><span>Hasil Prediksi</span></a></li>
                            <li><a href="../../logout.php"><span>Log out</span></a></li>                       
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Aturan Pohon Keputusan (Rules)</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content-inner">
                <div class="row">
                    <!-- Visualisasi Tree -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body" style="overflow-x: auto;">
                                <h4 class="header-title">Visualisasi Pohon Keputusan</h4>
                                <?php
                                $sql_tree = mysqli_query($connection, "SELECT * FROM c45_rules");
                                if(mysqli_num_rows($sql_tree) > 0) {
                                    $rules_data = [];
                                    while($row_tree = mysqli_fetch_assoc($sql_tree)) {
                                        $rules_data[] = ['path' => $row_tree['rule_path'], 'class' => $row_tree['kelas']];
                                    }

                                    $tree_array = [];
                                    foreach($rules_data as $rule_item) {
                                        $conditions = explode(" AND ", $rule_item['path']);
                                        $currentNode = &$tree_array;
                                        foreach($conditions as $cond) {
                                            $cond = trim($cond);
                                            if (!isset($currentNode[$cond])) {
                                                $currentNode[$cond] = [];
                                            }
                                            $currentNode = &$currentNode[$cond];
                                        }
                                        $currentNode['__class'] = $rule_item['class'];
                                    }

                                    if (!function_exists('getAttributeAndCondition')) {
                                        function getAttributeAndCondition($condStr) {
                                            if (strpos($condStr, ' == ') !== false) {
                                                $parts = explode(' == ', $condStr);
                                                return [$parts[0], str_replace("'", "", $parts[1])];
                                            } else if (strpos($condStr, ' <=') !== false) {
                                                $parts = explode(' <=', $condStr);
                                                return [$parts[0], '<= ' . $parts[1]];
                                            } else if (strpos($condStr, ' >') !== false) {
                                                $parts = explode(' >', $condStr);
                                                return [$parts[0], '> ' . $parts[1]];
                                            }
                                            return [$condStr, ''];
                                        }
                                    }

                                    if (!function_exists('buildTreeHTML')) {
                                        function buildTreeHTML($arr) {
                                            if (isset($arr['__class'])) {
                                                $class = $arr['__class'];
                                                $cssClass = isYes($class) ? 'leaf-yes' : 'leaf-no';
                                                $label = isYes($class) ? 'Layak (Ya)' : 'Tidak Layak (Tidak)';
                                                return "<a href='javascript:void(0);' class='node-leaf $cssClass'>$label</a>";
                                            }

                                            $firstKey = array_key_first($arr);
                                            list($attr, $cond) = getAttributeAndCondition($firstKey);
                                            $attrName = strtoupper(str_replace('_', ' ', $attr));

                                            $html = "<a href='javascript:void(0);' class='node-attr'>$attrName</a>";
                                            $html .= "<ul>";
                                            foreach($arr as $key => $subTree) {
                                                list($childAttr, $childCond) = getAttributeAndCondition($key);
                                                $html .= "<li>";
                                                $html .= "<div class='edge-label'>$childCond</div>";
                                                $html .= buildTreeHTML($subTree);
                                                $html .= "</li>";
                                            }
                                            $html .= "</ul>";

                                            return $html;
                                        }
                                    }
                                    
                                    echo "<div class='css-tree'><ul><li>";
                                    echo buildTreeHTML($tree_array);
                                    echo "</li></ul></div>";
                                } else {
                                    echo "<div class='alert alert-warning'>Belum ada pohon keputusan yang terbentuk. Silakan lakukan perhitungan pada menu Perhitungan terlebih dahulu.</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Aturan -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Hasil Algoritma Decision Tree C4.5</h4>
                                <p class="text-muted font-14 mb-4">Berikut adalah himpunan aturan klasifikasi kelayakan donor darah berdasarkan dataset training.</p>
                                <?php
                                $sql = mysqli_query($connection, "SELECT * FROM c45_rules");
                                if(mysqli_num_rows($sql) > 0) {
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($sql)) {
                                        $path = htmlspecialchars($row['rule_path']);
                                        $kelas = htmlspecialchars($row['kelas']);
                                        $kategori_label = isYes($kelas) ? "<span class='badge badge-success'>Layak (Ya)</span>" : "<span class='badge badge-danger'>Tidak Layak (Tidak)</span>";
                                        
                                        echo "<div class='rule-card'>";
                                        echo "<strong>Rule $no:</strong> <span class='rule-if'>IF</span> <span class='rule-cond'>$path</span> <span class='rule-then'>THEN</span> Kategori = $kategori_label";
                                        echo "</div>";
                                        $no++;
                                    }
                                } else {
                                    echo "<div class='alert alert-warning'>Belum ada rule yang terbentuk. Silakan lakukan perhitungan pada menu Perhitungan terlebih dahulu.</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <div class="footer-area">
                <p>© Copyright 2026. All right reserved.</p>
            </div>
        </footer>
    </div>
    <script src="../../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/metisMenu.min.js"></script>
    <script src="../../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../../assets/js/jquery.slicknav.min.js"></script>
    <script src="../../assets/js/scripts.js"></script>
</body>
</html>
