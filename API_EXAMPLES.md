# API Usage Examples

## 1. LOGIN

**cURL:**
```bash
curl -X POST http://localhost/absensi_api/api/auth.php \
  -d "action=login&nim_nik=20240810002&password=Kuningan2003-10-03"
```

**JavaScript (Fetch):**
```javascript
fetch('http://localhost/absensi_api/api/auth.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
  },
  body: 'action=login&nim_nik=20240810002&password=Kuningan2003-10-03'
})
.then(res => res.json())
.then(data => console.log(data));
```

**Postman:**
- Method: POST
- URL: http://localhost/absensi_api/api/auth.php
- Body (form-data):
  - action: login
  - nim_nik: 20240810002
  - password: Kuningan2003-10-03

---

## 2. REGISTER NEW USER

**cURL:**
```bash
curl -X POST http://localhost/absensi_api/api/register.php \
  -F "nim_nik=20240810003" \
  -F "nama=Budi Santoso" \
  -F "gender=Laki-laki" \
  -F "jurusan=Teknik Informatika" \
  -F "kelas=B" \
  -F "angkatan=2024" \
  -F "semester=1" \
  -F "tempat_lahir=Bandung" \
  -F "tanggal_lahir=2003-05-15" \
  -F "device_id=DEVICE_12345" \
  -F "doc_type=KTM (Kartu Tanda Mahasiswa)" \
  -F "role=mahasiswa" \
  -F "foto_ktm=@/path/to/ktm.jpg" \
  -F "foto_selfie=@/path/to/selfie.jpg"

# Contoh registrasi dosen/admin
curl -X POST http://localhost/absensi_api/api/register.php \
  -F "nim_nik=19871234001" \
  -F "nama=Dr. Rudi Santoso" \
  -F "gender=Laki-laki" \
  -F "jurusan=Teknik Informatika" \
  -F "tempat_lahir=Jakarta" \
  -F "tanggal_lahir=1987-12-03" \
  -F "device_id=DEVICE_DOSEN_001" \
  -F "doc_type=Dosen" \
  -F "role=dosen"
```

**JavaScript (FormData):**
```javascript
const formData = new FormData();
formData.append('nim_nik', '20240810003');
formData.append('nama', 'Budi Santoso');
formData.append('tempat_lahir', 'Bandung');
formData.append('tanggal_lahir', '2003-05-15');
formData.append('device_id', 'DEVICE_12345');
formData.append('foto_ktm', fileInput1.files[0]);
formData.append('foto_selfie', fileInput2.files[0]);

fetch('http://localhost/absensi_api/api/register.php', {
  method: 'POST',
  body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

---

## 3. CREATE ATTENDANCE

**cURL:**
```bash
curl -X POST http://localhost/absensi_api/api/attendance.php \
  -F "user_id=1" \
  -F "matakuliah=Pemrograman Web" \
  -F "keterangan=Hadir" \
  -F "latitude=-6.123456" \
  -F "longitude=106.654321" \
  -F "device_id=DEVICE_12345" \
  -F "foto=@/path/to/attendance.jpg"
```

**JavaScript (FormData):**
```javascript
const formData = new FormData();
formData.append('user_id', 1);
formData.append('matakuliah', 'Pemrograman Web');
formData.append('keterangan', 'Hadir');
formData.append('latitude', -6.123456);
formData.append('longitude', 106.654321);
formData.append('device_id', 'DEVICE_12345');
formData.append('foto', cameraInput.files[0]);

fetch('http://localhost/absensi_api/api/attendance.php', {
  method: 'POST',
  body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

---

## 4. GET ATTENDANCE HISTORY

**cURL:**
```bash
curl "http://localhost/absensi_api/api/attendance.php?action=history&user_id=1&limit=50"
```

**JavaScript:**
```javascript
fetch('http://localhost/absensi_api/api/attendance.php?action=history&user_id=1&limit=50')
  .then(res => res.json())
  .then(data => console.log(data));
```

---

## 5. GET ATTENDANCE SUMMARY

**cURL:**
```bash
curl "http://localhost/absensi_api/api/attendance.php?action=summary&user_id=1"
```

**JavaScript:**
```javascript
fetch('http://localhost/absensi_api/api/attendance.php?action=summary&user_id=1')
  .then(res => res.json())
  .then(data => console.log(data));
```

---

## 6. GET ALL SCHEDULES

**cURL:**
```bash
curl "http://localhost/absensi_api/api/schedule.php?action=all"
```

**JavaScript:**
```javascript
fetch('http://localhost/absensi_api/api/schedule.php?action=all')
  .then(res => res.json())
  .then(data => console.log(data));
```

---

## 7. GET TODAY'S SCHEDULE

**cURL:**
```bash
curl "http://localhost/absensi_api/api/schedule.php?action=today"
```

---

## 8. GET SCHEDULE BY DAY

**cURL:**
```bash
curl "http://localhost/absensi_api/api/schedule.php?action=by_day&day=Senin"
```

---

## 9. CREATE SCHEDULE (ADMIN)

**cURL:**
```bash
curl -X POST http://localhost/absensi_api/api/schedule.php \
  -d "matakuliah=Basis Data" \
  -d "hari=Selasa" \
  -d "jam_mulai=10:30:00" \
  -d "ruangan=Ruang 3.2" \
  -d "tipe=Teori"
```

---

## 10. DELETE ATTENDANCE (ADMIN)

**cURL:**
```bash
curl -X DELETE "http://localhost/absensi_api/api/attendance.php?id=1"
```

---

## TESTING WITH POSTMAN

1. Import Collection:
```json
{
  "info": {
    "name": "Absensi API",
    "description": "Sistem Absensi REST API"
  },
  "item": [
    {
      "name": "Login",
      "request": {
        "method": "POST",
        "url": "http://localhost/absensi_api/api/auth.php",
        "body": {
          "mode": "urlencoded",
          "urlencoded": [
            {"key": "action", "value": "login"},
            {"key": "nim_nik", "value": "20240810002"},
            {"key": "password", "value": "Kuningan2003-10-03"}
          ]
        }
      }
    },
    {
      "name": "Get Today Schedule",
      "request": {
        "method": "GET",
        "url": "http://localhost/absensi_api/api/schedule.php?action=today"
      }
    },
    {
      "name": "Get Attendance History",
      "request": {
        "method": "GET",
        "url": "http://localhost/absensi_api/api/attendance.php?action=history&user_id=1"
      }
    }
  ]
}
```

---

## TROUBLESHOOTING

**Q: Login returns "NIM belum terdaftar"**
A: Pastikan user sudah terdaftar di database. Use register endpoint atau import database SQL.

**Q: Upload file gagal**
A: 
- Pastikan ukuran file < 5MB
- Format file harus JPEG/JPG/PNG
- Pastikan folder uploads/ memiliki write permission (chmod 777)

**Q: CORS error di browser**
A: API sudah memiliki CORS headers. Pastikan request method yang benar.

**Q: Perangkat tidak cocok dengan akun**
A: Device ID harus sama dengan yang terdaftar saat login. Cek nilai device_id.

---

## RESPONSE CODES

- 200: OK / Success
- 201: Created / Resource created successfully
- 400: Bad Request / Invalid data
- 401: Unauthorized / Authentication failed
- 403: Forbidden / Access denied
- 404: Not Found / Endpoint not found
- 409: Conflict / Data conflict (e.g., duplicate entry)
- 422: Unprocessable Entity / Validation error
- 500: Server Error / Database connection error

---

Last Updated: 2024-05-22
