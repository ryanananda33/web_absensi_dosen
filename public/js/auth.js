// Auth helper for dosen login/register synchronization
const AUTH_STORAGE_KEY = 'absensi_absensi_user';

function saveAuthUser(user) {
    localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(user));
}

function getAuthUser() {
    const raw = localStorage.getItem(AUTH_STORAGE_KEY);
    if (!raw) return null;
    try {
        return JSON.parse(raw);
    } catch (error) {
        return null;
    }
}

function clearAuthUser() {
    localStorage.removeItem(AUTH_STORAGE_KEY);
}

function logoutUser() {
    clearAuthUser();
    window.location.href = 'login.php';
}

function updateNavbarAuth() {
    const nav = document.getElementById('nav-auth-area');
    if (!nav) return;

    const user = getAuthUser();

    if (user) {
        nav.innerHTML = `
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> ${user.nama || user.nim_nik}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text">Role: ${user.role}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="javascript:logoutUser()">Logout</a></li>
                </ul>
            </li>
        `;
    } else {
        nav.innerHTML = `
            <li class="nav-item">
                <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register_dosen.php"><i class="bi bi-person-plus"></i> Register Dosen</a>
            </li>
        `;
    }
}

function ensureDosenLogin() {
    const user = getAuthUser();
    if (!user || user.role !== 'dosen') {
        window.location.href = 'login.php';
        return false;
    }
    return true;
}

function getCurrentUser() {
    return getAuthUser();
}

function updateDashboardUserInfo() {
    const info = document.getElementById('dashboard-user-info');
    if (!info) return;

    const user = getAuthUser();
    if (user) {
        info.innerHTML = `
            <div class="alert alert-info py-2 mb-4">
                <strong>Login sebagai:</strong> ${user.nama || user.nim_nik} <br>
                <strong>Role:</strong> ${user.role}
            </div>
        `;
    } else {
        info.innerHTML = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateNavbarAuth();
    updateDashboardUserInfo();
});
