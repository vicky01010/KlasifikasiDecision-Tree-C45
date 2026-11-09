<?php
    include('../../connection.php');
    session_start();

    if(isset($_POST['add'])) {
        
        $total = $_POST['total'];
        for ($i=1; $i<=$total; $i++) {
            $donor_id = $_POST['donor_id'.$i];
			$nama = $_POST['nama'.$i];
			$jk = $_POST['jk'.$i];
			$usia = $_POST['usia'.$i];
            $golongan_darah = $_POST['golongan_darah'.$i];
            $tanggal_donor_terakhir = $_POST['tanggal_donor_terakhir'.$i];
            $total_sumbangan = $_POST['total_sumbangan'.$i];
            $riwayat_penyakit = $_POST['riwayat_penyakit'.$i];
            $berat_badan = $_POST['berat_badan'.$i];
            $hemoglobin = $_POST['hemoglobin'.$i];
            $kategori = $_POST['kategori'.$i];
            $status = $_POST['status'.$i];
            
            $sql = mysqli_query($connection, "INSERT INTO data_donor VALUES ('$donor_id', '$nama', '$jk', '$usia', '$golongan_darah', '$tanggal_donor_terakhir', '$total_sumbangan', '$riwayat_penyakit', '$berat_badan', '$hemoglobin', '$kategori', '$status')") or die (mysqli_error($connection));
        }

        if($sql) {
            $_SESSION['message'] = "<b>$total Data Berhasil di Tambahkan</b>";
            $_SESSION['msg_type'] = "success";

            header("location: index.php");
        } else {
            echo "<script>alert('Gagal Tambah Data, Coba Lagi!'); window.location='generate.php';</script>";
        }
    } else if(isset($_POST['edit'])) {
        $donor_id = $_POST['donor_id'];
        $nama = $_POST['nama'];
        $jk = $_POST['jk'];
        $usia = $_POST['usia'];
        $golongan_darah = $_POST['golongan_darah'];
        $tanggal_donor_terakhir = $_POST['tanggal_donor_terakhir'];
        $total_sumbangan = $_POST['total_sumbangan'];
        $riwayat_penyakit = $_POST['riwayat_penyakit'];
        $berat_badan = $_POST['berat_badan'];
        $hemoglobin = $_POST['hemoglobin'];
        $kategori = $_POST['kategori'];
        $status = $_POST['status'];

        $db = mysqli_query($connection, "UPDATE data_donor SET nama='$nama', jk='$jk', usia='$usia', golongan_darah='$golongan_darah', tanggal_donor_terakhir='$tanggal_donor_terakhir', total_sumbangan='$total_sumbangan', riwayat_penyakit='$riwayat_penyakit', berat_badan='$berat_badan', hemoglobin='$hemoglobin', kategori='$kategori', status_data='$status' WHERE donor_id='$donor_id'") or die (mysqli_error($connection));
      
        if($db) {
            $_SESSION['message'] = "<b>Data Berhasil di Update</b>";
            $_SESSION['msg_type'] = "success";

            header("location: index.php");
        } else {
            echo "<script>alert('Gagal Update Data, Coba Lagi!'); window.location='edit.php?id=$donor_id';</script>";
        }
    } else if(isset($_POST['hapus_semua'])) {
        $hapus = mysqli_query($connection, "DELETE FROM data_donor") or die (mysqli_error($connection));
        if($hapus) {
            $_SESSION['message'] = "<b>Semua Data Berhasil di Hapus</b>";
            $_SESSION['msg_type'] = "success";
            header("location: index.php");
        } else {
            echo "<script>alert('Gagal Hapus Semua Data, Coba Lagi!'); window.location='index.php';</script>";
        }
    } else if(isset($_POST['hapus'])) {
        if(empty($_POST['checked_id'])) {
            echo "<script>alert('Tidak Ada Data Yang Dipilih!'); window.location='index.php';</script>";
        } else {
            $donor_id = $_POST['checked_id'];
            $jml_check = count($donor_id);

            for($x=0; $x < $jml_check; $x++) {
                $hapus = mysqli_query($connection, "DELETE FROM data_donor WHERE donor_id='$donor_id[$x]'") or die (mysqli_error($connection));
            }
            if($hapus) {
                $_SESSION['message'] = "<b>$jml_check Data Berhasil di Hapus</b>";
                $_SESSION['msg_type'] = "success";

                header("location: index.php");
            } else {
                echo "<script>alert('Gagal Hapus Data, Coba Lagi!'); window.location='index.php';</script>";
            }
        }
    } else if(isset($_POST['import'])) {
        $handle = fopen($_FILES['file']['tmp_name'], "r");
        $first = true;
        while($data = fgetcsv($handle)){
            if($first) { $first=false; continue; } // skip header
            if(!isset($data[0])) continue;
            
            $donor_id = mysqli_real_escape_string($connection, $data[0]);
            $nama = mysqli_real_escape_string($connection, $data[1]);
            $jk = mysqli_real_escape_string($connection, $data[2]);
            $usia = mysqli_real_escape_string($connection, $data[3]);
            $gol_darah = mysqli_real_escape_string($connection, $data[4]);
            $tgl = mysqli_real_escape_string($connection, $data[5]);
            $total_sumb = (int) $data[6];
            $penyakit = mysqli_real_escape_string($connection, $data[7]);
            $berat = (float) $data[8];
            $hb = (float) $data[9];
            $kategori = mysqli_real_escape_string($connection, $data[10]);

            $sql = "INSERT IGNORE INTO data_donor (donor_id, nama, jk, usia, golongan_darah, tanggal_donor_terakhir, total_sumbangan, riwayat_penyakit, berat_badan, hemoglobin, kategori, status_data)
                        VALUES ('$donor_id', '$nama', '$jk', '$usia', '$gol_darah', '$tgl', '$total_sumb', '$penyakit', '$berat', '$hb', '$kategori', 'DATA TRAINING')";
            mysqli_query($connection, $sql);
        }
        header("location: index.php");
    } else if(isset($_POST['export'])) {
        header('Content-Type: text/csv; charset=utf-8'); 
        header('Content-Disposition: attachment; filename=data_donor.csv'); 
        $output = fopen("php://output", "w"); 
        fputcsv($output, array('Donor_ID', 'Nama', 'JK', 'usia', 'golongan_darah', 'tanggal_donor_terakhir', 'Total_sumbangan', 'riwayat_penyakit', 'berat_badan', 'Hemoglobin', 'Kategori')); 
        $query = "SELECT donor_id, nama, jk, usia, golongan_darah, tanggal_donor_terakhir, total_sumbangan, riwayat_penyakit, berat_badan, hemoglobin, kategori FROM data_donor ORDER BY donor_id ASC"; 
        $result = mysqli_query($connection, $query); 
        while ($row = mysqli_fetch_assoc($result)) { 
            fputcsv($output, $row); 
        } 
        fclose($output); 
    }
?>
