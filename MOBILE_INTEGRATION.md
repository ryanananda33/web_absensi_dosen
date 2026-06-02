# Mobile Integration Guide

## Overview

Sistem Absensi Anda sekarang memiliki REST API yang siap diintegrasikan dengan aplikasi mobile. Panduan ini menjelaskan cara mengintegrasikan mobile app Anda dengan backend.

---

## 1. Android Integration (Java/Kotlin)

### 1.1 Setup Dependencies

**build.gradle:**
```gradle
dependencies {
    // HTTP Client
    implementation 'com.squareup.okhttp3:okhttp:4.11.0'
    implementation 'com.squareup.retrofit2:retrofit:2.10.0'
    implementation 'com.squareup.retrofit2:converter-gson:2.10.0'
    
    // JSON Parsing
    implementation 'com.google.code.gson:gson:2.10.1'
    
    // Lifecycle & ViewModel
    implementation 'androidx.lifecycle:lifecycle-viewmodel:2.6.1'
    
    // SharedPreferences encryption
    implementation 'androidx.security:security-crypto:1.1.0-alpha06'
}
```

### 1.2 Create API Service Interface

**ApiService.kt:**
```kotlin
import retrofit2.Call
import retrofit2.http.*
import okhttp3.MultipartBody
import okhttp3.RequestBody

interface ApiService {
    companion object {
        const val BASE_URL = "http://your-server:8000/absensi_api/api/"
    }

    // Authentication
    @FormUrlEncoded
    @POST("auth.php")
    fun login(
        @Field("action") action: String = "login",
        @Field("nim_nik") nimNik: String,
        @Field("password") password: String
    ): Call<LoginResponse>

    // Registration
    @Multipart
    @POST("register.php")
    fun register(
        @Part("nim_nik") nimNik: RequestBody,
        @Part("nama") nama: RequestBody,
        @Part("gender") gender: RequestBody,
        @Part("jurusan") jurusan: RequestBody,
        @Part("kelas") kelas: RequestBody,
        @Part("angkatan") angkatan: RequestBody,
        @Part("semester") semester: RequestBody,
        @Part("tempat_lahir") tempatLahir: RequestBody,
        @Part("tanggal_lahir") tanggalLahir: RequestBody,
        @Part("device_id") deviceId: RequestBody,
        @Part("doc_type") docType: RequestBody,
        @Part fotoKtm: MultipartBody.Part,
        @Part fotoSelfie: MultipartBody.Part
    ): Call<RegisterResponse>

    // Attendance
    @Multipart
    @POST("attendance.php")
    fun createAttendance(
        @Part("user_id") userId: RequestBody,
        @Part("matakuliah") matakuliah: RequestBody,
        @Part("keterangan") keterangan: RequestBody,
        @Part("latitude") latitude: RequestBody,
        @Part("longitude") longitude: RequestBody,
        @Part("device_id") deviceId: RequestBody,
        @Part foto: MultipartBody.Part
    ): Call<AttendanceResponse>

    @GET("attendance.php")
    fun getAttendanceHistory(
        @Query("action") action: String = "history",
        @Query("user_id") userId: Int,
        @Query("limit") limit: Int = 50,
        @Query("offset") offset: Int = 0
    ): Call<List<AttendanceRecord>>

    @GET("attendance.php")
    fun getAttendanceSummary(
        @Query("action") action: String = "summary",
        @Query("user_id") userId: Int
    ): Call<List<SummaryRecord>>

    // Schedules
    @GET("schedule.php")
    fun getAllSchedules(
        @Query("action") action: String = "all"
    ): Call<List<Schedule>>

    @GET("schedule.php")
    fun getTodaySchedule(
        @Query("action") action: String = "today"
    ): Call<List<Schedule>>

    @GET("schedule.php")
    fun getScheduleByDay(
        @Query("action") action: String = "by_day",
        @Query("day") day: String
    ): Call<List<Schedule>>
}
```

### 1.3 Data Classes

