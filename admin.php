<?php 
require_once('koneksi.php'); 
if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($con, "DELETE FROM absensi WHERE id = '$id'");
    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Dashboard Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3>Monitor Kehadiran Mahasiswa (Real-time)</h3>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Matakuliah</th>
                            <th>Status</th>
                            <th>Foto Selfie</th>
                            <th>Lokasi</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT absensi.*, users.nama FROM absensi JOIN users ON absensi.user_id = users.id ORDER BY waktu_absen DESC";
                        $res = mysqli_query($con, $query);
                        while($row = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['matakuliah'] ?></td>
                            <td><span class="badge bg-success"><?= $row['keterangan'] ?></span></td>
                            <!-- Master Level: Path Gambar harus benar -->
                            <td><img src="uploads/selfie/<?= $row['foto'] ?>" width="80" class="rounded"></td>
                            <td><a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn btn-sm btn-info text-white">Lihat Peta</a></td>
                            <td><?= $row['waktu_absen'] ?></td>
                            <td><a href="admin.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>