<?php
  include('../../cekadmin.php');

  include('../../connection.php');

//   $message = '';
    
//   include_once 'dataset_button.php';

//   $query = "SELECT * FROM data_donor";
//   $result = mysqli_query($connection, $query);
?>

<!doctype html>
<html class="no-js" lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Aplikasi Kelayakan Donor Darah</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="../../assets/css/themify-icons.css">
        <link rel="stylesheet" href="../../assets/css/metisMenu.css">
        <link rel="stylesheet" href="../../assets/css/owl.carousel.min.css">
        <link rel="stylesheet" href="../../assets/css/slicknav.min.css">
        <!-- amchart css -->
        <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
        <!-- others css -->
        <link rel="stylesheet" href="../../assets/css/typography.css">
        <link rel="stylesheet" href="../../assets/css/default-css.css?v=2">
        <link rel="stylesheet" href="../../assets/css/styles.css?v=2">
        <link rel="stylesheet" href="../../assets/css/responsive.css">
        <!-- modernizr css -->
        <script src="../../assets/js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="../../assets/js/vendor/jquery-2.2.4.min.js"></script>
        <!-- bootstrap 4 js -->
        <script src="../../assets/js/popper.min.js"></script>
        <script src="../../assets/js/bootstrap.min.js"></script>
        <script src="../../assets/js/owl.carousel.min.js"></script>
        <script src="../../assets/js/metisMenu.min.js"></script>
        <script src="../../assets/js/jquery.slimscroll.min.js"></script>
        <script src="../../assets/js/jquery.slicknav.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
        <!-- <script>
            $(document).ready( function () {
                $('#table_id').DataTable();
            } );
        </script> -->

    </head>

    <body>
        <!--[if lt IE 8]>
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            <![endif]-->
        <!-- preloader area start -->
        <div id="preloader">
            <div class="loader"></div>
        </div>
        <!-- preloader area end -->
        <!-- page container area start -->
        <div class="page-container">
            <!-- sidebar menu area start -->
            <div class="sidebar-menu">
                <div class="sidebar-header">
                    <?php
                        $sesi = $_SESSION['username'];
                        $query="SELECT level FROM user WHERE username='$sesi'";
                        $result=mysqli_query($connection,$query);
                        $row=mysqli_fetch_assoc($result);
                        $level=$row['level'];               
                    ?>
                    <div class="logo">
                        <h3><b><?php echo $level; ?></b></h3>
                    </div>
                </div>
                <div class="main-menu">
                    <div class="menu-inner">
                        <nav>
                            <ul class="metismenu" id="menu">
                                <li><a href="../"><i class="ti-dashboard"></i><span>Dashboard</span></a></li>
                                <li class="active"><a href="../data_donor"><i class="ti-server"></i><span>Data Donor</span></a></li>
                                <li><a href="../partisi_data"><i class="ti-files"></i><span>Partisi Data</span></a></li>
                                <li><a href="../perhitungan"><i class="ti-pencil-alt"></i><span>Perhitungan</span></a></li>
                                <li><a href="../decision_tree"><i class="ti-vector"></i><span>Pohon Keputusan</span></a></li>
                                <li>
                                    <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-sidebar-left"></i><span>Kinerja</span></a>
                                    <ul class="collapse">
                                        <li><a href="../perbandingan">Tabel Perbandingan</a></li>
                                        <li><a href="../tabel_penilaian">Tabel Penilaian</a></li>
                                    </ul>
                                </li>
                                <li><a href="../hasil_prediksi"><i class="ti-announcement"></i><span>Hasil Prediksi</span></a></li>
                                <li><a href="../../logout.php"></i><span>Log out</span></a></li>                       
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- sidebar menu area end -->
            <!-- main content area start -->
            <div class="main-content">
                <!-- header area start -->
                <div class="header-area">
                    <div class="row align-items-center">
                        <!-- nav and search button -->
                        <div class="col-md-6 col-sm-8 clearfix">
                            <div class="nav-btn pull-left">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <!-- profile info & task notification -->
                        <div class="col-md-6 col-sm-4 clearfix">
                            <ul class="notification-area pull-right">
                                <li id="full-view"><i class="ti-fullscreen"></i></li>
                                <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- header area end -->
                <!-- page title area start -->
                <div class="page-title-area">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="breadcrumbs-area clearfix">
                                <h4 class="page-title pull-left">Data Donor</h4>
                            </div>
                        </div>
                        <div class="col-sm-6 clearfix">
                            <ul class="user-profile pull-right">  
                                <li class="dropdown">
                                <?php
                                    $sesi = $_SESSION['username'];
                                    $query="SELECT nama_lengkap FROM user WHERE username='$sesi'";
                                    $result=mysqli_query($connection,$query);
                                    $row=mysqli_fetch_assoc($result);
                                    $namalengkap=$row['nama_lengkap'];               
                                ?>
                                    <div class="user-profile pull-right">
                                        <img class="avatar user-thumb" src="../../assets/images/author/avatar.png?v=2" alt="avatar">
                                        <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><?php echo $namalengkap; ?></h4>
                                        <!-- <ul class="dropdown-menu">                         
                                            <li>
                                                <a href="../../logout.php">Log Out</a>
                                            </li>
                                        </ul> -->
                                        <!-- <div class="dropdown-menu">
                                            <a class="dropdown-item" href="logout.php">Log Out</a>
                                        </div> -->
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- page title area end -->
                <div class="main-content-inner">
                    <div class="row">
                        <div class="col-lg-6 col-ml-12">
                            <div class="row">
                                <!-- Textual inputs start -->
                                <div class="col-12 mt-5">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php
                                                $donor_id = $_GET['id'];
                                                $sql_dataset = mysqli_query($connection, "SELECT * FROM data_donor WHERE donor_id = '$donor_id'") or die (mysqli_error($connection));
                                                $data = mysqli_fetch_assoc($sql_dataset);
                                            ?>
                                            <div class="pull-right">
                                                <a class="btn btn-warning" href="../data_donor"><i class="ti-angle-left"></i><h8> &nbspKembali</h8></a>
                                            </div>
                                            <h4 class="header-title">Edit Data</h4>
                                            <form action="proses.php" method="post">
                                                <div class="form-group">
                                                    <label>Donor ID</label>
                                                    <input class="form-control" type="text" name="donor_id" value="<?php echo $data['donor_id']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label>Nama</label>
                                                    <input class="form-control" type="text" name="nama" value="<?php echo $data['nama']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jenis Kelamin</label>
                                                    <select name="jk" class="custom-select" required>
                                                        <option value="Male" <?php if($data['jk'] == 'Male') echo 'selected'; ?>>Male</option>
                                                        <option value="Female" <?php if($data['jk'] == 'Female') echo 'selected'; ?>>Female</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Usia</label>
                                                    <input class="form-control" type="number" name="usia" value="<?php echo $data['usia']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Golongan Darah</label>
                                                    <select name="golongan_darah" class="custom-select" required>
                                                        <option value="A+" <?php if($data['golongan_darah'] == 'A+') echo 'selected'; ?>>A+</option>
                                                        <option value="A-" <?php if($data['golongan_darah'] == 'A-') echo 'selected'; ?>>A-</option>
                                                        <option value="B+" <?php if($data['golongan_darah'] == 'B+') echo 'selected'; ?>>B+</option>
                                                        <option value="B-" <?php if($data['golongan_darah'] == 'B-') echo 'selected'; ?>>B-</option>
                                                        <option value="AB+" <?php if($data['golongan_darah'] == 'AB+') echo 'selected'; ?>>AB+</option>
                                                        <option value="AB-" <?php if($data['golongan_darah'] == 'AB-') echo 'selected'; ?>>AB-</option>
                                                        <option value="O+" <?php if($data['golongan_darah'] == 'O+') echo 'selected'; ?>>O+</option>
                                                        <option value="O-" <?php if($data['golongan_darah'] == 'O-') echo 'selected'; ?>>O-</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tanggal Donor Terakhir</label>
                                                    <input class="form-control" type="text" name="tanggal_donor_terakhir" value="<?php echo $data['tanggal_donor_terakhir']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Total Sumbangan</label>
                                                    <input class="form-control" type="number" name="total_sumbangan" value="<?php echo $data['total_sumbangan']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Riwayat Penyakit</label>
                                                    <input class="form-control" type="text" name="riwayat_penyakit" value="<?php echo $data['riwayat_penyakit']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Berat Badan</label>
                                                    <input class="form-control" type="text" name="berat_badan" value="<?php echo $data['berat_badan']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Hemoglobin</label>
                                                    <input class="form-control" type="text" name="hemoglobin" value="<?php echo $data['hemoglobin']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Kategori</label>
                                                    <select name="kategori" class="custom-select" required>
                                                        <option value="Yes" <?php if(isYes($data['kategori'])) echo 'selected'; ?>>Yes (Layak)</option>
                                                        <option value="No" <?php if(isNo($data['kategori'])) echo 'selected'; ?>>No (Tidak Layak)</option>
                                                    </select>           
                                                </div>
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <input class="form-control" type="text" name="status" value="<?php echo $data['status_data']; ?>" readonly>
                                                </div>
                                                <div class="form-group pull-right">
                                                    <input name="edit" type="submit" value="Update" class="btn btn-success">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Textual inputs end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- main content area end -->
            <!-- footer area start-->
            <footer>
                <div class="footer-area">
                    <p>© Copyright 2020. All right reserved.</p>
                </div>
            </footer>
            <!-- footer area end-->
        <!-- page container area end -->
        
        <!-- jquery latest version -->
        <script src="../../assets/js/vendor/jquery-2.2.4.min.js"></script>
        <!-- bootstrap 4 js -->
        <script src="../../assets/js/popper.min.js"></script>
        <script src="../../assets/js/bootstrap.min.js"></script>
        <script src="../../assets/js/owl.carousel.min.js"></script>
        <script src="../../assets/js/metisMenu.min.js"></script>
        <script src="../../assets/js/jquery.slimscroll.min.js"></script>
        <script src="../../assets/js/jquery.slicknav.min.js"></script>

        <!-- start chart js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
        <!-- start highcharts js -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <!-- start zingchart js -->
        <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
        <script>
            zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
            ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
        </script>
        <!-- all line chart activation -->
        <script src="../../assets/js/line-chart.js"></script>
        <!-- all pie chart -->
        <script src="../../assets/js/pie-chart.js"></script>
        <!-- others plugins -->
        <script src="../../assets/js/plugins.js"></script>
        <script src="../../assets/js/scripts.js"></script>
    </body>

</html>
