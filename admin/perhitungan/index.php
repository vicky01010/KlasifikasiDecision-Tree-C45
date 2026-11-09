<?php
  include('../../cekadmin.php');
  include('../../connection.php');
  include('c45_engine.php');
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
                                <li class="active"><a href="../perhitungan"><i class="ti-pencil-alt"></i><span>Perhitungan</span></a></li>
                                <li><a href="../decision_tree"><i class="ti-vector"></i><span>Pohon Keputusan</span></a></li>
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
                                <h4 class="page-title pull-left">Perhitungan Model C4.5</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-content-inner">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Eksekusi Algoritma C4.5</h4>
                                <p class="text-muted font-14 mb-4">Sistem akan membaca seluruh `Data Training` dan melakukan algoritma C4.5 secara otomatis untuk mencari split point terbaik pada data kontinyu serta membuat pohon keputusan.</p>
                                <?php
                                if(isset($_POST['hitung'])) {
                                    $sql = mysqli_query($connection, "SELECT * FROM data_donor WHERE status_data='DATA TRAINING'");
                                    if(mysqli_num_rows($sql) == 0) {
                                        echo "<div class='alert alert-danger'>Data Training kosong. Silakan import atau tambah data terlebih dahulu.</div>";
                                    } else {
                                        $data = [];
                                        while($row = mysqli_fetch_assoc($sql)) {
                                            $data[] = $row;
                                        }

                                        // Hitung distribusi asli untuk ditampilkan
                                        $original_counts = ['Ya' => 0, 'Tidak' => 0];
                                        foreach($data as $row) {
                                            $kat = isYes($row['kategori']) ? 'Ya' : 'Tidak';
                                            $original_counts[$kat]++;
                                        }

                                        $smote_applied = false;
                                        $final_counts = $original_counts;

                                        // Terapkan SMOTE jika dicentang oleh user
                                        if (isset($_POST['use_smote']) && $_POST['use_smote'] == '1') {
                                            include('smote.php');
                                            $smote = new SMOTE(
                                                'kategori',
                                                ['usia', 'total_sumbangan', 'berat_badan', 'hemoglobin'],
                                                ['jk', 'golongan_darah', 'riwayat_penyakit'],
                                                5
                                            );
                                            $data = $smote->balance($data);
                                            $smote_applied = true;

                                            // Hitung distribusi setelah SMOTE
                                            $final_counts = ['Ya' => 0, 'Tidak' => 0];
                                            foreach($data as $row) {
                                                $kat = isYes($row['kategori']) ? 'Ya' : 'Tidak';
                                                $final_counts[$kat]++;
                                            }
                                        }

                                        $c45 = new C45(
                                            $connection, 
                                            'kategori', 
                                            ['usia', 'total_sumbangan', 'berat_badan', 'hemoglobin'], 
                                            ['jk', 'golongan_darah', 'riwayat_penyakit']
                                        );

                                        $attributes = ['usia', 'total_sumbangan', 'berat_badan', 'hemoglobin', 'jk', 'golongan_darah', 'riwayat_penyakit'];
                                        $tree = $c45->buildTree($data, $attributes);
                                        $c45->generateRules($tree);
                                        $c45->saveRulesToDB();

                                        echo "<div class='alert alert-success'><strong>Sukses!</strong> Perhitungan C4.5 dinamis telah selesai dieksekusi. " . count($c45->rules) . " rules berhasil di-generate.</div>";
                                        
                                        // Tampilkan tabel komparasi kelas latih
                                        ?>
                                        <div class="row mt-4 mb-4">
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-primary"><i class="ti-pie-chart"></i> Distribusi Latih Sebelum SMOTE</h5>
                                                        <ul class="list-group list-group-flush">
                                                             <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                                                 Layak (Ya / Yes)
                                                                 <span class="badge badge-primary badge-pill"><?php echo $original_counts['Ya']; ?></span>
                                                             </li>
                                                             <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                                                 Tidak Layak (Tidak / No)
                                                                 <span class="badge badge-danger badge-pill"><?php echo $original_counts['Tidak']; ?></span>
                                                             </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-success"><i class="ti-pie-chart"></i> Distribusi Latih Setelah SMOTE</h5>
                                                        <ul class="list-group list-group-flush">
                                                             <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                                                 Layak (Ya / Yes)
                                                                 <span class="badge badge-primary badge-pill"><?php echo $final_counts['Ya']; ?></span>
                                                             </li>
                                                             <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                                                 Tidak Layak (Tidak / No)
                                                                 <span class="badge badge-danger badge-pill"><?php echo $final_counts['Tidak']; ?></span>
                                                             </li>
                                                        </ul>
                                                        <?php if ($smote_applied): ?>
                                                            <div class="mt-2 text-success font-weight-bold text-center">
                                                                <i class="ti-check-box"></i> SMOTE Berhasil Menyeimbangkan Dataset!
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="mt-2 text-muted text-center">
                                                                SMOTE Tidak Digunakan (Dataset Asli)
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        
                                        echo "<a href='../decision_tree' class='btn btn-primary mt-3'><i class='ti-vector'></i> Lihat Pohon Keputusan</a>";
                                        
                                        // Save Tree object to session for visualization
                                        $_SESSION['c45_tree_json'] = json_encode($tree);
                                    }
                                } else {
                                ?>
                                <form action="" method="post">
                                    <div class="custom-control custom-checkbox mb-4 p-3 border rounded bg-light" style="border-left: 4px solid #007bff !important;">
                                        <input type="checkbox" class="custom-control-input" id="use_smote" name="use_smote" value="1" checked>
                                        <label class="custom-control-label font-weight-bold" for="use_smote" style="padding-left: 5px; cursor: pointer;">
                                            Terapkan Metode SMOTE (Synthetic Minority Over-sampling Technique)
                                            <br><span class="text-muted font-weight-normal"><small>Opsi ini akan menyeimbangkan jumlah data latih kelas minoritas secara sintetis (in-memory) untuk menghasilkan pohon keputusan yang tidak bias.</small></span>
                                        </label>
                                    </div>
                                    <button type="submit" name="hitung" class="btn btn-success btn-lg btn-block">Mulai Perhitungan C4.5</button>
                                </form>
                                <?php } ?>
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
