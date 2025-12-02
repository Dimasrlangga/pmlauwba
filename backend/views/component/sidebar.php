<?php
include __DIR__ . '/path.php';

// Ambil role dari session agar kodenya lebih pendek
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="?page=dashboard_backend" class="logo">
                <img
                    src="<?= $assets ?>/assets/img/kaiadmin/logo_light.svg"
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

                <li class="nav-item <?= ($page == 'dashboard_backend') ? 'active' : '' ?>">
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

                <?php if ($role == 'superuser'): ?>
                    <li class="nav-item <?= ($page == 'kelola_user' || $page == 'tambah_user' || $page == 'edit_user') ? 'active' : '' ?>">
                        <a href="?page=kelola_user">
                            <i class="fas fa-users-cog"></i>
                            <p>Kelola User</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array($role, ['superuser', 'admin'])): ?>

                    <li class="nav-item <?= ($page == 'presensi') ? 'active' : '' ?>">
                        <a href="?page=presensi">
                            <i class="fas fa-fingerprint"></i>
                            <p>Input Presensi</p>
                        </a>
                    </li>

                    <li class="nav-item <?= ($page == 'kelola_progress') ? 'active' : '' ?>">
                        <a href="?page=kelola_progress">
                            <i class="fas fa-tasks"></i>
                            <p>Kelola Progress</p>
                        </a>
                    </li>

                <?php endif; ?>

                <?php
                // Cek apakah halaman yang aktif adalah salah satu submenu laporan
                $is_laporan_active = in_array($page, ['log_harian', 'kelola_izin', 'laporan_presensi']);
                $show_laporan = ($is_laporan_active) ? 'show' : '';
                ?>

                <li class="nav-item <?= $is_laporan_active ? 'active' : '' ?>">
                    <a data-bs-toggle="collapse" href="#laporan">
                        <i class="far fa-chart-bar"></i>
                        <p>Laporan & Log</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?= $show_laporan ?>" id="laporan">
                        <ul class="nav nav-collapse">
                            <li class="<?= ($page == 'log_harian') ? 'active' : '' ?>">
                                <a href="?page=log_harian">
                                    <span class="sub-item">Log Aktivitas Harian</span>
                                </a>
                            </li>

                            <li class="<?= ($page == 'kelola_izin') ? 'active' : '' ?>">
                                <a href="?page=kelola_izin">
                                    <span class="sub-item">Data Izin & Sakit</span>
                                </a>
                            </li>

                            <li class="<?= ($page == 'laporan_presensi') ? 'active' : '' ?>">
                                <a href="?page=laporan_presensi">
                                    <span class="sub-item">Laporan Presensi</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>