**Models.kt:**
```kotlin
// Login Response
data class LoginResponse(
    val status: String,
    val message: String,
    val data: UserData?
)

data class UserData(
    val id: Int,
    val nim_nik: String,
    val nama: String,
    val gender: String,
    val jurusan: String,
    val kelas: String,
    val angkatan: String,
    val semester: String,
    val role: String,
    val device_id: String
)

// Attendance
data class AttendanceResponse(
    val status: String,
    val message: String,
    val data: AttendanceData?
)

data class AttendanceData(
    val attendance_id: Int,
    val timestamp: String
)

data class AttendanceRecord(
    val id: Int,
    val user_id: Int,
    val matakuliah: String,
    val keterangan: String,
    val latitude: String,
    val longitude: String,
    val foto: String,
    val waktu_absen: String,
    val tanggal: String
)

data class SummaryRecord(
    val matakuliah: String,
    val total: Int,
    val hadir: Int,
    val izin: Int,
    val sakit: Int,
    val alfa: Int,
    val terlambat: Int
)

// Schedule
data class Schedule(
    val id: Int,
    val matakuliah: String,
    val hari: String,
    val jam_mulai: String,
    val ruangan: String,
    val tipe: String
)

// Registration
data class RegisterResponse(
    val status: String,
    val message: String,
    val data: RegisterData?
)

data class RegisterData(
    val user_id: Int,
    val default_password: String,
    val status: String
)
```

### 1.4 Retrofit Client

**RetrofitClient.kt:**
```kotlin
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

object RetrofitClient {
    private val retrofit by lazy {
        Retrofit.Builder()
            .baseUrl(ApiService.BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    val apiService: ApiService by lazy {
        retrofit.create(ApiService::class.java)
    }
}
```

### 1.5 Authentication Manager

**AuthManager.kt:**
```kotlin
import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey

class AuthManager(context: Context) {
    private val masterKey = MasterKey.Builder(context)
        .setKeyScheme(MasterKey.KeyScheme.AES256_GCM)
        .build()

    private val sharedPreferences = EncryptedSharedPreferences.create(
        context,
        "auth_prefs",
        masterKey,
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
    )

    fun saveUser(userId: Int, deviceId: String, username: String) {
        sharedPreferences.edit().apply {
            putInt("user_id", userId)
            putString("device_id", deviceId)
            putString("username", username)
            apply()
        }
    }

    fun getUserId(): Int = sharedPreferences.getInt("user_id", -1)
    fun getDeviceId(): String = sharedPreferences.getString("device_id", "") ?: ""
    fun getUsername(): String = sharedPreferences.getString("username", "") ?: ""

    fun isLoggedIn(): Boolean = getUserId() != -1

    fun logout() {
        sharedPreferences.edit().clear().apply()
    }
}
```

### 1.6 Login Activity Example

**LoginActivity.kt:**
```kotlin
import android.content.Intent
import android.os.Bundle
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class LoginActivity : AppCompatActivity() {
    private lateinit var authManager: AuthManager
    private lateinit var apiService: ApiService
    private lateinit var nimInput: EditText
    private lateinit var passwordInput: EditText
    private lateinit var loginButton: Button
    private lateinit var progressBar: ProgressBar

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

        authManager = AuthManager(this)
        apiService = RetrofitClient.apiService

        if (authManager.isLoggedIn()) {
            startActivity(Intent(this, MainActivity::class.java))
            finish()
            return
        }

        nimInput = findViewById(R.id.nim_input)
        passwordInput = findViewById(R.id.password_input)
        loginButton = findViewById(R.id.login_button)
        progressBar = findViewById(R.id.progress_bar)

        loginButton.setOnClickListener {
            performLogin()
        }
    }

    private fun performLogin() {
        val nim = nimInput.text.toString().trim()
        val password = passwordInput.text.toString().trim()

        if (nim.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "NIM dan password harus diisi", Toast.LENGTH_SHORT).show()
            return
        }

        progressBar.visibility = ProgressBar.VISIBLE
        loginButton.isEnabled = false

        apiService.login(nimNik = nim, password = password)
            .enqueue(object : Callback<LoginResponse> {
                override fun onResponse(call: Call<LoginResponse>, response: Response<LoginResponse>) {
                    progressBar.visibility = ProgressBar.GONE
                    loginButton.isEnabled = true

                    if (response.isSuccessful && response.body()?.status == "success") {
                        val user = response.body()?.data
                        if (user != null) {
                            authManager.saveUser(user.id, user.device_id, user.nama)
                            startActivity(Intent(this@LoginActivity, MainActivity::class.java))
                            finish()
                        }
                    } else {
                        val message = response.body()?.message ?: "Login gagal"
                        Toast.makeText(this@LoginActivity, message, Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<LoginResponse>, t: Throwable) {
                    progressBar.visibility = ProgressBar.GONE
                    loginButton.isEnabled = true
                    Toast.makeText(this@LoginActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
    }
}
```

