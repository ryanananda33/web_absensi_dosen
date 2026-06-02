<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-  expand-lg navbar-dark bg-primary shadow-sm">
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
                        <a class="nav-link" href="schedule.php"><i class="bi bi-calendar3"></i> Jadwal</a>
                    </li>
                    <li id="nav-auth-area" class="nav-item"></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Dashboard -->
        <div id="dashboard-content">
            <div class="container-fluid py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 d-inline-block">Dashboard</h1>
                        <p class="text-muted d-inline-block ms-2">Selamat datang di Sistem Absensi</p>
                        <div id="dashboard-user-info" class="mt-3"></div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4" id="stats-container">
                    <div class="col-md-3 col-sm-6">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="card-text text-white-50">Total Mahasiswa</p>
                                        <h4 class="card-title" id="total-students">0</h4>
                                    </div>
                                    <i class="bi bi-people fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="card-text text-white-50">Hadir Hari Ini</p>
                                        <h4 class="card-title" id="present-today">0</h4>
                                    </div>
                                    <i class="bi bi-check-circle fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="card stats-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="card-text text-white-50">Izin/Sakit</p>
                                        <h4 class="card-title" id="permission-today">0</h4>
                                    </div>
                                    <i class="bi bi-exclamation-circle fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="card stats-card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="card-text text-white-50">Alfa</p>
                                        <h4 class="card-title" id="absent-today">0</h4>
                                    </div>
                                    <i class="bi bi-x-circle fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-light border-bottom">
                                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Absensi Terbaru</h5>
                            </div>
                            <div class="card-body p-0">
                                <div id="recent-attendance-table">
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-hourglass-split fs-1"></i>
                                        <p>Memuat data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Schedule -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-light border-bottom">
                                <h5 class="card-title mb-0"><i class="bi bi-calendar-check"></i> Jadwal Hari Ini</h5>
                            </div>
                            <div class="card-body p-0">
                                <div id="today-schedule">
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-hourglass-split fs-1"></i>
                                        <p>Memuat jadwal...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-5 border-top">
        <div class="container-fluid text-center text-muted">
            <p class="mb-0">&copy; 2024 Sistem Absensi. Developed with <i class="bi bi-heart-fill text-danger"></i></p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
