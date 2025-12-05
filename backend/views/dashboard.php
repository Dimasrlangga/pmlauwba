<?php
// File: backend/views/dashboard.php
// Dashboard Backend dengan statistik dan chart

// Pastikan APP_ROOT tersedia
if (!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(__DIR__ . '/../..'));
}

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek login
if (!isset($_SESSION['logged_in'])) {
    header("Location: ?url=login");
    exit;
}

$judul_halaman = "Dashboard";
$nama_user = $_SESSION['nama_lengkap'] ?? 'User';
$role_user = $_SESSION['role'] ?? 'peserta';

// Load header & sidebar
$headerFile = APP_ROOT . '/backend/views/component/header.php';
$navbarFile = APP_ROOT . '/backend/views/component/navbar.php';
$sidebarFile = APP_ROOT . '/backend/views/component/sidebar.php';
$footerFile = APP_ROOT . '/backend/views/component/footer.php';

if (file_exists($headerFile)) include $headerFile;
else echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Dashboard</title></head><body>";
if (file_exists($navbarFile)) include $navbarFile;
if (file_exists($sidebarFile)) include $sidebarFile;

// Data dari index.php
$peserta_count = $peserta_count ?? 0;
$admin_count = $admin_count ?? 0;
$cnt_hadir = $cnt_hadir ?? 0;
$cnt_izin = $cnt_izin ?? 0;
$cnt_sakit = $cnt_sakit ?? 0;
$cnt_total = $cnt_total ?? 0;
$chart_peserta = $chart_peserta ?? array_fill(0, 12, 0);
$chart_log = $chart_log ?? array_fill(0, 12, 0);
$chart_presensi = $chart_presensi ?? array_fill(0, 12, 0);
?>

<style>
    .dashboard-card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        font-size: 3rem;
        opacity: 0.2;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0;
    }
    
    .stat-label {
        color: #777;
        font-size: 0.9rem;
        text-transform: uppercase;
    }
</style>

<div class="main-panel">
    <div class="container">
        <div class="page-inner">
            
            <!-- Page Header -->
            <div class="page-header">
                <h4 class="page-title"><?= htmlspecialchars($judul_halaman) ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="?url=dashboard_backend">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Dashboard</a></li>
                </ul>
            </div>

            <!-- Welcome Message -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</strong>
                        <br>
                        <small>Role: <?= htmlspecialchars(ucfirst($role_user)) ?> | Tanggal: <?= date('d F Y') ?></small>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                
                <!-- Card: Total Peserta -->
                <div class="col-sm-6 col-md-3">
                    <div class="card dashboard-card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category stat-label">Total Peserta</p>
                                        <h4 class="card-title stat-value"><?= number_format($peserta_count) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Total Admin -->
                <div class="col-sm-6 col-md-3">
                    <div class="card dashboard-card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category stat-label">Total Admin</p>
                                        <h4 class="card-title stat-value"><?= number_format($admin_count) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Hadir Hari Ini -->
                <div class="col-sm-6 col-md-3">
                    <div class="card dashboard-card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category stat-label">Hadir Hari Ini</p>
                                        <h4 class="card-title stat-value"><?= number_format($cnt_hadir) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Total Presensi -->
                <div class="col-sm-6 col-md-3">
                    <div class="card dashboard-card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category stat-label">Total Presensi</p>
                                        <h4 class="card-title stat-value"><?= number_format($cnt_total) ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Presensi Detail Cards -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-check text-success" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-0"><?= number_format($cnt_hadir) ?></h3>
                            <p class="text-muted">Hadir</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt text-warning" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-0"><?= number_format($cnt_izin) ?></h3>
                            <p class="text-muted">Izin</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="fas fa-hospital text-danger" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 mb-0"><?= number_format($cnt_sakit) ?></h3>
                            <p class="text-muted">Sakit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mt-4">
                
                <!-- Chart: Presensi Bulanan -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Presensi Tahun <?= date('Y') ?></h4>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPresensi" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chart: Peserta Bulanan -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Peserta Baru Tahun <?= date('Y') ?></h4>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPeserta" height="200"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Menu Cepat</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="?url=presensi_backend" class="btn btn-success btn-block btn-lg">
                                        <i class="fas fa-fingerprint"></i> Presensi
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="?url=log_harian" class="btn btn-info btn-block btn-lg">
                                        <i class="fas fa-book"></i> Log Harian
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="?url=laporan_presensi" class="btn btn-warning btn-block btn-lg">
                                        <i class="fas fa-chart-bar"></i> Laporan
                                    </a>
                                </div>
                                <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                <div class="col-md-3 mb-3">
                                    <a href="?url=kelola_user" class="btn btn-primary btn-block btn-lg">
                                        <i class="fas fa-users-cog"></i> Kelola User
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php
    if (file_exists($footerFile)) {
        include $footerFile;
    } else {
        echo "</div></body></html>";
    }
    ?>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Data dari PHP
    const chartPresensiData = <?= json_encode($chart_presensi) ?>;
    const chartPesertaData = <?= json_encode($chart_peserta) ?>;
    
    const bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // Chart Presensi
    const ctxPresensi = document.getElementById('chartPresensi');
    if (ctxPresensi) {
        new Chart(ctxPresensi, {
            type: 'bar',
            data: {
                labels: bulanLabels,
                datasets: [{
                    label: 'Presensi Hadir',
                    data: chartPresensiData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Chart Peserta
    const ctxPeserta = document.getElementById('chartPeserta');
    if (ctxPeserta) {
        new Chart(ctxPeserta, {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [{
                    label: 'Peserta Baru',
                    data: chartPesertaData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
</script>