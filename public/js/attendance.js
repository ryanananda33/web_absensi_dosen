// Attendance Management JavaScript
const API_URL = '../api';
let deleteId = null;

$(document).ready(function() {
    loadAttendanceData();
    
    $('#btn-filter').click(loadAttendanceData);
    $('#btn-delete-confirm').click(confirmDelete);
});

function loadAttendanceData() {
    const date = $('#filter-date').val() || new Date().toISOString().split('T')[0];
    const status = $('#filter-status').val();
    
    $.ajax({
        url: API_URL + '/attendance.php?action=all&limit=1000',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                filterAndRenderAttendance(response.data, date, status);
            }
        },
        error: function() {
            showAlert('Error loading data', 'danger');
        }
    });
}

function filterAndRenderAttendance(data, date, status) {
    let filteredData = data.filter(record => {
        let dateMatch = !date || record.tanggal === date;
        let statusMatch = !status || record.keterangan === status;
        return dateMatch && statusMatch;
    });
    
    renderAttendanceTable(filteredData);
}

function renderAttendanceTable(data) {
    const tbody = $('#tbody-attendance');
    
    if (data.length === 0) {
        tbody.html(`<tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox"></i>
                            <p>Tidak ada data yang sesuai dengan filter</p>
                        </td>
                    </tr>`);
        return;
    }
    
    let html = '';
    data.forEach(record => {
        const statusBadge = getStatusBadge(record.keterangan);
        const time = new Date(record.waktu_absen).toLocaleString('id-ID');
        const mapUrl = `https://www.google.com/maps?q=${record.latitude},${record.longitude}`;
        
        html += `<tr>
                    <td>${record.nama}</td>
                    <td>${record.nim_nik}</td>
                    <td>${record.matakuliah}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <a href="${mapUrl}" target="_blank" class="btn btn-sm btn-info text-white">
                            <i class="bi bi-geo-alt"></i> Lihat Peta
                        </a>
                    </td>
                    <td>${time}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewPhoto('${record.foto}')">
                            <i class="bi bi-image"></i> Foto
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${record.id})">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>`;
    });
    
    tbody.html(html);
}

function getStatusBadge(status) {
    const badges = {
        'Hadir': '<span class="badge bg-success">Hadir</span>',
        'Izin': '<span class="badge bg-info">Izin</span>',
        'Sakit': '<span class="badge bg-warning">Sakit</span>',
        'Alfa': '<span class="badge bg-danger">Alfa</span>',
        'Terlambat': '<span class="badge bg-secondary">Terlambat</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function viewPhoto(photoName) {
    const photoUrl = `../public/uploads/selfie/${photoName}`;
    $('#photo-img').attr('src', photoUrl);
    new bootstrap.Modal(document.getElementById('photoModal')).show();
}

function openDeleteModal(id) {
    deleteId = id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function confirmDelete() {
    if (!deleteId) return;
    
    $.ajax({
        url: API_URL + `/attendance.php?id=${deleteId}`,
        type: 'DELETE',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showAlert('Data berhasil dihapus', 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                loadAttendanceData();
            }
        },
        error: function() {
            showAlert('Error menghapus data', 'danger');
        }
    });
}

function showAlert(message, type) {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
    $('#tbody-attendance').before(alertHtml);
    setTimeout(() => {
        $('.alert').fadeOut(() => $('.alert').remove());
    }, 5000);
}
