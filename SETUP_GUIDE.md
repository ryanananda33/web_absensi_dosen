# Project Structure & Configuration Guide

## Folder Structure

```
absensi_api/
├── api/                      # REST API endpoints
│   ├── auth.php             # Login & profile endpoints
│   ├── register.php         # Registration & user management
│   ├── attendance.php       # Attendance CRUD operations
│   └── schedule.php         # Schedule management
│
├── config/                  # Configuration files
│   ├── Database.php         # Database connection class
│   └── Response.php         # JSON response handler
│
├── controllers/             # Business logic (expandable)
│
├── models/                  # Database models
│   ├── User.php            # User model with queries
│   ├── Attendance.php      # Attendance model
│   └── Schedule.php        # Schedule model
│
├── helpers/                 # Helper functions
│   ├── Validation.php      # Input validation
│   └── Security.php        # Security functions (hash, token)
│
├── middleware/              # Middleware
│   └── Auth.php            # Authentication middleware
│
├── public/                  # Web UI & public files
│   ├── index.php           # Dashboard home
│   ├── attendance.php      # Attendance management page
│   ├── users.php           # Users management page
│   ├── schedule.php        # Schedule management page
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   ├── js/
│   │   ├── dashboard.js    # Dashboard logic
│   │   ├── attendance.js   # Attendance page logic
│   │   ├── users.js        # Users page logic
│   │   └── schedule.js     # Schedule page logic
│   └── uploads/            # User uploads directory
│       ├── ktm/            # Student ID card photos
│       └── selfie/         # Verification & attendance photos
│
├── views/                   # Reusable view components
│   └── layouts/
│
├── logs/                    # Application logs
│
└── API_DOCUMENTATION.md    # API documentation
```

## Setup Instructions

### 1. Database Setup

```bash
# Import database
1. Open phpMyAdmin
2. Create new database: absensi_db
3. Import absensi_db.sql file
```

**Or via MySQL command line:**
```bash
mysql -u root -p absensi_db < absensi_db.sql
```

### 2. File Permissions

Set write permissions for upload directories:
```bash
chmod 777 public/uploads/ktm/
chmod 777 public/uploads/selfie/
chmod 777 logs/
```

### 3. Configuration

Edit `config/Database.php` if needed:
```php
private $host = "localhost";      // MySQL host
private $db_name = "absensi_db";  // Database name
private $user = "root";           // MySQL user
private $password = "";           // MySQL password
```

### 4. Access the Application

**Web Dashboard:**
```
http://localhost/absensi_api/public/index.php
```

**API Endpoints:**
```
http://localhost/absensi_api/api/auth.php
http://localhost/absensi_api/api/register.php
http://localhost/absensi_api/api/attendance.php
http://localhost/absensi_api/api/schedule.php
```

## API Usage Examples

### 1. Login
```bash
curl -X POST http://localhost/absensi_api/api/auth.php \
  -d "action=login&nim_nik=20240810002&password=Kuningan2003-10-03"
```

### 2. Register User
```bash
curl -X POST http://localhost/absensi_api/api/register.php \
  -F "nim_nik=20240810003" \
  -F "nama=John Doe" \
  -F "gender=Laki-laki" \
  -F "jurusan=Teknik Informatika" \
  -F "kelas=A" \
  -F "angkatan=2024" \
  -F "semester=1" \
  -F "tempat_lahir=Jakarta" \
  -F "tanggal_lahir=2003-10-03" \
  -F "device_id=device123" \
  -F "doc_type=KTM (Kartu Tanda Mahasiswa)" \
  -F "foto_ktm=@ktm.jpg" \
  -F "foto_selfie=@selfie.jpg"
```

### 3. Create Attendance
```bash
curl -X POST http://localhost/absensi_api/api/attendance.php \
  -F "user_id=1" \
  -F "matakuliah=Pemrograman Web" \
  -F "keterangan=Hadir" \
  -F "latitude=-6.123456" \
  -F "longitude=106.654321" \
  -F "device_id=device123" \
  -F "foto=@attendance.jpg"
```

### 4. Get Schedules
```bash
curl http://localhost/absensi_api/api/schedule.php?action=all
curl http://localhost/absensi_api/api/schedule.php?action=today
curl http://localhost/absensi_api/api/schedule.php?action=by_day&day=Senin
```

## Integration with Mobile App

### Mobile Requirements:
- Store `user_id` and `device_id` after login
- Always include `device_id` in API requests
- Use `latitude` and `longitude` from device GPS
- Capture `foto` as base64 or multipart file

### Recommended Mobile Stack:
- **Android:** Retrofit + OkHttp + GSON
- **iOS:** URLSession + Codable
- **Flutter:** http + json_serializable

### Mobile Authentication Flow:
```
1. User enters NIM & Password
2. POST to /api/auth.php
3. Get user_id & device_id
4. Store in SecureStorage/Keychain
5. Use for all subsequent requests
```

## Security Checklist

✅ **Implemented:**
- Prepared statements for SQL injection prevention
- bcrypt password hashing
- Device binding for fraud prevention
- Input validation & sanitization
- CORS support for mobile apps

⚠️ **Recommended for Production:**
- [ ] Implement JWT tokens
- [ ] Add API rate limiting
- [ ] Enable HTTPS/SSL
- [ ] Add request logging & monitoring
- [ ] Implement admin authentication
- [ ] Add two-factor authentication
- [ ] Database backups
- [ ] API versioning

## Troubleshooting

### Issue: "Database connection error"
```
Solution:
1. Verify MySQL is running
2. Check database credentials in config/Database.php
3. Ensure database 'absensi_db' exists
```

### Issue: "Upload folder permission denied"
```
Solution:
chmod 777 public/uploads/ktm/
chmod 777 public/uploads/selfie/
```

### Issue: "CORS error in mobile app"
```
Solution:
- Verify CORS headers in API files
- Check Access-Control-Allow-Origin
- Try OPTIONS request first
```

### Issue: "File upload failed"
```
Solution:
1. Check file size (max 5MB)
2. Verify MIME type is image/jpeg
3. Ensure upload folder has write permissions
4. Check PHP upload_max_filesize setting
```

## Development Tips

1. **Testing API:** Use Postman or cURL
2. **Debugging:** Check logs/ folder for errors
3. **Database Changes:** Update models/ files accordingly
4. **Adding Endpoints:** Create new file in api/ folder
5. **Frontend Changes:** Edit views/ or public/ files

## Next Steps

1. ✅ Configure database
2. ✅ Test API endpoints
3. ✅ Develop mobile app with API
4. ✅ Customize dashboard UI
5. ✅ Setup admin authentication
6. ✅ Deploy to production server

---

For API documentation, see: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