### 1.7 Attendance Activity Example

**AttendanceActivity.kt:**
```kotlin
import android.Manifest
import android.content.pm.PackageManager
import android.location.Location
import android.os.Bundle
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.camera.core.CameraSelector
import androidx.camera.core.Preview
import androidx.camera.lifecycle.ProcessCameraProvider
import androidx.camera.view.PreviewView
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import com.google.android.gms.location.FusedLocationProviderClient
import com.google.android.gms.location.LocationServices
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.toRequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.File

class AttendanceActivity : AppCompatActivity() {
    private lateinit var apiService: ApiService
    private lateinit var authManager: AuthManager
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    
    private var latitude: Double = 0.0
    private var longitude: Double = 0.0
    private var photoFile: File? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_attendance)

        apiService = RetrofitClient.apiService
        authManager = AuthManager(this)
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)

        requestLocationPermission()
        setupCamera()

        findViewById<Button>(R.id.submit_attendance).setOnClickListener {
            submitAttendance()
        }
    }

    private fun requestLocationPermission() {
        if (ContextCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(
                this,
                arrayOf(Manifest.permission.ACCESS_FINE_LOCATION),
                PERMISSION_REQUEST_CODE
            )
        } else {
            getCurrentLocation()
        }
    }

    private fun getCurrentLocation() {
        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) == PackageManager.PERMISSION_GRANTED
        ) {
            fusedLocationClient.lastLocation.addOnSuccessListener { location: Location? ->
                if (location != null) {
                    latitude = location.latitude
                    longitude = location.longitude
                    Toast.makeText(this, "Lokasi: $latitude, $longitude", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun setupCamera() {
        // Implementation untuk mengakses kamera
        // Gunakan CameraX atau Camera2 API
    }

    private fun submitAttendance() {
        val userId = authManager.getUserId()
        val deviceId = authManager.getDeviceId()
        val matakuliah = findViewById<Spinner>(R.id.matakuliah_spinner).selectedItem.toString()
        val keterangan = findViewById<Spinner>(R.id.status_spinner).selectedItem.toString()

        if (photoFile == null) {
            Toast.makeText(this, "Ambil foto terlebih dahulu", Toast.LENGTH_SHORT).show()
            return
        }

        val userIdBody = userId.toString().toRequestBody("text/plain".toMediaType())
        val matakuliahBody = matakuliah.toRequestBody("text/plain".toMediaType())
        val keteranganBody = keterangan.toRequestBody("text/plain".toMediaType())
        val latBody = latitude.toString().toRequestBody("text/plain".toMediaType())
        val lngBody = longitude.toString().toRequestBody("text/plain".toMediaType())
        val deviceIdBody = deviceId.toRequestBody("text/plain".toMediaType())

        val fotoPart = MultipartBody.Part.createFormData(
            "foto",
            photoFile!!.name,
            photoFile!!.readBytes().toRequestBody("image/jpeg".toMediaType())
        )

        apiService.createAttendance(
            userIdBody, matakuliahBody, keteranganBody, latBody, lngBody, deviceIdBody, fotoPart
        ).enqueue(object : Callback<AttendanceResponse> {
            override fun onResponse(call: Call<AttendanceResponse>, response: Response<AttendanceResponse>) {
                if (response.isSuccessful && response.body()?.status == "success") {
                    Toast.makeText(this@AttendanceActivity, "Absensi berhasil", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@AttendanceActivity, response.body()?.message, Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<AttendanceResponse>, t: Throwable) {
                Toast.makeText(this@AttendanceActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    companion object {
        private const val PERMISSION_REQUEST_CODE = 100
    }
}
```

---

## 2. iOS Integration (Swift)

### 2.1 API Service

