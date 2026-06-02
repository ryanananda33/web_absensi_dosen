# Sistem Absensi - REST API Documentation

## Base URL
```
http://localhost/absensi_api/api
```

---

## 1. Authentication Endpoints

### 1.1 Login
**Endpoint:** `POST /auth.php`

**Request Parameters:**
```json
{
  "action": "login",
  "nim_nik": "20240810002",
  "password": "password123"
}
```

**Response Success:**
```json
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "id": 1,
    "nim_nik": "20240810002",
    "nama": "Nabil",
    "gender": "Laki-laki",
    "jurusan": "Teknik Informatika",
    "kelas": "A",
    "angkatan": "2024",
    "semester": "1",
    "role": "mahasiswa",
    "device_id": "device123"
  }
}
```

**Response Error:**
```json
{
  "status": "error",
  "message": "Password salah"
}
```

**HTTP Status Codes:**
- `200` - Login berhasil
- `401` - NIM tidak terdaftar atau password salah
- `403` - Akun pending verifikasi

---

### 1.2 Get User Profile
**Endpoint:** `GET /auth.php`

**Request Parameters:**
```
?action=profile&user_id=1
```

**Response Success:**
```json
{
  "status": "success",
  "message": "Data user berhasil diambil",
  "data": {
    "id": 1,
    "nim_nik": "20240810002",
    "nama": "Nabil",
    "gender": "Laki-laki",
    "jurusan": "Teknik Informatika",
    "kelas": "A",
    "angkatan": "2024",
    "semester": "1",
    "role": "mahasiswa",
    "status_akun": "aktif"
  }
}
```

---

## 2. Registration Endpoints

### 2.1 Register New User
**Endpoint:** `POST /register.php`

**Request Parameters:**
```
Content-Type: multipart/form-data

Parameters:
- nim_nik (string, required): NIM/NIK mahasiswa
- nama (string, required): Nama lengkap
- gender (string): Jenis kelamin
- jurusan (string): Jurusan
- kelas (string): Kelas
- angkatan (string): Angkatan
- semester (string): Semester
- tempat_lahir (string, required): Tempat lahir
- tanggal_lahir (date, required): Tanggal lahir (YYYY-MM-DD)
- device_id (string, required): ID perangkat
- doc_type (string): Tipe dokumen (KTM, KRS, SK) atau 'Dosen' untuk dosen/admin
- foto_ktm (file): Foto KTM
- foto_selfie (file): Foto selfie untuk verifikasi
- role (string): Peran user, 'mahasiswa' atau 'dosen' (default: mahasiswa)
```

**Response Success:**
```json
{
  "status": "success",
  "message": "Terdaftar! Password Anda: Kuningan2003-10-03",
  "data": {
    "user_id": 2,
    "default_password": "Kuningan2003-10-03",
    "status": "aktif"
  }
}
```

**Note:** Password otomatis di-generate dari: `tempat_lahir + tanggal_lahir` (tanpa spasi)

---

### 2.2 Get All Users (Admin Only)
**Endpoint:** `GET /register.php`

**Request Parameters:**
```
?action=list
?action=list&role=mahasiswa
```

**Response:**
```json
{
  "status": "success",
  "message": "Daftar user berhasil diambil",
  "data": [
    {
      "id": 1,
      "nim_nik": "20240810002",
      "nama": "Nabil",
      "jurusan": "Teknik Informatika",
      "kelas": "A",
      "role": "mahasiswa",
      "status_akun": "aktif"
    }
  ]
}
```

---

## 3. Attendance Endpoints

### 3.1 Create Attendance
**Endpoint:** `POST /attendance.php`

**Request Parameters:**
```
Content-Type: multipart/form-data

Parameters:
- user_id (integer, required): ID pengguna
- matakuliah (string, required): Nama matakuliah
- keterangan (string, required): Status (Hadir, Izin, Sakit, Alfa, Terlambat)
- latitude (float, required): Latitude lokasi
- longitude (float, required): Longitude lokasi
- device_id (string, required): ID perangkat
- foto (file): Foto selfie saat absensi
```

