// Users Management JavaScript
const API_URL = '../api';

$(document).ready(function() {
    loadUsersData();
    
    $('#filter-role').change(loadUsersData);
    $('#filter-status').change(loadUsersData);
});

// bind after DOM ready
$(document).on('click', '#btn-add-user', function() {
    openCreateModal();
});
$(document).on('click', '#btn-save-user', function() {
    submitUserForm();
});

function loadUsersData() {
    const role = $('#filter-role').val();
    const status = $('#filter-status').val();
    
    let url = API_URL + '/register.php?action=list';
    if (role) url += `&role=${role}`;
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                filterAndRenderUsers(response.data, status);
            }
        },
        error: function() {
            showAlert('Error loading data', 'danger');
        }
    });
}

function filterAndRenderUsers(data, status) {
    let filteredData = data.filter(user => {
        return !status || user.status_akun === status;
    });
    
    renderUsersTable(filteredData);
}

function renderUsersTable(data) {
    const tbody = $('#tbody-users');
    
    if (data.length === 0) {
        tbody.html(`<tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox"></i>
                            <p>Tidak ada data pengguna</p>
                        </td>
                    </tr>`);
        return;
    }
    
    let html = '';
    data.forEach(user => {
        const statusBadge = user.status_akun === 'aktif' 
            ? '<span class="badge bg-success">Aktif</span>' 
            : '<span class="badge bg-warning">Pending</span>';
        const roleBadge = user.role === 'mahasiswa'
            ? '<span class="badge bg-info">Mahasiswa</span>'
            : '<span class="badge bg-primary">Dosen</span>';
        
        html += `<tr>
                    <td>${user.nim_nik}</td>
                    <td>${user.nama}</td>
                    <td>${user.jurusan || '-'}</td>
                    <td>${user.kelas || '-'}</td>
                    <td>${roleBadge}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewDetail(${user.id})">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                    </td>
                </tr>`;
    });
    
    tbody.html(html);
}

function viewDetail(userId) {
    $.ajax({
        url: API_URL + `/auth.php?action=profile&user_id=${userId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                renderDetailModal(response.data);
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            }
        },
        error: function() {
            showAlert('Error loading user detail', 'danger');
        }
    });
}

function renderDetailModal(user) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Informasi Pribadi</h6>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold">Nama</td>
                        <td>${user.nama}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">NIM/NIK</td>
                        <td>${user.nim_nik}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Jenis Kelamin</td>
                        <td>${user.gender || '-'}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Akademik</h6>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold">Jurusan</td>
                        <td>${user.jurusan || '-'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Kelas</td>
                        <td>${user.kelas || '-'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Angkatan</td>
                        <td>${user.angkatan || '-'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Semester</td>
                        <td>${user.semester || '-'}</td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <h6 class="text-muted mb-3">Status</h6>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold">Role</td>
                        <td>${user.role === 'mahasiswa' ? 'Mahasiswa' : 'Dosen'}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status Akun</td>
                        <td>${user.status_akun === 'aktif' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-warning">Pending</span>'}</td>
                    </tr>
                </table>
            </div>
        </div>
    `;
    
    // add action buttons
    html += `
        <div class="mt-3 text-end">
            <button class="btn btn-sm btn-primary me-2" onclick="openEditModal(${user.id})"><i class="bi bi-pencil"></i> Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})"><i class="bi bi-trash"></i> Hapus</button>
        </div>
    `;

    $('#detail-content').html(html);
}

function openCreateModal() {
    $('#userModalTitle').text('Tambah Pengguna');
    $('#user-form')[0].reset();
    $('#user_id').val('');
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function openEditModal(userId) {
    $.ajax({
        url: API_URL + `/auth.php?action=profile&user_id=${userId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const u = response.data;
                $('#userModalTitle').text('Edit Pengguna');
                $('#user_id').val(u.id);
                $('#nim_nik').val(u.nim_nik);
                $('#nama').val(u.nama);
                $('#gender').val(u.gender);
                $('#jurusan').val(u.jurusan);
                $('#tempat_lahir').val(u.tempat_lahir);
                $('#tanggal_lahir').val(u.tanggal_lahir);
                $('#device_id').val(u.device_id);
                $('#role').val(u.role);
                $('#status_akun').val(u.status_akun);
                new bootstrap.Modal(document.getElementById('userModal')).show();
            }
        },
        error: function() {
            showAlert('Error loading user for edit', 'danger');
        }
    });
}

function submitUserForm() {
    const form = document.getElementById('user-form');
    const formData = new FormData(form);
    const userId = $('#user_id').val();

    if (userId) {
        formData.append('action', 'update');
        formData.append('user_id', userId);
    }

    fetch(API_URL + '/register.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            // hide user modal safely
            const userModalEl = document.getElementById('userModal');
            const userModalInst = bootstrap.Modal.getInstance(userModalEl) || new bootstrap.Modal(userModalEl);
            userModalInst.hide();
            loadUsersData();
        } else {
            showAlert(data.message || 'Gagal menyimpan user', 'danger');
        }
    })
    .catch(() => showAlert('Network error', 'danger'));
}

function deleteUser(userId) {
    if (!confirm('Yakin ingin menghapus user ini?')) return;

    const body = new URLSearchParams();
    body.append('action', 'delete');
    body.append('user_id', userId);

    fetch(API_URL + '/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            loadUsersData();
            // close detail modal safely
            const detailEl = document.getElementById('detailModal');
            const detailInst = bootstrap.Modal.getInstance(detailEl) || new bootstrap.Modal(detailEl);
            detailInst.hide();
        } else {
            showAlert(data.message || 'Gagal menghapus user', 'danger');
        }
    })
    .catch(() => showAlert('Network error', 'danger'));
}

function showAlert(message, type) {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`;
    $('.container-fluid').prepend(alertHtml);
    setTimeout(() => {
        $('.alert').fadeOut(() => $('.alert').remove());
    }, 5000);
}