**APIService.swift:**
```swift
import Foundation

struct APIResponse<T: Codable>: Codable {
    let status: String
    let message: String
    let data: T?
}

struct User: Codable {
    let id: Int
    let nim_nik: String
    let nama: String
    let gender: String?
    let jurusan: String?
    let kelas: String?
    let angkatan: String?
    let semester: String?
    let role: String
    let device_id: String
}

struct Schedule: Codable {
    let id: Int
    let matakuliah: String
    let hari: String
    let jam_mulai: String
    let ruangan: String
    let tipe: String
}

class APIService {
    static let shared = APIService()
    private let baseURL = "http://your-server:8000/absensi_api/api/"

    func login(nim: String, password: String, completion: @escaping (Result<User, Error>) -> Void) {
        var request = URLRequest(url: URL(string: baseURL + "auth.php")!)
        request.httpMethod = "POST"
        request.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")
        
        let body = "action=login&nim_nik=\(nim)&password=\(password)"
        request.httpBody = body.data(using: .utf8)

        URLSession.shared.dataTask(with: request) { data, response, error in
            if let error = error {
                completion(.failure(error))
                return
            }

            guard let data = data else {
                completion(.failure(NSError(domain: "No data", code: -1)))
                return
            }

            do {
                let response = try JSONDecoder().decode(APIResponse<User>.self, from: data)
                if response.status == "success", let user = response.data {
                    completion(.success(user))
                } else {
                    let error = NSError(domain: response.message, code: -1)
                    completion(.failure(error))
                }
            } catch {
                completion(.failure(error))
            }
        }.resume()
    }

    func getTodaySchedule(completion: @escaping (Result<[Schedule], Error>) -> Void) {
        let url = URL(string: baseURL + "schedule.php?action=today")!
        
        URLSession.shared.dataTask(with: url) { data, response, error in
            if let error = error {
                completion(.failure(error))
                return
            }

            guard let data = data else {
                completion(.failure(NSError(domain: "No data", code: -1)))
                return
            }

            do {
                let schedules = try JSONDecoder().decode([Schedule].self, from: data)
                completion(.success(schedules))
            } catch {
                completion(.failure(error))
            }
        }.resume()
    }
}
```

---

## 3. Flutter Integration

### 3.1 pubspec.yaml Dependencies

```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0
  provider: ^6.0.0
  shared_preferences: ^2.2.0
  image_picker: ^1.0.0
  geolocator: ^9.0.0
```

### 3.2 API Service

**api_service.dart:**
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = 'http://your-server:8000/absensi_api/api/';

  // Login
  static Future<Map<String, dynamic>> login(String nim, String password) async {
    final response = await http.post(
      Uri.parse('${baseUrl}auth.php'),
      body: {
        'action': 'login',
        'nim_nik': nim,
        'password': password,
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Login failed');
    }
  }

  // Get Today Schedule
  static Future<List<dynamic>> getTodaySchedule() async {
    final response = await http.get(
      Uri.parse('${baseUrl}schedule.php?action=today'),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body)['data'] ?? [];
    } else {
      throw Exception('Failed to load schedules');
    }
  }

  // Create Attendance
  static Future<Map<String, dynamic>> createAttendance({
    required int userId,
    required String matakuliah,
    required String keterangan,
    required double latitude,
    required double longitude,
    required String deviceId,
    required String fotoPath,
  }) async {
    var request = http.MultipartRequest(
      'POST',
      Uri.parse('${baseUrl}attendance.php'),
    );

    request.fields.addAll({
      'user_id': userId.toString(),
      'matakuliah': matakuliah,
      'keterangan': keterangan,
      'latitude': latitude.toString(),
      'longitude': longitude.toString(),
      'device_id': deviceId,
    });

    request.files.add(await http.MultipartFile.fromPath('foto', fotoPath));

    var response = await request.send();
    var responseBody = await response.stream.bytesToString();

    if (response.statusCode == 201) {
      return jsonDecode(responseBody);
    } else {
      throw Exception('Failed to create attendance');
    }
  }

  // Get Attendance History
  static Future<List<dynamic>> getAttendanceHistory(int userId) async {
    final response = await http.get(
      Uri.parse('${baseUrl}attendance.php?action=history&user_id=$userId'),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body)['data'] ?? [];
    } else {
      throw Exception('Failed to load attendance history');
    }
  }
}
```

### 3.3 Auth Provider

**auth_provider.dart:**
```dart
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'api_service.dart';

class AuthProvider extends ChangeNotifier {
  int? _userId;
  String? _deviceId;
  String? _username;

