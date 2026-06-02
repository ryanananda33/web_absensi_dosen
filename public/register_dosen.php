<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Dosen - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="auth-wrapper">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-11 col-md-9 col-lg-8">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-logo-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h3 class="fw-bold mb-1">Daftar Akun Dosen</h3>
                        <p class="text-muted">Buat akun Dosen baru untuk mengelola dan memantau absensi</p>
                    </div>
                    <div class="auth-body">
                        <div id="message"></div>

                        <form id="register-form" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nim_nik" class="form-label">NIP / NIK</label>
                                    <input type="text" class="form-control" id="nim_nik" name="nim_nik" placeholder="Masukkan NIP atau NIK" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama Lengkap beserta Gelar" required autocomplete="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="jurusan" class="form-label">Jurusan / Fakultas</label>
                                    <input type="text" class="form-control" id="jurusan" name="jurusan" placeholder="Masukkan Jurusan atau Fakultas" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Contoh: Kuningan" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required autocomplete="off">
                                </div>
                                <div class="col-md-12">
                                    <label for="device_id" class="form-label">Device ID</label>
                                    <input type="text" class="form-control" id="device_id" name="device_id" placeholder="Masukkan Device ID Perangkat Anda" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label for="foto_selfie" class="form-label">Foto Selfie (Opsional)</label>
                                    <input type="file" class="form-control" id="foto_selfie" name="foto_selfie" accept="image/*">
                                </div>
                                <div class="col-md-6">
                                    <label for="foto_ktm" class="form-label">Foto Identitas / Surat Tugas (Opsional)</label>
                                    <input type="file" class="form-control" id="foto_ktm" name="foto_ktm" accept="image/*">
                                </div>
                            </div>

                            <div class="mt-4 d-grid gap-2">
                                <button type="submit" class="btn btn-success py-2.5">
                                    <i class="bi bi-person-check-fill"></i> Daftarkan Akun
                                </button>
                                <a href="login.php" class="btn btn-outline-secondary py-2.5">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                                </a>
                            </div>
                        </form>
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

            $('#register-form').on('submit', function(event) {
                event.preventDefault();
                $('#message').html('');

                const formData = new FormData(this);
                formData.append('role', 'dosen');
                formData.append('doc_type', 'Dosen');

                fetch('../api/register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        $('#message').html(`<div class="alert alert-success py-2.5">Registrasi Berhasil! Password default Anda: <strong>${data.data.default_password}</strong>. Mengarahkan...</div>`);
                        setTimeout(() => window.location.href = 'login.php', 3500);
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
