<?php
  include('../../cekadmin.php');
  include('../../connection.php');

  function predictTree($tree, $input) {
      if (!$tree) return "Belum Ada Model";
      if ($tree['type'] == 'leaf') {
          return $tree['class'];
      }
      
      $attr = $tree['attribute'];
      $val = isset($input[$attr]) ? $input[$attr] : null;

      if ($tree['split_type'] == 'continuous') {
          $threshold = $tree['threshold'];
          if ($val <= $threshold) {
              return predictTree($tree['children']['<=' . $threshold], $input);
          } else {
              return predictTree($tree['children']['>' . $threshold], $input);
          }
      } else {
          // Categorical
          if (isset($tree['children'][$val])) {
              return predictTree($tree['children'][$val], $input);
          } else {
              // Handle unknown categorical value
              return "Unknown (Nilai '$val' tidak ada dalam data latih pada atribut '$attr')";
          }
      }
  }

  $input = [
      'donor_id' => $_POST['donor_id'],
      'nama' => $_POST['nama'],
      'jk' => $_POST['jk'],
      'usia' => $_POST['usia'],
      'golongan_darah' => $_POST['golongan_darah'],
      'tanggal_donor_terakhir' => $_POST['tanggal_donor_terakhir'],
      'total_sumbangan' => $_POST['total_sumbangan'],
      'riwayat_penyakit' => $_POST['riwayat_penyakit'],
      'berat_badan' => $_POST['berat_badan'],
      'hemoglobin' => $_POST['hemoglobin'],
  ];

  $prediction = "Model C4.5 belum dibuat. Silakan menuju ke menu Perhitungan terlebih dahulu.";
  if (isset($_SESSION['c45_tree_json'])) {
      $tree = json_decode($_SESSION['c45_tree_json'], true);
      $prediction = predictTree($tree, $input);
  }
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
                    <?php
                        $sesi = $_SESSION['username'];
                        $query="SELECT level FROM user WHERE username='$sesi'";
                        $result=mysqli_query($connection,$query);
                        $row=mysqli_fetch_assoc($result);
                    ?>
                    <div class="logo">
                        <h3><b><?php echo $row['level']; ?></b></h3>
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
                                <li>
                                    <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout-sidebar-left"></i><span>Kinerja</span></a>
                                    <ul class="collapse">
                                        <li><a href="../perbandingan">Tabel Perbandingan</a></li>
                                        <li><a href="../tabel_penilaian">Tabel Penilaian</a></li>
                                    </ul>
                                </li>
                                <li class="active"><a href="../hasil_prediksi"><i class="ti-announcement"></i><span>Hasil Prediksi</span></a></li>
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
                                <h4 class="page-title pull-left">Hasil Prediksi Baru</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-content-inner">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr><th width="30%">Donor ID</th><td><?php echo htmlspecialchars($input['donor_id']); ?></td></tr>
                                        <tr><th>Nama</th><td><?php echo htmlspecialchars($input['nama']); ?></td></tr>
                                        <tr><th>Jenis Kelamin</th><td><?php echo htmlspecialchars($input['jk']); ?></td></tr>
                                        <tr><th>Usia</th><td><?php echo htmlspecialchars($input['usia']); ?></td></tr>
                                        <tr><th>Golongan Darah</th><td><?php echo htmlspecialchars($input['golongan_darah']); ?></td></tr>
                                        <tr><th>Total Sumbangan</th><td><?php echo htmlspecialchars($input['total_sumbangan']); ?></td></tr>
                                        <tr><th>Riwayat Penyakit</th><td><?php echo htmlspecialchars($input['riwayat_penyakit']); ?></td></tr>
                                        <tr><th>Berat Badan</th><td><?php echo htmlspecialchars($input['berat_badan']); ?> kg</td></tr>
                                        <tr><th>Hemoglobin</th><td><?php echo htmlspecialchars($input['hemoglobin']); ?> g/dL</td></tr>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <h4><strong>Hasil Prediksi Kelayakan :</strong></h4>
                                    <?php
                                        if (isYes($prediction)) {
                                            echo "<h3><span class='badge badge-success'>Layak (Ya)</span></h3>";
                                        } elseif (isNo($prediction)) {
                                            echo "<h3><span class='badge badge-danger'>Tidak Layak (Tidak)</span></h3>";
                                        } else {
                                            echo "<h3><span class='badge badge-warning'>" . htmlspecialchars($prediction) . "</span></h3>";
                                        }
                                    ?>
                                </div>
                                <div class="mt-4">
                                    <a href="../hasil_prediksi" class="btn btn-warning"><i class="ti-angle-left"></i> Kembali</a>
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
