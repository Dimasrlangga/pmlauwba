<?php
// backend/views/component/sidebar.php
// Sidebar dengan fallback aman untuk $page dan session

// Pastikan session aktif (jika belum)
if (!session_id()) session_start();

// Path constants / assets - file path.php biasanya mendefinisikan $assets
$pathFile = __DIR__ . '/path.php';
if (file_exists($pathFile)) {
    include $pathFile;
} else {
    // Fallback jika tidak ada path.php
    $assets = '/backend/views/component'; // ubah jika perlu
}

// Ambil role dari session agar kodenya lebih pendek
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Buat fallback untuk $page jika belum didefinisikan
// Prioritas: variabel $page (jika di-include dari controller), lalu $_GET['page'], lalu $_GET['url'], lalu empty string
$currentPage = '';
if (isset($page) && is_string($page)) {
    $currentPage = $page;
} elseif (isset($_GET['page'])) {
    $currentPage = $_GET['page'];
} elseif (isset($_GET['url'])) {
    $currentPage = $_GET['url'];
} else {
    $currentPage = '';
}

// Helper kecil untuk cek active class
function is_active($currentPage, $names)
{
    // $names bisa string atau array
    if (is_array($names)) {
        return in_array($currentPage, $names);
    }
    return ($currentPage === $names);
}
?>

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="?page=dashboard_backend" class="logo">
                <img
                    src="<?= htmlspecialchars($assets ?? '') ?>/assets/img/kaiadmin/logo_light.svg"
                    alt="navbar brand"
                    class="navbar-brand"
                    height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <li class="nav-item <?= is_active($currentPage, 'dashboard_backend') ? 'active' : '' ?>">
                    <a href="?page=dashboard_backend">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu Utama</h4>
                </li>

                <?php if ($role === 'superuser'): ?>
                    <li class="nav-item <?= is_active($currentPage, ['kelola_user', 'tambah_user', 'edit_user']) ? 'active' : '' ?>">
                        <a href="?page=kelola_user">
                            <i class="fas fa-users-cog"></i>
                            <p>Kelola User</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array($role, ['superuser', 'admin'])): ?>

                    <li class="nav-item <?= is_active($currentPage, 'presensi') ? 'active' : '' ?>">
                        <a href="?page=presensi">
                            <i class="fas fa-fingerprint"></i>
                            <p>Input Presensi</p>
                        </a>
                    </li>



                <?php endif; ?>

                <?php
                // Cek apakah halaman yang aktif adalah salah satu submenu laporan
                $is_laporan_active = is_active($currentPage, ['log_harian', 'kelola_izin', 'laporan_presensi']);
                $show_laporan = $is_laporan_active ? 'show' : '';
                ?>

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Laporan dan Log</h4>
                </li>
                <!-- MENU LAPORAN - TANPA DROPDOWN -->
                <li class="nav-item <?= is_active($currentPage, 'log_harian') ? 'active' : '' ?>">
                    <a href="?page=log_harian">
                        <i class="fas fa-users-cog"></i>
                        <p>Log Aktivitas Harian</p>
                    </a>
                </li>
                <li class="nav-item <?= is_active($currentPage, 'kelola_izin') ? 'active' : '' ?>">
                    <a href="?page=kelola_izin">
                        <i class="fas fa-notes-medical"></i>
                        <p>Data Izin & Sakit</p>
                    </a>
                </li>
                <li class="nav-item <?= is_active($currentPage, 'laporan_presensi') ? 'active' : '' ?>">
                    <a href="?page=laporan_presensi">
                        <i class="far fa-chart-bar"></i>
                        <p>Laporan Presensi</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>