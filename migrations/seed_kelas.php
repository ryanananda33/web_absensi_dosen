<?php
// migrations/seed_kelas.php
// Fill kelas/jurusan/angkatan in jadwal based on mapping. Edit $mapping to adjust.
require_once __DIR__ . '/../koneksi.php';

$mapping = [
    // match by partial matakuliah name => set kelas/jurusan/angkatan
    ['match' => 'Pemrograman Web', 'kelas' => 'TINFC-2024-02', 'jurusan' => 'Teknik Informatika', 'angkatan' => '2024'],
    ['match' => 'Basis Data', 'kelas' => 'TINFC-2024-01', 'jurusan' => 'Teknik Informatika', 'angkatan' => '2024'],
    ['match' => 'Mobile App', 'kelas' => 'TINFC-2024-03', 'jurusan' => 'Teknik Informatika', 'angkatan' => '2024'],
    ['match' => 'Desain Grafis', 'kelas' => 'DKV-2024-01', 'jurusan' => 'Desain Komunikasi Visual', 'angkatan' => '2024'],
];

$results = [];
foreach ($mapping as $m) {
    $like = '%' . $m['match'] . '%';
    $sql = "UPDATE jadwal SET kelas = ?, jurusan = ?, angkatan = ? WHERE matakuliah LIKE ?";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        $results[] = ['match' => $m['match'], 'status' => 'error', 'message' => mysqli_error($con)];
        continue;
    }
    mysqli_stmt_bind_param($stmt, 'ssss', $m['kelas'], $m['jurusan'], $m['angkatan'], $like);
    if (mysqli_stmt_execute($stmt)) {
        $aff = mysqli_stmt_affected_rows($stmt);
        $results[] = ['match' => $m['match'], 'status' => 'updated', 'rows' => $aff];
    } else {
        $results[] = ['match' => $m['match'], 'status' => 'error', 'message' => mysqli_error($con)];
    }
    mysqli_stmt_close($stmt);
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'results' => $results], JSON_PRETTY_PRINT);
