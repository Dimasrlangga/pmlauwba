<?php
session_start();

// 1. Definisi Root Path & Assets
define('APP_ROOT', dirname(__DIR__));
// Sesuaikan base URL assets Anda jika perlu
$assets = "http://localhost/pmlauwba/backend";

// 2. Koneksi Database
require_once APP_ROOT . '/database.php';

// 3. Router
$page = $_GET['page'] ?? $_GET['url'] ?? 'dashboard_backend';

// 4. Force Login
if (!isset($_SESSION['logged_in']) && $page !== 'login' && $page !== 'auth_process') {
    header("Location: ?page=login");
    exit;
}

// 5. Load Controllers
require_once 'controllers/SuperuserController.php';
require_once 'controllers/AuthController.php';

$authController = new AuthController($koneksi);
$superuserController = new SuperuserController($koneksi);

// ==========================================
// FUNGSI BANTUAN (HELPER)
// ==========================================
if (!function_exists('getMonthlyStats')) {
    function getMonthlyStats($koneksi, $table, $dateColumn, $condition = "")
    {
        $data = array_fill(0, 12, 0);
        $year = date('Y');
        $sql = "SELECT MONTH($dateColumn) as bulan, COUNT(*) as total 
                FROM $table 
                WHERE YEAR($dateColumn) = '$year' $condition 
                GROUP BY MONTH($dateColumn)";
        $result = mysqli_query($koneksi, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[intval($row['bulan']) - 1] = intval($row['total']);
            }
        }
        return $data;
    }
}

if (!function_exists('get_presensi_count')) {
    function get_presensi_count($koneksi, $status = null)
    {
        if (!($koneksi instanceof mysqli)) return 0;
        $date = date('Y-m-d'); // Gunakan format tanggal aman
        if ($status === null) {
            $sql = "SELECT COUNT(*) AS cnt FROM presensi WHERE tanggal = '$date'";
        } else {
            $status = mysqli_real_escape_string($koneksi, $status);
            $sql = "SELECT COUNT(*) AS cnt FROM presensi WHERE tanggal = '$date' AND status_presensi = '$status'";
        }
        $stmt = mysqli_query($koneksi, $sql);
        $r = mysqli_fetch_assoc($stmt);
        return intval($r['cnt'] ?? 0);
    }
}