**Response Success:**
```json
{
  "status": "success",
  "message": "Absensi berhasil",
  "data": {
    "attendance_id": 123,
    "timestamp": "2024-05-22 14:30:45"
  }
}
```

**Response Error:**
```json
{
  "status": "error",
  "message": "Anda sudah absen untuk matakuliah ini hari ini"
}
```

**HTTP Status Codes:**
- `201` - Absensi berhasil dibuat
- `400` - Data tidak lengkap atau file upload gagal
- `403` - Perangkat tidak sesuai dengan akun
- `409` - Sudah absen untuk matakuliah ini hari ini

---

### 3.2 Get Attendance History
**Endpoint:** `GET /attendance.php`

**Request Parameters:**
```
?action=history&user_id=1&limit=50&offset=0
```

**Response:**
```json
{
  "status": "success",
  "message": "Riwayat absensi berhasil diambil",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "matakuliah": "Pemrograman Web",
      "keterangan": "Hadir",
      "latitude": "-6.123456",
      "longitude": "106.654321",
      "foto": "ABSEN_1_1234567890.jpg",
      "device_id": "device123",
      "waktu_absen": "2024-05-22 08:30:00",
      "tanggal": "2024-05-22"
    }
  ]
}
```

---

### 3.3 Get Attendance Summary
**Endpoint:** `GET /attendance.php`

**Request Parameters:**
```
?action=summary&user_id=1
```

**Response:**
```json
{
  "status": "success",
  "message": "Ringkasan absensi berhasil diambil",
  "data": [
    {
      "matakuliah": "Pemrograman Web",
      "total": 10,
      "hadir": 8,
      "izin": 1,
      "sakit": 1,
      "alfa": 0,
      "terlambat": 0
    }
  ]
}
```

---

### 3.4 Get All Attendance (Admin Only)
**Endpoint:** `GET /attendance.php`

**Request Parameters:**
```
?action=all&limit=100&offset=0
```

**Response:**
```json
{
  "status": "success",
  "message": "Semua data absensi berhasil diambil",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "nama": "Nabil",
      "nim_nik": "20240810002",
      "matakuliah": "Pemrograman Web",
      "keterangan": "Hadir",
      "latitude": "-6.123456",
      "longitude": "106.654321",
      "foto": "ABSEN_1_1234567890.jpg",
      "waktu_absen": "2024-05-22 08:30:00"
    }
  ]
}
```

---

### 3.5 Delete Attendance (Admin Only)
**Endpoint:** `DELETE /attendance.php`

**Request Parameters:**
```
?id=1
```

**Response:**
```json
{
  "status": "success",
  "message": "Data absensi berhasil dihapus"
}
```

---

## 4. Schedule Endpoints

### 4.1 Get All Schedules
**Endpoint:** `GET /schedule.php`

**Request Parameters:**
```
?action=all
```

**Response:**
```json
{
  "status": "success",
  "message": "Jadwal berhasil diambil",
  "data": [
    {
      "id": 1,
      "matakuliah": "Pemrograman Web",
      "hari": "Senin",
      "jam_mulai": "08:00:00",
      "ruangan": "Lab Komputer 1",
      "tipe": "Praktikum"
    }
  ]
}
```

---

### 4.2 Get Today's Schedule
**Endpoint:** `GET /schedule.php`

**Request Parameters:**
```
?action=today
```

**Response:** (Sama dengan Get All Schedules, tapi hanya hari ini)

---

### 4.3 Get Schedule by Day
**Endpoint:** `GET /schedule.php`

**Request Parameters:**
```
?action=by_day&day=Senin
```

**Response:** (Sama dengan Get All Schedules, tapi hanya hari yang dipilih)

---

### 4.4 Create Schedule (Admin Only)
**Endpoint:** `POST /schedule.php`

**Request Parameters:**
```json
{
  "matakuliah": "Pemrograman Web",
  "hari": "Senin",
  "jam_mulai": "08:00:00",
  "ruangan": "Lab Komputer 1",
  "tipe": "Praktikum"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Jadwal berhasil ditambahkan"
}
```

---

### 4.5 Update Schedule (Admin Only)
**Endpoint:** `PUT /schedule.php`

