<?php
// migrations/run_migrations.php
// Run from browser or CLI to apply migrations safely.
require_once __DIR__ . '/../koneksi.php';

$results = [];
$table = 'jadwal';
$columns = [
    'kelas' => "VARCHAR(50) DEFAULT NULL",
    'jurusan' => "VARCHAR(100) DEFAULT NULL",
    'angkatan' => "VARCHAR(10) DEFAULT NULL",
];

foreach ($columns as $col => $def) {
    $q = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$col'");
    if (!$q) {
        $results[] = ["column" => $col, "status" => "error", "message" => mysqli_error($con)];
        continue;
    }

    if (mysqli_num_rows($q) === 0) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$col` $def AFTER `matakuliah`";
        if (mysqli_query($con, $sql)) {
            $results[] = ["column" => $col, "status" => "added"];
        } else {
            $results[] = ["column" => $col, "status" => "error", "message" => mysqli_error($con)];
        }
    } else {
        $results[] = ["column" => $col, "status" => "exists"];
    }
}

// Optionally update sample rows or schema dump here

header('Content-Type: application/json');
echo json_encode(["success" => true, "results" => $results], JSON_PRETTY_PRINT);
