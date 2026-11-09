<?php
$connection = mysqli_connect("localhost", "root", "", "dbmining");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$queries = [
    "DROP TABLE IF EXISTS `data_keputusan_kinerja`;",
    "DROP TABLE IF EXISTS `data_keputusan`;",
    "DROP TABLE IF EXISTS `perbandingan`;",
    "DROP TABLE IF EXISTS `pohon`;",
    "DROP TABLE IF EXISTS `rule_c45`;",
    "DROP TABLE IF EXISTS `nilai_atribut`;",
    "DROP TABLE IF EXISTS `atribut`;",
    "DROP TABLE IF EXISTS `mining_c45`;",
    "DROP TABLE IF EXISTS `partisi_data`;",
    "DROP TABLE IF EXISTS `data_mahasiswa`;",
    "DROP TABLE IF EXISTS `data_donor`;",
    "DROP TABLE IF EXISTS `c45_rules`;",
    
    "CREATE TABLE `data_donor` (
        `donor_id` varchar(20) NOT NULL,
        `nama` varchar(100) NOT NULL,
        `jk` varchar(10) NOT NULL,
        `usia` int(5) NOT NULL,
        `golongan_darah` varchar(5) NOT NULL,
        `tanggal_donor_terakhir` varchar(20) NOT NULL,
        `total_sumbangan` int(5) NOT NULL,
        `riwayat_penyakit` varchar(50) NOT NULL,
        `berat_badan` decimal(5,1) NOT NULL,
        `hemoglobin` decimal(4,1) NOT NULL,
        `kategori` varchar(10) NOT NULL,
        `status_data` varchar(20) NOT NULL DEFAULT 'DATA TRAINING',
        PRIMARY KEY (`donor_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "INSERT INTO `data_donor` (`donor_id`, `nama`, `jk`, `usia`, `golongan_darah`, `tanggal_donor_terakhir`, `total_sumbangan`, `riwayat_penyakit`, `berat_badan`, `hemoglobin`, `kategori`, `status_data`) VALUES
    ('DNR000001', 'Sangeeta', 'Female', 38, 'O+', '7/10/2025', 1, 'None', 66.6, 13.6, 'Yes', 'DATA TRAINING'),
    ('DNR000002', 'Meena Iyer', 'Female', 49, 'B+', '8/11/2020', 1, 'Hypertension', 70.8, 14.0, 'No', 'DATA TRAINING'),
    ('DNR000003', 'Priya Nair', 'Female', 29, 'B+', '12/4/2025', 2, 'Diabetes', 73.4, 12.5, 'No', 'DATA TRAINING'),
    ('DNR000004', 'Ramesh', 'Male', 45, 'A+', '1/1/2026', 3, 'None', 75.0, 14.5, 'Yes', 'DATA TESTING'),
    ('DNR000005', 'Anita', 'Female', 31, 'O-', '5/5/2025', 1, 'None', 55.0, 11.0, 'No', 'DATA TESTING'),
    ('DNR000006', 'Vikram', 'Male', 52, 'AB+', '3/15/2020', 5, 'Heart Disease', 80.5, 12.1, 'No', 'DATA TESTING'),
    ('DNR000007', 'Suresh', 'Male', 25, 'O+', '10/10/2025', 1, 'None', 68.0, 15.2, 'Yes', 'DATA TRAINING'),
    ('DNR000008', 'Lakshmi', 'Female', 40, 'B-', '2/20/2026', 4, 'None', 65.0, 13.5, 'Yes', 'DATA TRAINING');",

    "CREATE TABLE `partisi_data` (
        `id_partisi` int(5) NOT NULL AUTO_INCREMENT,
        `training_yes` int(5) NOT NULL,
        `testing_yes` int(5) NOT NULL,
        `training_no` int(5) NOT NULL,
        `testing_no` int(5) NOT NULL,
        PRIMARY KEY (`id_partisi`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

    "INSERT INTO `partisi_data` (`training_yes`, `testing_yes`, `training_no`, `testing_no`) VALUES (3, 1, 2, 2);",

    "CREATE TABLE `c45_rules` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `rule_path` text NOT NULL,
        `kelas` varchar(20) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
];

foreach ($queries as $query) {
    if (mysqli_query($connection, $query)) {
        echo "Query executed successfully.\n";
    } else {
        echo "Error executing query: " . mysqli_error($connection) . "\n" . $query . "\n\n";
    }
}

mysqli_close($connection);
echo "Database Re-initialized Successfully.";
?>