**Request Parameters:**
```
?id=1
Body: (sama seperti create)
```

**Response:**
```json
{
  "status": "success",
  "message": "Jadwal berhasil diupdate"
}
```

---

### 4.6 Delete Schedule (Admin Only)
**Endpoint:** `DELETE /schedule.php`

**Request Parameters:**
```
?id=1
```

**Response:**
```json
{
  "status": "success",
  "message": "Jadwal berhasil dihapus"
}
```

---

## 5. CORS Headers

Semua endpoint mendukung CORS requests dengan headers:
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

---

## 6. Error Handling

### Common Error Responses

**Validation Error (422):**
```json
{
  "status": "validation_error",
  "message": "Validation failed",
  "errors": {
    "field_name": "Error message"
  }
}
```

**Database Error (500):**
```json
{
  "status": "error",
  "message": "Database connection error"
}
```

**Not Found (404):**
```json
{
  "status": "error",
  "message": "Endpoint tidak ditemukan"
}
```

---

## 7. Integration with Mobile App

### Important Notes:

1. **Device Binding**: Setiap user diikat dengan satu device_id untuk keamanan
2. **File Upload**: Maksimal ukuran file 5MB per file
3. **Authentication**: Gunakan response login untuk mendapatkan user_id dan device_id
4. **Timestamps**: Semua timestamp dalam format `YYYY-MM-DD HH:mm:ss` dengan timezone Asia/Jakarta
5. **Coordinates**: Format latitude/longitude harus sesuai standar geografis (-90 to 90 for lat, -180 to 180 for lng)

### Example Mobile Implementation:

**Login Flow:**
```
1. POST /auth.php dengan nim_nik & password
2. Dapatkan user_id dan device_id dari response
3. Simpan di SharedPreferences/LocalStorage
4. Gunakan untuk semua request berikutnya
```

**Attendance Flow:**
```
1. GET /schedule.php?action=today untuk melihat jadwal
2. POST /attendance.php dengan data lokasi + foto
3. GET /attendance.php?action=history untuk riwayat
```

---

## 8. Database Schema

### Users Table
```sql
CREATE TABLE `users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `nim_nik` varchar(20) NOT NULL UNIQUE,
  `nama` varchar(100) NOT NULL,
  `gender` varchar(20),
  `jurusan` varchar(100),
  `kelas` varchar(50),
  `angkatan` varchar(10),
  `semester` varchar(20),
  `tempat_lahir` varchar(100),
  `tanggal_lahir` date,
  `password` varchar(255) NOT NULL,
  `device_id` varchar(255) NOT NULL UNIQUE,
  `doc_type` varchar(50),
  `foto_ktm` varchar(255),
  `foto_selfie` varchar(255),
  `status_akun` enum('pending','aktif') DEFAULT 'aktif',
  `role` enum('mahasiswa','dosen') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Attendance Table
```sql
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11),
  `matakuliah` varchar(100) NOT NULL,
  `keterangan` enum('Hadir','Izin','Sakit','Alfa','Terlambat') NOT NULL,
  `latitude` decimal(10,8),
  `longitude` decimal(11,8),
  `foto` varchar(255),
  `device_id` varchar(255),
  `waktu_absen` timestamp DEFAULT CURRENT_TIMESTAMP,
  `tanggal` date DEFAULT CURDATE(),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Schedule Table
```sql
CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `matakuliah` varchar(100),
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'),
  `jam_mulai` time,
  `ruangan` varchar(50),
  `tipe` enum('Teori','Praktikum') DEFAULT 'Teori'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 9. Security Features

✅ **Implemented:**
- Prepared Statements (SQL Injection Prevention)
- Password Hashing (bcrypt)
- Device Binding (Fraud Prevention)
- Input Validation & Sanitization
- CORS Support
- Input Type Validation

⚠️ **For Production:**
- Implement JWT Token Authentication
- Add Rate Limiting
- Use HTTPS/SSL
- Add API Key Authentication
- Implement Request Logging
- Add Two-Factor Authentication

---

## Support

Untuk pertanyaan atau issues, hubungi: developer@absensi-system.local

Last Updated: 2024-05-22
