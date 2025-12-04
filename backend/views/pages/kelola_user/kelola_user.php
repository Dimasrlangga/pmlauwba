<?php
// backend/views/pages/kelola_user/kelola_user.php
// Versi robust: bisa di-include oleh controller (menggunakan $data_users)
// atau dibuka langsung via browser (akan melakukan bootstrap minimal).

// 1) Pastikan APP_ROOT terdefinisi — buat fallback bila perlu
if (!defined('APP_ROOT')) {
    // __DIR__ => backend/views/pages/kelola_user
    $root = realpath(__DIR__ . '/../../../../'); // naik 4 level ke project root
    if ($root) define('APP_ROOT', $root);
    else define('APP_ROOT', __DIR__); // fallback paling aman
}

// 2) Jika file dipanggil langsung (bukan via controller), pastikan $data_users tersedia
$bootstrapped_here = false;
if (!isset($data_users)) {
    $bootstrapped_here = true;

    // Jika ada file database.php di root, include itu (harus membuat $koneksi)
    $dbFile = APP_ROOT . '/database.php';
    if (file_exists($dbFile)) {
        require_once $dbFile;
        // database.php diharapkan membuat $koneksi (mysqli)
    } else {
        // fallback: coba koneksi manual (sesuaikan jika perlu)
        $host = '127.0.0.1';
        $user = 'root';
        $pass = '';
        $dbname = 'pmlauwba';
        $koneksi = new mysqli($host, $user, $pass, $dbname);
        if ($koneksi->connect_errno) {
            die("Koneksi DB gagal (fallback): " . $koneksi->connect_error);
        }
    }

    // Pastikan model User ada (path sesuai strukturmu: /models/User.php)
    $userModelPathCandidates = [
        APP_ROOT . '/models/User.php',
        APP_ROOT . '/app/models/User.php',
        APP_ROOT . '/backend/models/User.php'
    ];
    $userModelIncluded = false;
    foreach ($userModelPathCandidates as $p) {
        if (file_exists($p)) {
            require_once $p;
            $userModelIncluded = true;
            break;
        }
    }
    if (!$userModelIncluded) {
        // jika model belum ketemu, tampilkan pesan dan buat halaman kosong
        $data_users = [];
        $model_error_msg = "Model User.php tidak ditemukan. Dicari di: " . implode(', ', $userModelPathCandidates);
    } else {
        // buat instance model jika class User ada
        if (class_exists('User')) {
            // pastikan $koneksi variable ada
            if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
                // jika koneksi belum benar, coba ambil dari file 'database.php' jika ada
                // (tapi sudah ditangani di atas)
            }
            $um = new User($koneksi);
            $data_users = $um->getAll();
        } else {
            $data_users = [];
            $model_error_msg = "Class User tidak ditemukan di model yang di-include.";
        }
    }
}

// 3) Path component (header/sidebar/footer)
$headerPath  = APP_ROOT . '/backend/views/component/header.php';
$navbarPath  = APP_ROOT . '/backend/views/component/navbar.php';
$sidebarPath = APP_ROOT . '/backend/views/component/sidebar.php';
$footerPath  = APP_ROOT . '/backend/views/component/footer.php';

// Include header & sidebar (jika ada). Jika tidak ada, tetap lanjut.
if (file_exists($headerPath)) include $headerPath;
else {
    echo "<!-- header not found: {$headerPath} -->";
}
if (file_exists($navbarPath)) include $navbarPath;
else {
    echo "<!-- navbar not found: {$navbarPath} -->";
}
if (file_exists($sidebarPath)) include $sidebarPath;
else {
    echo "<!-- sidebar not found: {$sidebarPath} -->";
}

// 4) Pastikan $data_users ada sebagai array
if (!isset($data_users) || !is_array($data_users)) $data_users = [];
?>
<div class="main-panel">
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">Kelola User</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Kelola User</a></li>
                </ul>
            </div>

            <?php
            if (isset($_GET['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
            }
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            // jika ada pesan error saat bootstrap, tampilkan (hanya bila dibuka langsung)
            if (!empty($model_error_msg) && $bootstrapped_here) {
                echo '<div class="alert alert-warning">Warning: ' . htmlspecialchars($model_error_msg) . '</div>';
            }
            ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Daftar User Sistem</h4>
                                <a href="/pmlauwba/?url=tambah_user" class="btn btn-primary btn-round ms-auto">
                                    <i class="fa fa-plus"></i> Tambah User
                                </a>

                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($data_users)): $no = 1; ?>
                                            <?php foreach ($data_users as $user): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                                    <td>
                                                        <?php
                                                        $badge = 'secondary';
                                                        if ($user['role'] == 'superuser') $badge = 'danger';
                                                        elseif ($user['role'] == 'admin') $badge = 'primary';
                                                        elseif ($user['role'] == 'manager') $badge = 'success';
                                                        elseif ($user['role'] == 'peserta') $badge = 'info';
                                                        ?>
                                                        <span class="badge badge-<?= $badge ?>"><?= htmlspecialchars($user['role']) ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <a href="/pmlauwba/?url=edit_user&id=<?= $user['id_user'] ?>"
                                                                class="btn btn-link btn-warning btn-lg" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>

                                                            <a href="/pmlauwba/?url=hapus_user&id=<?= $user['id_user'] ?>"
                                                                class="btn btn-link btn-danger"
                                                                onclick="return confirm('Yakin hapus user ini?');"
                                                                title="Hapus">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </div>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada user.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php if (file_exists($footerPath)) include $footerPath;
    else echo "<!-- footer not found: {$footerPath} -->"; ?>
</div>

<!-- DataTables scripts (safety checks included) -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    (function() {
        if (typeof jQuery === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            console.warn('jQuery atau DataTables tidak ditemukan. DataTables tidak diinisialisasi.');
            return;
        }

        $(document).ready(function() {
            var $table = $('#basic-datatables');
            if ($table.length === 0) return;

            var colCount = $table.find('thead th').length || 0;
            $table.find('tbody tr').each(function() {
                var $cells = $(this).children('td,th');
                if ($cells.length === 1) {
                    var colspan = parseInt($cells.attr('colspan') || 1, 10);
                    if (colspan === colCount) return;
                }
                if ($cells.length < colCount) {
                    for (var i = $cells.length; i < colCount; i++) $(this).append('<td></td>');
                } else if ($cells.length > colCount) {
                    $cells.slice(colCount).remove();
                }
            });

            if ($.fn.DataTable.isDataTable($table)) {
                try {
                    $table.DataTable().clear().destroy();
                } catch (e) {}
            }

            try {
                $table.DataTable({
                    "pageLength": 10,
                    "ordering": false
                });
            } catch (err) {
                console.error('Gagal inisialisasi DataTable:', err);
            }
        });
    })();
</script>