  int? get userId => _userId;
  String? get deviceId => _deviceId;
  String? get username => _username;

  bool get isLoggedIn => _userId != null;

  Future<bool> login(String nim, String password) async {
    try {
      final response = await ApiService.login(nim, password);
      if (response['status'] == 'success') {
        final user = response['data'];
        _userId = user['id'];
        _deviceId = user['device_id'];
        _username = user['nama'];

        // Save to SharedPreferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setInt('user_id', _userId!);
        await prefs.setString('device_id', _deviceId!);
        await prefs.setString('username', _username!);

        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<void> logout() async {
    _userId = null;
    _deviceId = null;
    _username = null;

    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();

    notifyListeners();
  }

  Future<void> restoreSession() async {
    final prefs = await SharedPreferences.getInstance();
    _userId = prefs.getInt('user_id');
    _deviceId = prefs.getString('device_id');
    _username = prefs.getString('username');
    notifyListeners();
  }
}
```

### 3.4 Login Screen

**login_screen.dart:**
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'auth_provider.dart';

class LoginScreen extends StatefulWidget {
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final nimController = TextEditingController();
  final passwordController = TextEditingController();
  bool isLoading = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(
              controller: nimController,
              decoration: InputDecoration(labelText: 'NIM'),
            ),
            SizedBox(height: 20),
            TextField(
              controller: passwordController,
              decoration: InputDecoration(labelText: 'Password'),
              obscureText: true,
            ),
            SizedBox(height: 20),
            isLoading
                ? CircularProgressIndicator()
                : ElevatedButton(
                    onPressed: () => _performLogin(context),
                    child: Text('Login'),
                  ),
          ],
        ),
      ),
    );
  }

  void _performLogin(BuildContext context) async {
    setState(() => isLoading = true);

    final success = await context.read<AuthProvider>().login(
          nimController.text,
          passwordController.text,
        );

    setState(() => isLoading = false);

    if (success) {
      Navigator.of(context).pushReplacementNamed('/home');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Login failed')),
      );
    }
  }

  @override
  void dispose() {
    nimController.dispose();
    passwordController.dispose();
    super.dispose();
  }
}
```

---

## 4. Security Best Practices

✅ **Do:**
- Store user credentials securely (encrypted storage)
- Use HTTPS in production
- Validate all inputs before sending
- Implement token refresh mechanism
- Log out on app exit
- Request location permission from user
- Validate GPS coordinates

❌ **Don't:**
- Store passwords in plaintext
- Use HTTP in production
- Send sensitive data in URL parameters
- Cache authentication tokens permanently
- Trust client-side validation alone
- Store private keys in app

---

## 5. Common Issues & Solutions

### Issue: CORS Error
**Solution:** 
- API sudah memiliki CORS headers
- Pastikan request method sesuai (POST, GET, dll)
- Cek Content-Type header

### Issue: "Perangkat tidak cocok dengan akun"
**Solution:**
- Device ID harus konsisten
- Generate unique device ID menggunakan UUID library
- Simpan di secure storage

### Issue: File Upload Fails
**Solution:**
- Periksa ukuran file (max 5MB)
- Pastikan format JPEG/PNG
- Cek permission (camera, file storage)

### Issue: Location is Null
**Solution:**
- Request location permission terlebih dahulu
- Gunakan high accuracy mode
- Test di real device (emulator sering bermasalah)

---

## 6. Testing Checklist

- [ ] Login dengan NIM yang benar
- [ ] Login dengan password salah
- [ ] Ambil jadwal hari ini
- [ ] Submit attendance dengan lokasi
- [ ] Lihat riwayat attendance
- [ ] Check device binding validation
- [ ] Test upload foto
- [ ] Cek internet connection handling
- [ ] Test logout flow
- [ ] Test app crash recovery

---

## 7. Performance Optimization

1. **Image Compression:** Compress foto sebelum upload
2. **Caching:** Cache jadwal untuk penggunaan offline
3. **Pagination:** Gunakan limit & offset untuk history
4. **Network:** Implementasi request timeout
5. **Storage:** Clear old files secara berkala

---

## 8. Support Resources

- API Documentation: API_DOCUMENTATION.md
- Setup Guide: SETUP_GUIDE.md
- API Examples: API_EXAMPLES.md

---

Last Updated: 2024-05-22
