<?php
function replace_in_file($path) {
    if(!file_exists($path)) return;
    $content = file_get_contents($path);
    $new_content = str_replace('<title>Aplikasi Klasifikasi Mahasiswa Aktif</title>', '<title>Aplikasi Kelayakan Donor Darah</title>', $content);
    $new_content = str_replace('Klasifikasi Data Mining Untuk Mengetahui Mahasiswa Berpotensi Drop Out Menggunakan Algoritma Decision Tree C4.5', 'Aplikasi Penentuan Kelayakan Donor Darah Menggunakan Algoritma Decision Tree C4.5', $new_content);
    $new_content = str_replace('data_mahasiswa', 'data_donor', $new_content);
    $new_content = str_replace('Data Mahasiswa', 'Data Donor', $new_content);
    
    if($content !== $new_content) {
        file_put_contents($path, $new_content);
        echo "Updated: $path\n";
    }
}

$files = [
    'login.php',
    'index.php',
    'admin/index.php',
    'admin/data_donor/index.php',
    'admin/data_donor/add.php',
    'admin/data_donor/edit.php',
    'admin/data_donor/proses.php',
    'admin/data_donor/generate.php',
    'admin/decision_tree/index.php',
    'admin/hasil_prediksi/index.php',
    'admin/hasil_prediksi/prediksi.php',
    'admin/partisi_data/index.php',
    'admin/perbandingan/index.php',
    'admin/perhitungan/index.php',
    'admin/tabel_penilaian/index.php'
];

foreach ($files as $f) {
    replace_in_file(__DIR__ . '/' . $f);
}
echo "Done replacing text.";
?>
