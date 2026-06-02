<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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
                        <a class="nav-link" href="users.php"><i class="bi bi-people"></i> Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="schedule.php"><i class="bi bi-calendar3"></i> Jadwal</a>
                    </li>
                    <li id="nav-auth-area" class="nav-item"></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1 class="h3">Jadwal Perkuliahan</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                    <i class="bi bi-plus"></i> Tambah Jadwal
                </button>
            </div>
        </div>

        <!-- Schedule by Day -->
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="senin-tab" data-bs-toggle="tab" data-bs-target="#senin" type="button" role="tab">Senin</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="selasa-tab" data-bs-toggle="tab" data-bs-target="#selasa" type="button" role="tab">Selasa</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rabu-tab" data-bs-toggle="tab" data-bs-target="#rabu" type="button" role="tab">Rabu</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="kamis-tab" data-bs-toggle="tab" data-bs-target="#kamis" type="button" role="tab">Kamis</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="jumat-tab" data-bs-toggle="tab" data-bs-target="#jumat" type="button" role="tab">Jumat</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sabtu-tab" data-bs-toggle="tab" data-bs-target="#sabtu" type="button" role="tab">Sabtu</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="senin" role="tabpanel">
                        <div id="schedule-senin" class="schedule-container"></div>
                    </div>
                    <div class="tab-pane fade" id="selasa" role="tabpanel">
                        <div id="schedule-selasa" class="schedule-container"></div>
                    </div>
                    <div class="tab-pane fade" id="rabu" role="tabpanel">
                        <div id="schedule-rabu" class="schedule-container"></div>
                    </div>
                    <div class="tab-pane fade" id="kamis" role="tabpanel">
                        <div id="schedule-kamis" class="schedule-container"></div>
                    </div>
                    <div class="tab-pane fade" id="jumat" role="tabpanel">
                        <div id="schedule-jumat" class="schedule-container"></div>
                    </div>
                    <div class="tab-pane fade" id="sabtu" role="tabpanel">
                        <div id="schedule-sabtu" class="schedule-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="schedule-form">
                        <div class="mb-3">
                            <label class="form-label">Matakuliah</label>
                            <input type="text" class="form-control" name="matakuliah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control" name="kelas" placeholder="Contoh: A, B, TI-2A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" class="form-control" name="jurusan" placeholder="Contoh: Teknik Informatika">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Angkatan</label>
                            <input type="text" class="form-control" name="angkatan" placeholder="Contoh: 2024">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select class="form-select" name="hari" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" name="jam_mulai" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ruangan</label>
                            <input type="text" class="form-control" name="ruangan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-select" name="tipe" required>
                                <option value="Teori">Teori</option>
                                <option value="Praktikum">Praktikum</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btn-save-schedule">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/schedule.js"></script>
</body>
</html>
