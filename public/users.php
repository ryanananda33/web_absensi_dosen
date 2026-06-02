<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-clipboard-check"></i> Sistem Absensi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php"><i class="bi bi-list-check"></i> Data Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="users.php"><i class="bi bi-people"></i> Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="schedule.php"><i class="bi bi-calendar3"></i> Jadwal</a>
                    </li>
                    <li id="nav-auth-area" class="nav-item"></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-8">
                <h1 class="h3 d-inline-block">Data Pengguna</h1>
            </div>
            <div class="col-4 text-end">
                <button id="btn-add-user" class="btn btn-success"><i class="bi bi-plus-lg"></i> Tambah Pengguna</button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Filter Role</label>
                        <select id="filter-role" class="form-select">
                            <option value="">Semua Role</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dosen">Dosen</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Filter Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="users-table" class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIM/NIK</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Kelas</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-users">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-hourglass-split fs-1"></i>
                                    <p>Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detail-content">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Create/Edit Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="user-form" enctype="multipart/form-data">
                        <input type="hidden" id="user_id" name="user_id" value="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NIM / NIK</label>
                                <input type="text" class="form-control" id="nim_nik" name="nim_nik" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jurusan / Fakultas</label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Device ID</label>
                                <input type="text" class="form-control" id="device_id" name="device_id">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Akun</label>
                                <select class="form-select" id="status_akun" name="status_akun">
                                    <option value="aktif">Aktif</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Foto Selfie (opsional)</label>
                                <input type="file" class="form-control" id="foto_selfie" name="foto_selfie" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Foto Identitas / Dokumen (opsional)</label>
                                <input type="file" class="form-control" id="foto_ktm" name="foto_ktm" accept="image/*">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-save-user">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/auth.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/users.js"></script>
</body>
</html>
