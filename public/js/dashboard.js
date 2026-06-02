// Dashboard JavaScript
const API_URL = '../api';

$(document).ready(function() {
    loadDashboardStats();
    loadRecentAttendance();
    loadTodaySchedule();
    
    // Refresh data every 30 seconds
    setInterval(loadDashboardStats, 30000);
    setInterval(loadRecentAttendance, 30000);
});

function loadDashboardStats() {
    // Simulate API call - replace with actual API
    $.ajax({
        url: API_URL + '/attendance.php?action=all&limit=1000',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                calculateStats(response.data);
            }
        },
        error: function() {
            console.log('Error loading attendance data');
        }
    });
}

function calculateStats(attendanceData) {
    const today = new Date().toISOString().split('T')[0];
    
    // Get unique users
    const users = new Set();
    let presentToday = 0;
    let permissionToday = 0;
    let absentToday = 0;
    
    attendanceData.forEach(record => {
        if (record.tanggal === today) {
            if (record.keterangan === 'Hadir') presentToday++;
            else if (['Izin', 'Sakit'].includes(record.keterangan)) permissionToday++;
            else if (record.keterangan === 'Alfa') absentToday++;
        }
        users.add(record.user_id);
    });
    
    // Update stats
    $('#total-students').text(users.size);
    $('#present-today').text(presentToday);
    $('#permission-today').text(permissionToday);
    $('#absent-today').text(absentToday);
}

function loadRecentAttendance() {
    $.ajax({
        url: API_URL + '/attendance.php?action=all&limit=10&offset=0',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                renderRecentAttendance(response.data);
            }
        },
        error: function() {
            $('#recent-attendance-table').html(
                '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-circle"></i> <p>Error loading data</p></div>'
            );
        }
    });
}

function renderRecentAttendance(data) {
    if (data.length === 0) {
        $('#recent-attendance-table').html(
            '<div class="text-center py-4 text-muted"><i class="bi bi-inbox"></i> <p>Tidak ada data absensi</p></div>'
        );
        return;
    }
    
    let html = '<table class="table table-sm table-hover mb-0">';
    html += '<thead class="table-light"><tr><th>Mahasiswa</th><th>Matakuliah</th><th>Status</th><th>Waktu</th></tr></thead>';
    html += '<tbody>';
    
    data.forEach(record => {
        const statusClass = getStatusBadgeClass(record.keterangan);
        const time = new Date(record.waktu_absen).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        html += `<tr>
                    <td>${record.nama || 'N/A'}</td>
                    <td>${record.matakuliah}</td>
                    <td><span class="badge ${statusClass}">${record.keterangan}</span></td>
                    <td>${time}</td>
                </tr>`;
    });
    
    html += '</tbody></table>';
    $('#recent-attendance-table').html(html);
}

function loadTodaySchedule() {
    $.ajax({
        url: API_URL + '/schedule.php?action=today',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                renderTodaySchedule(response.data);
            }
        },
        error: function() {
            $('#today-schedule').html(
                '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-circle"></i> <p>Error loading schedule</p></div>'
            );
        }
    });
}

function renderTodaySchedule(schedules) {
    if (schedules.length === 0) {
        $('#today-schedule').html(
            '<div class="text-center py-4 text-muted"><p class="mb-0">Tidak ada jadwal hari ini</p></div>'
        );
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    schedules.forEach((schedule, index) => {
        const typeClass = schedule.tipe === 'Praktikum' ? 'text-success' : 'text-info';
        
        html += `<div class="list-group-item border-0 py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${schedule.matakuliah}</h6>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> ${schedule.jam_mulai}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-door-open"></i> ${schedule.ruangan}
                            </small>
                        </div>
                        <span class="badge bg-light ${typeClass}">${schedule.tipe}</span>
                    </div>
                </div>`;
        
        if (index < schedules.length - 1) {
            html += '<hr class="my-0">';
        }
    });
    
    html += '</div>';
    $('#today-schedule').html(html);
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'Hadir':
            return 'bg-success';
        case 'Izin':
            return 'bg-info';
        case 'Sakit':
            return 'bg-warning';
        case 'Alfa':
            return 'bg-danger';
        case 'Terlambat':
            return 'bg-secondary';
        default:
            return 'bg-secondary';
    }
}