// ==========================================
// ROUTING & LOGIC
// ==========================================
switch ($page) {
    // --- AUTH ---
    case 'login':
        $authController->showBackendLoginForm();
        exit;
    case 'auth_process':
        $authController->processLogin();
        exit;
    case 'logout':
        $authController->logout();
        exit;

        // --- SUPERUSER ---
    case 'kelola_user':
        $superuserController->kelolaUser();
        exit;
    case 'tambah_user':
        $superuserController->showTambahForm();
        exit;
    case 'proses_tambah_user':
        $superuserController->prosesTambahUser();
        exit;
    case 'edit_user':
        $superuserController->showEditForm();
        exit;
    case 'proses_edit_user':
        $superuserController->prosesEditUser();
        exit;
    case 'hapus_user':
        $superuserController->prosesHapusUser();
        exit;

        // --- FEATURES ---
    case 'presensi':
        require_once 'views/pages/presensi/presensi.php';
        exit;
    case 'log_harian':
        require_once 'views/pages/log_harian/log_harian.php';
        exit;
    case 'kelola_izin':
        require_once 'views/pages/kelola_izin/kelola_izin.php';
        exit;
    case 'laporan_presensi':
        require_once 'views/pages/laporan_presensi/laporan_presensi.php';
        exit;

        // --- DASHBOARD (View Logic) ---
    case 'dashboard_backend':
        // 1. Data Peserta
        $peserta_count = 0;
        $res = mysqli_query($koneksi, "SELECT COUNT(*) AS cnt FROM users WHERE role = 'peserta'");
        if ($res) $peserta_count = mysqli_fetch_assoc($res)['cnt'];

        // 2. Data Admin
        $admin_count = 0;
        $res_admin = mysqli_query($koneksi, "SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin'");
        if ($res_admin) $admin_count = mysqli_fetch_assoc($res_admin)['cnt'];

        // 3. Data Presensi Hari Ini
        $cnt_hadir = get_presensi_count($koneksi, 'hadir');
        $cnt_izin  = get_presensi_count($koneksi, 'izin');
        $cnt_sakit = get_presensi_count($koneksi, 'sakit');
        $cnt_total = get_presensi_count($koneksi, null); // Total aktivitas hari ini

        // 4. Data Chart (Tahunan)
        $chart_peserta  = getMonthlyStats($koneksi, "users", "created_at", "AND role='peserta'");
        $chart_log      = getMonthlyStats($koneksi, "log_harian", "tanggal");
        $chart_presensi = getMonthlyStats($koneksi, "presensi", "tanggal", "AND status_presensi='hadir'");

        // Lanjut ke HTML di bawah...
        break;

    default:
        echo "<script>alert('Halaman tidak ditemukan'); window.location='?page=dashboard_backend';</script>";
        exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'views/component/header.php'; ?>

<body>
    <div class="wrapper">

        <?php include 'views/component/sidebar.php'; ?>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
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
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <?php include 'views/component/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <div
                        class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Dashboard</h3>
                            <h6 class="op-7 mb-2">Berikut Beberapa Data Yang Tersedia:</h6>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="?page=kelola_user" class="btn btn-label-info btn-round me-2">Manage User</a>
                            <a href="?page=presensi" class="btn btn-primary btn-round">Presensi</a>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                        $peserta_count = 0;
                        if (isset($koneksi) && ($koneksi instanceof mysqli)) {
                            $sql = "SELECT COUNT(*) AS cnt FROM users WHERE role = 'peserta'";
                            $res = mysqli_query($koneksi, $sql);
                            if ($res) {
                                $row = mysqli_fetch_assoc($res);
                                $peserta_count = intval($row['cnt'] ?? 0);
                            } else {
                                $peserta_count = null;
                                $db_error = "Query error: " . mysqli_error($koneksi);
                            }
                        }

                        $admin_count = 0;
                        if (isset($koneksi) && ($koneksi instanceof mysqli)) {
                            $sql_admin = "SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin'";
                            $res_admin = mysqli_query($koneksi, $sql_admin);
                            if ($res_admin) {
                                $row_admin = mysqli_fetch_assoc($res_admin);
                                $admin_count = intval($row_admin['cnt'] ?? 0);
                            } else {
                                $admin_count = null;
                                $db_error_admin = "Query error: " . mysqli_error($koneksi);
                            }
                        }

                        // Fungsi bantu query count
                        function get_presensi_count($koneksi, $status = null)
                        {
                            if (!($koneksi instanceof mysqli)) return null;
                            if ($status === null) {
                                $sql = "SELECT COUNT(*) AS cnt FROM presensi WHERE tanggal = CURDATE()";
                                $stmt = mysqli_query($koneksi, $sql);
                            } else {
                                $sql = "SELECT COUNT(*) AS cnt FROM presensi WHERE tanggal = CURDATE() AND status_presensi = '" . mysqli_real_escape_string($koneksi, $status) . "'";
                                $stmt = mysqli_query($koneksi, $sql);
                            }
                            if (!$stmt) return null;
                            $r = mysqli_fetch_assoc($stmt);
                            return intval($r['cnt'] ?? 0);
                        }

                        // Ambil counts
                        $cnt_hadir = get_presensi_count($koneksi, 'hadir');
                        $cnt_izin = get_presensi_count($koneksi, 'izin');
                        $cnt_sakit = get_presensi_count($koneksi, 'sakit');
                        $cnt_total = get_presensi_count($koneksi, null);

                        // Format label
                        $peserta_label = is_null($peserta_count) ? '-' : number_format($peserta_count);
                        $admin_label = is_null($admin_count) ? '-' : number_format($admin_count);
                        $label_hadir = is_null($cnt_hadir) ? '-' : number_format($cnt_hadir);
                        $label_izin = is_null($cnt_izin) ? '-' : number_format($cnt_izin);
                        $label_sakit = is_null($cnt_sakit) ? '-' : number_format($cnt_sakit);
                        $label_total = is_null($cnt_total) ? '-' : number_format($cnt_total);

                        $db_error_presensi = $db_error_presensi ?? null;
                        ?>

                        <!-- Baris Pertama: Peserta, Admin, Hadir -->
                        <div class="row">
                            <!-- Peserta Aktif -->
                            <div class="col-sm-6 col-md-4">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Peserta Aktif</p>
                                                    <h4 class="card-title"><?= htmlspecialchars($peserta_label, ENT_QUOTES, 'UTF-8') ?></h4>
                                                    <?php if (!empty($db_error)): ?>
                                                        <small class="text-danger">Error: <?= htmlspecialchars($db_error) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Aktif -->
                            <div class="col-sm-6 col-md-4">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Admin Aktif</p>
                                                    <h4 class="card-title"><?= htmlspecialchars($admin_label, ENT_QUOTES, 'UTF-8') ?></h4>
                                                    <?php if (!empty($db_error_admin)): ?>
                                                        <small class="text-danger">Error: <?= htmlspecialchars($db_error_admin) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Total Presensi -->
                            <div class="col-sm-6 col-md-4">
                                <a href="?url=laporan_presensi" style="text-decoration:none;">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                        <i class="fas fa-chart-line"></i>
                                                    </div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Total Presensi (Hari ini)</p>
                                                        <h4 class="card-title"><?= htmlspecialchars($label_total, ENT_QUOTES, 'UTF-8') ?></h4>
                                                        <small class="text-muted"><?= htmlspecialchars(date('d M Y')) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>


                        <!-- Baris Kedua: Izin, Sakit, Total Presensi -->
                        <div class="row">
                            <!-- Hadir -->
                            <div class="col-sm-6 col-md-4">
                                <a href="?url=laporan_presensi&filter=hadir" style="text-decoration:none;">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                                        <i class="fas fa-check-circle"></i>
                                                    </div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Hadir (Hari ini)</p>
                                                        <h4 class="card-title"><?= htmlspecialchars($label_hadir, ENT_QUOTES, 'UTF-8') ?></h4>
                                                        <small class="text-muted"><?= htmlspecialchars(date('d M Y')) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Izin -->
                            <div class="col-sm-6 col-md-4">
                                <a href="?url=laporan_presensi&filter=izin" style="text-decoration:none;">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                        <i class="fas fa-file-alt"></i>
                                                    </div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Izin (Hari ini)</p>
                                                        <h4 class="card-title"><?= htmlspecialchars($label_izin, ENT_QUOTES, 'UTF-8') ?></h4>
                                                        <small class="text-muted"><?= htmlspecialchars(date('d M Y')) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Sakit -->
                            <div class="col-sm-6 col-md-4">
                                <a href="?url=laporan_presensi&filter=sakit" style="text-decoration:none;">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                        <i class="fas fa-procedures"></i>
                                                    </div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category">Sakit (Hari ini)</p>
                                                        <h4 class="card-title"><?= htmlspecialchars($label_sakit, ENT_QUOTES, 'UTF-8') ?></h4>
                                                        <small class="text-muted"><?= htmlspecialchars(date('d M Y')) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($db_error_presensi)): ?>
                            <div class="alert alert-warning mt-2">Warning: <?= htmlspecialchars($db_error_presensi, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-round">
                                    <div class="card-header">
                                        <div class="card-head-row">
                                            <div class="card-title">Statistik Sistem (Tahun <?= date('Y') ?>)</div>
                                            <div class="card-tools">
                                                <a href="#" class="btn btn-label-success btn-round btn-sm me-2">
                                                    <span class="btn-label"><i class="fa fa-pencil"></i></span> Export
                                                </a>
                                                <a href="#" class="btn btn-label-info btn-round btn-sm">
                                                    <span class="btn-label"><i class="fa fa-print"></i></span> Print
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container" style="min-height: 375px">
                                            <canvas id="statisticsChart"></canvas>
                                        </div>
                                        <!-- <div id="myChartLegend"></div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="col-md-4">
                        <div class="card card-round">
                            <div class="card-body">
                                <div class="card-head-row card-tools-still-right">
                                    <div class="card-title">New Customers</div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-icon btn-clean me-0"
                                                type="button"
                                                id="dropdownMenuButton"
                                                data-bs-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-list py-4">
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img
                                                src="<?= $assets ?>/assets/img/jm_denis.jpg"
                                                alt="..."
                                                class="avatar-img rounded-circle" />
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">Jimmy Denis</div>
                                            <div class="status">Graphic Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-link op-8 me-1">
                                            <i class="far fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-icon btn-link btn-danger op-8">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <span
                                                class="avatar-title rounded-circle border border-white">CF</span>
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">Chandra Felix</div>
                                            <div class="status">Sales Promotion</div>
                                        </div>
                                        <button class="btn btn-icon btn-link op-8 me-1">
                                            <i class="far fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-icon btn-link btn-danger op-8">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img
                                                src="<?= $assets ?>/assets/img/talha.jpg"
                                                alt="..."
                                                class="avatar-img rounded-circle" />
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">Talha</div>
                                            <div class="status">Front End Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-link op-8 me-1">
                                            <i class="far fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-icon btn-link btn-danger op-8">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <img
                                                src="<?= $assets ?>/assets/img/chadengle.jpg"
                                                alt="..."
                                                class="avatar-img rounded-circle" />
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">Chad</div>
                                            <div class="status">CEO Zeleaf</div>
                                        </div>
                                        <button class="btn btn-icon btn-link op-8 me-1">
                                            <i class="far fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-icon btn-link btn-danger op-8">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <span
                                                class="avatar-title rounded-circle border border-white bg-primary">H</span>
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">Hizrian</div>
                                            <div class="status">Web Designer</div>
                                        </div>
                                        <button class="btn btn-icon btn-link op-8 me-1">
                                            <i class="far fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-icon btn-link btn-danger op-8">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                    <div class="item-list">
                                        <div class="avatar">
                                            <span
                                                class="avatar-title rounded-circle border border-white bg-secondary">F</span>
                                        </div>
                                        <div class="info-user ms-3">
                                            <div class="username">Farrah</div>
                                            <div class="status">Marketing</div>
                                        </div>
                                        <button class="btn btn-icon btn-link op-8 me-1">
                                            <i class="far fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-icon btn-link btn-danger op-8">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                            <!-- <div class="col-md-8">
                        <div class="card card-round">
                            <div class="card-header">
                                <div class="card-head-row card-tools-still-right">
                                    <div class="card-title">Transaction History</div>
                                    <div class="card-tools">
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-icon btn-clean me-0"
                                                type="button"
                                                id="dropdownMenuButton"
                                                data-bs-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div
                                                class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#">Action</a>
                                                <a class="dropdown-item" href="#">Another action</a>
                                                <a class="dropdown-item" href="#">Something else here</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    
                                    <table class="table align-items-center mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">Payment Number</th>
                                                <th scope="col" class="text-end">Date & Time</th>
                                                <th scope="col" class="text-end">Amount</th>
                                                <th scope="col" class="text-end">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <button
                                                        class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                    Payment from #10231
                                                </th>
                                                <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                                <td class="text-end">$250.00</td>
                                                <td class="text-end">
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> -->
                        </div>
                    </div>
                </div>
                <?php include 'views/component/footer.php'; ?>

            </div>

            <!-- Custom template | don't include it in your project! -->
            <div class="custom-template">
                <div class="title">Settings</div>
                <div class="custom-content">
                    <div class="switcher">
                        <div class="switch-block">
                            <h4>Logo Header</h4>
                            <div class="btnSwitch">
                                <button
                                    type="button"
                                    class="selected changeLogoHeaderColor"
                                    data-color="dark"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="blue"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="purple"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="light-blue"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="green"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="orange"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="red"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="white"></button>
                                <br />
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="dark2"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="blue2"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="purple2"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="light-blue2"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="green2"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="orange2"></button>
                                <button
                                    type="button"
                                    class="changeLogoHeaderColor"
                                    data-color="red2"></button>
                            </div>
                        </div>
                        <div class="switch-block">
                            <h4>Navbar Header</h4>
                            <div class="btnSwitch">
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="dark"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="blue"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="purple"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="light-blue"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="green"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="orange"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="red"></button>
                                <button
                                    type="button"
                                    class="selected changeTopBarColor"
                                    data-color="white"></button>
                                <br />
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="dark2"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="blue2"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="purple2"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="light-blue2"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="green2"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="orange2"></button>
                                <button
                                    type="button"
                                    class="changeTopBarColor"
                                    data-color="red2"></button>
                            </div>
                        </div>
                        <div class="switch-block">
                            <h4>Sidebar</h4>
                            <div class="btnSwitch">
                                <button
                                    type="button"
                                    class="changeSideBarColor"
                                    data-color="white"></button>
                                <button
                                    type="button"
                                    class="selected changeSideBarColor"
                                    data-color="dark"></button>
                                <button
                                    type="button"
                                    class="changeSideBarColor"
                                    data-color="dark2"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custom-toggle">
                    <i class="icon-settings"></i>
                </div>
            </div>
            <!-- End Custom template -->
        </div>
        <?php include 'views/component/script.php'; ?>


        <!-- js untuk widget card -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var chartCanvas = document.getElementById('statisticsChart');
                if (!chartCanvas) return;

                var ctx = chartCanvas.getContext('2d');

                // Data dari PHP
                var dataPeserta = <?= json_encode($chart_peserta) ?>;
                var dataLog = <?= json_encode($chart_log) ?>;
                var dataPresensi = <?= json_encode($chart_presensi) ?>;

                var statisticsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                        datasets: [{
                            label: "Peserta Baru",
                            borderColor: '#f3545d',
                            pointBackgroundColor: 'rgba(243, 84, 93, 0.6)',
                            pointRadius: 0,
                            backgroundColor: 'rgba(243, 84, 93, 0.4)',
                            legendColor: '#f3545d',
                            fill: true,
                            borderWidth: 2,
                            data: dataPeserta
                        }, {
                            label: "Log Aktivitas",
                            borderColor: '#fdaf4b',
                            pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
                            pointRadius: 0,
                            backgroundColor: 'rgba(253, 175, 75, 0.4)',
                            legendColor: '#fdaf4b',
                            fill: true,
                            borderWidth: 2,
                            data: dataLog
                        }, {
                            label: "Presensi Hadir",
                            borderColor: '#177dff',
                            pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
                            pointRadius: 0,
                            backgroundColor: 'rgba(23, 125, 255, 0.4)',
                            legendColor: '#177dff',
                            fill: true,
                            borderWidth: 2,
                            data: dataPresensi
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        tooltips: {
                            bodySpacing: 4,
                            mode: "nearest",
                            intersect: 0,
                            position: "nearest",
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10
                        },
                        layout: {
                            padding: {
                                left: 15,
                                right: 15,
                                top: 15,
                                bottom: 15
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    fontStyle: "500",
                                    beginAtZero: true,
                                    maxTicksLimit: 5,
                                    padding: 10
                                },
                                gridLines: {
                                    drawTicks: false,
                                    display: false
                                }
                            }],
                            xAxes: [{
                                gridLines: {
                                    zeroLineColor: "transparent"
                                },
                                ticks: {
                                    padding: 10,
                                    fontStyle: "500"
                                }
                            }]
                        },
                        legendCallback: function(chart) {
                            var text = [];
                            text.push('<ul class="' + chart.id + '-legend html-legend">');
                            for (var i = 0; i < chart.data.datasets.length; i++) {
                                text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>');
                                if (chart.data.datasets[i].label) {
                                    text.push(chart.data.datasets[i].label);
                                }
                                text.push('</li>');
                            }
                            text.push('</ul>');
                            return text.join('');
                        }
                    }
                });

                // Generate Legend
                var myLegendContainer = document.getElementById("myChartLegend");
                if (myLegendContainer) {
                    myLegendContainer.innerHTML = statisticsChart.generateLegend();
                }
            });
        </script>
</body>

</html>