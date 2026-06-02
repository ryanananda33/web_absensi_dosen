// Schedule Management JavaScript
const API_URL = '../api';
const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

$(document).ready(function() {
    loadAllSchedules();
    
    $('#btn-save-schedule').click(saveSchedule);
});

function loadAllSchedules() {
    $.ajax({
        url: API_URL + '/schedule.php?action=all',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                renderSchedulesByDay(response.data);
            }
        },
        error: function() {
            showAlert('Error loading schedules', 'danger');
        }
    });
}

function renderSchedulesByDay(schedules) {
    DAYS.forEach(day => {
        const daySchedules = schedules.filter(s => s.hari === day);
        renderDaySchedules(day, daySchedules);
    });
}

function renderDaySchedules(day, schedules) {
    const containerId = `schedule-${day.toLowerCase()}`;
    const container = $(`#${containerId}`);
    
    if (schedules.length === 0) {
        container.html(`<div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x"></i>
                            <p>Tidak ada jadwal pada hari ini</p>
                        </div>`);
        return;
    }
    
    let html = '';
    schedules.forEach(schedule => {
        const typeColor = schedule.tipe === 'Praktikum' ? '#198754' : '#0d6efd';
        const typeLabel = schedule.tipe === 'Praktikum' ? 'Praktikum' : 'Teori';
        
        html += `
            <div class="schedule-item" style="border-left-color: ${typeColor};">
                <div class="schedule-item-time">${schedule.jam_mulai}</div>
                <div class="schedule-item-title">${schedule.matakuliah}</div>
                <div class="schedule-item-details">
                    <i class="bi bi-door-open"></i> ${schedule.ruangan} ${schedule.kelas ? ' - Kelas: ' + schedule.kelas : ''}
                </div>
                <div class="schedule-item-type" style="background-color: ${typeColor}20; color: ${typeColor}; border: 1px solid ${typeColor}40;">
                    ${typeLabel}
                </div>
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-warning" onclick="editSchedule(${schedule.id}, decodeURIComponent('${encodeURIComponent(schedule.matakuliah)}'), decodeURIComponent('${encodeURIComponent(schedule.hari)}'), decodeURIComponent('${encodeURIComponent(schedule.jam_mulai)}'), decodeURIComponent('${encodeURIComponent(schedule.ruangan)}'), decodeURIComponent('${encodeURIComponent(schedule.tipe)}'), decodeURIComponent('${encodeURIComponent(schedule.kelas || '')}'), decodeURIComponent('${encodeURIComponent(schedule.jurusan || '')}'), decodeURIComponent('${encodeURIComponent(schedule.angkatan || '')}'))">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(${schedule.id})">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function saveSchedule() {
    const form = document.getElementById('schedule-form');
    const formData = new FormData(form);
    const saveBtn = $('#btn-save-schedule');
    const id = saveBtn.attr('data-id');

    if (id) {
        // Update (PUT)
        const dataObj = Object.fromEntries(formData);
        $.ajax({
            url: API_URL + `/schedule.php?id=${id}`,
            type: 'PUT',
            data: dataObj,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('Jadwal berhasil diubah', 'success');
                    form.reset();
                    saveBtn.removeAttr('data-id');
                    saveBtn.text('Simpan');
                    bootstrap.Modal.getInstance(document.getElementById('addScheduleModal')).hide();
                    loadAllSchedules();
                }
            },
            error: function() {
                showAlert('Error updating schedule', 'danger');
            }
        });
    } else {
        $.ajax({
            url: API_URL + '/schedule.php',
            type: 'POST',
            data: Object.fromEntries(formData),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('Jadwal berhasil ditambahkan', 'success');
                    form.reset();
                    bootstrap.Modal.getInstance(document.getElementById('addScheduleModal')).hide();
                    loadAllSchedules();
                }
            },
            error: function() {
                showAlert('Error saving schedule', 'danger');
            }
        });
    }
}

function editSchedule(id, matakuliah, hari, jamMulai, ruangan, tipe, kelas = '', jurusan = '', angkatan = '') {
    // Pre-fill form
    $('input[name="matakuliah"]').val(matakuliah);
    $('select[name="hari"]').val(hari);
    $('input[name="jam_mulai"]').val(jamMulai);
    $('input[name="ruangan"]').val(ruangan);
    $('select[name="tipe"]').val(tipe);
    $('input[name="kelas"]').val(kelas);
    $('input[name="jurusan"]').val(jurusan);
    $('input[name="angkatan"]').val(angkatan);
    
    const saveBtn = $('#btn-save-schedule');
    saveBtn.attr('data-id', id);
    saveBtn.text('Ubah Jadwal');
    
    new bootstrap.Modal(document.getElementById('addScheduleModal')).show();
}

function deleteSchedule(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) return;
    
    $.ajax({
        url: API_URL + `/schedule.php?id=${id}`,
        type: 'DELETE',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showAlert('Jadwal berhasil dihapus', 'success');
                loadAllSchedules();
            }
        },
        error: function() {
            showAlert('Error deleting schedule', 'danger');
        }
    });
}

function showAlert(message, type) {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
    $('.container-fluid').prepend(alertHtml);
    setTimeout(() => {
        $('.alert').fadeOut(() => $('.alert').remove());
    }, 5000);
}
