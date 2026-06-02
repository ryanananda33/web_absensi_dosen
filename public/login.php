<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="auth-wrapper">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-logo-icon">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Sistem Absensi</h3>
                        <p class="text-muted">Masuk menggunakan NIM/NIK dan password Anda</p>
                    </div>
                    <div class="auth-body">
                        <div id="message"></div>

                        <form id="login-form">
                            <div class="mb-3">
                                <label for="nim_nik" class="form-label">NIM / NIK</label>
                                <input type="text" class="form-control" id="nim_nik" name="nim_nik" placeholder="Masukkan NIM atau NIK" required autocomplete="username">
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label mb-0">Password</label>
                                </div>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3 py-2.5">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-1 text-muted">Belum punya akun Dosen?</p>
                            <a href="register_dosen.php" class="btn btn-sm btn-outline-primary px-4">Daftar Akun Dosen</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/auth.js"></script>
    <script>
        $(document).ready(function() {
            const user = getAuthUser();
            if (user) {
                window.location.href = 'index.php';
                return;
            }

            $('#login-form').on('submit', function(event) {
                event.preventDefault();
                $('#message').html('');

                const nimNik = $('#nim_nik').val().trim();
                const password = $('#password').val().trim();

                if (!nimNik || !password) {
                    $('#message').html('<div class="alert alert-warning">Semua field harus diisi.</div>');
                    return;
                }

                const body = new URLSearchParams();
                body.append('action', 'login');
                body.append('nim_nik', nimNik);
                body.append('password', password);

                fetch('../api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString()
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        saveAuthUser(data.data);
                        $('#message').html('<div class="alert alert-success py-2.5">Login berhasil! Mengarahkan...</div>');
                        setTimeout(() => window.location.href = 'index.php', 800);
                    } else {
                        $('#message').html(`<div class="alert alert-danger py-2.5">${data.message}</div>`);
                    }
                })
                .catch(() => {
                    $('#message').html('<div class="alert alert-danger py-2.5">Terjadi kesalahan jaringan.</div>');
                });
            });
        });
    </script>
</body>
</html>
