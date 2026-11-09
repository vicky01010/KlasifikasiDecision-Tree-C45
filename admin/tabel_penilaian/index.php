<?php
  include('../../cekadmin.php');
  include('../../connection.php');
  include('../perhitungan/c45_engine.php');
  
  $tree = isset($_SESSION['c45_tree_json']) ? json_decode($_SESSION['c45_tree_json'], true) : null;
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
                                <li><a href="../decision_tree"><i class="ti-vector"></i><span>Pohon Keputusan</span></a></li>
                                <li class="active">
                                    <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-sidebar-left"></i><span>Kinerja</span></a>
                                    <ul class="collapse">
                                        <li><a href="../perbandingan">Tabel Perbandingan</a></li>
                                        <li class="active"><a href="../tabel_penilaian">Tabel Penilaian</a></li>
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
                                <h4 class="page-title pull-left">Tabel Penilaian (Confusion Matrix)</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-content-inner">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Evaluasi Performa Model</h4>
                                <?php if(!$tree) { echo "<div class='alert alert-warning'>Model C4.5 belum dibuat. Silahkan lakukan perhitungan terlebih dahulu.</div>"; } else { 
                                    
                                    $TP = 0; // True Positive (Asli Yes, Prediksi Yes)
                                    $TN = 0; // True Negative (Asli No, Prediksi No)
                                    $FP = 0; // False Positive (Asli No, Prediksi Yes)
                                    $FN = 0; // False Negative (Asli Yes, Prediksi No)

                                    $sql_dataset = mysqli_query($connection, "SELECT * FROM data_donor WHERE status_data='DATA TESTING'");
                                    if(mysqli_num_rows($sql_dataset) == 0) {
                                        echo "<div class='alert alert-warning'>Data Testing kosong.</div>";
                                    } else {
                                        while($data = mysqli_fetch_array($sql_dataset)) {
                                            $asli = $data['kategori'];
                                            $prediksi = C45::predictTree($tree, $data);

                                            if (isYes($asli) && isYes($prediksi)) $TP++;
                                            if (isNo($asli)  && isNo($prediksi))  $TN++;
                                            if (isNo($asli)  && isYes($prediksi)) $FP++;
                                            if (isYes($asli) && isNo($prediksi))  $FN++;
                                        }

                                        $total = $TP + $TN + $FP + $FN;
                                        $accuracy = ($total > 0) ? round((($TP + $TN) / $total) * 100, 2) : 0;
                                        $precision_yes = (($TP + $FP) > 0) ? round(($TP / ($TP + $FP)) * 100, 2) : 0;
                                        $precision_no  = (($TN + $FN) > 0) ? round(($TN / ($TN + $FN)) * 100, 2) : 0;
                                        $recall_yes = (($TP + $FN) > 0) ? round(($TP / ($TP + $FN)) * 100, 2) : 0;
                                        $recall_no  = (($TN + $FP) > 0) ? round(($TN / ($TN + $FP)) * 100, 2) : 0;
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-striped text-center table-bordered">
                                        <thead class="text-uppercase bg-light">
                                            <tr>
                                                <th scope="col" rowspan="2" class="align-middle">Kategori Aktual</th>
                                                <th scope="col" colspan="2">Prediksi C4.5</th>
                                                <th scope="col" rowspan="2" class="align-middle">Class Precision</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Prediksi Layak (Ya)</th>
                                                <th scope="col">Prediksi Tidak Layak (Tidak)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Layak (Ya)</strong></td>
                                                <td class="text-success font-weight-bold"><?php echo $TP; ?> (TP)</td>
                                                <td class="text-danger"><?php echo $FN; ?> (FN)</td>
                                                <td><?php echo $precision_yes; ?> %</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tidak Layak (Tidak)</strong></td>
                                                <td class="text-danger"><?php echo $FP; ?> (FP)</td>
                                                <td class="text-success font-weight-bold"><?php echo $TN; ?> (TN)</td>
                                                <td><?php echo $precision_no; ?> %</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Class Recall</strong></td>
                                                <td><?php echo $recall_yes; ?> %</td>
                                                <td><?php echo $recall_no; ?> %</td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 p-3 bg-light border rounded">
                                    <h4 class="mb-0 text-success">Overall Accuracy : <?php echo $accuracy; ?> %</h4>
                                </div>
                                <?php 
                                    }
                                } 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <div class="footer-area"><p>© Copyright 2026. All right reserved.</p></div>
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
