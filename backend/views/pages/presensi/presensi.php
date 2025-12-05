<?php
// File: backend/views/pages/presensi/presensi.php
// Versi yang sudah diperbaiki: includes absolut, fallback variabel, safe output

// Pastikan APP_ROOT tersedia (biasanya didefinisikan di root index.php)
if (!defined('APP_ROOT')) {
    // Coba cari 3 level ke atas dari lokasi file ini
    $maybeRoot = realpath(__DIR__ . '/../../../../');
    if ($maybeRoot) define('APP_ROOT', $maybeRoot);
    else define('APP_ROOT', __DIR__); // fallback (tetap bekerja tapi relatif)
}

// Pastikan session aktif
if (!session_id()) session_start();

// Fallback untuk assets/path jika ada file path.php di component
$componentPath = APP_ROOT . '/backend/views/component';
$pathFile = $componentPath . '/path.php';
if (file_exists($pathFile)) {
    include_once $pathFile; // harus mendefinisikan $assets minimal
} else {
    // fallback assets (ubah sesuai struktur jika perlu)
    $assets = $assets ?? '/backend/views/component';
}

// Safety defaults untuk variabel yang dikirim controller
$sudah_masuk  = $sudah_masuk  ?? false;
$sudah_keluar = $sudah_keluar ?? false;
$waktu_masuk  = $waktu_masuk  ?? null;
$id_presensi  = $id_presensi  ?? null;

$judul_halaman = "Presensi Harian";

// Include header / sidebar memakai path absolut supaya tidak error
$headerFile = APP_ROOT . '/backend/views/component/header.php';
$navbarFile = APP_ROOT . '/backend/views/component/navbar.php';
$sidebarFile = APP_ROOT . '/backend/views/component/sidebar.php';
$footerFile = APP_ROOT . '/backend/views/component/footer.php';

if (file_exists($headerFile)) include $headerFile;
else {
    // Minimal fallback: head tag supaya halaman tetap valid
    echo "<!doctype html><html><head><meta charset='utf-8'><title>" . htmlspecialchars($judul_halaman) . "</title></head><body>";
}

if (file_exists($navbarFile)) include $navbarFile;
else {
    echo "<!-- navbar not found: {$navbarFile} -->";
}
if (file_exists($sidebarFile)) include $sidebarFile;
?>

<style>
    .jam-digital {
        font-size: 3.5rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
        margin-top: 20px;
    }

    .tanggal-digital {
        font-size: 1.2rem;
        color: #555;
        margin-bottom: 30px;
    }

    .btn-presensi {
        width: 100%;
        padding: 15px;
        font-size: 1.2rem;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 15px;
        border-radius: 50px;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all .3s;
    }

    .btn-presensi:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    .status-alert {
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .status-sukses {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
</style>

<div class="main-panel">
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title"><?= htmlspecialchars($judul_halaman) ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="?url=dashboard_backend">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Presensi</a></li>
                </ul>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-header text-center">
                            <div class="card-title">Form Presensi</div>
                            <p class="card-category">Silakan lakukan presensi sesuai waktu kerja.</p>
                        </div>

                        <div class="card-body text-center">
                            <div id="jam-digital" class="jam-digital">--:--:--</div>
                            <div class="tanggal-digital"><?= htmlspecialchars(date('d F Y')) ?></div>

                            <?php if (!empty($_GET['error'])): ?>
                                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_GET['error']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($_GET['success'])): ?>
                                <div class="alert alert-success" role="alert"><?= htmlspecialchars($_GET['success']) ?></div>
                            <?php endif; ?>

                            <?php if ($sudah_keluar): ?>
                                <div class="status-alert status-sukses">
                                    <i class="fa fa-check-circle"></i> Anda sudah menyelesaikan presensi hari ini.
                                    <br>
                                    <small>Masuk: <?= htmlspecialchars(date('H:i', strtotime($waktu_masuk ?? 'now'))) ?> | Selesai</small>
                                </div>

                            <?php elseif ($sudah_masuk): ?>

                                <?php
                                // Pastikan id_presensi berupa integer (0 jika tidak ada)
                                $id_presensi_int = isset($id_presensi) ? intval($id_presensi) : 0;

                                // Pastikan waktu masuk valid (string). Jika null, gunakan current time supaya strtotime tidak error.
                                $waktu_masuk_safe = $waktu_masuk ?? date('Y-m-d H:i:s');
                                ?>

                                <?php if ($id_presensi_int > 0): ?>
                                    <form action="?url=proses_presensi_keluar_backend" method="POST">
                                        <input type="hidden" name="id_presensi" value="<?= htmlspecialchars((string)$id_presensi_int) ?>">
                                        <button type="submit" class="btn btn-danger btn-presensi">
                                            <i class="fa fa-sign-out-alt"></i> PRESENSI KELUAR
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        Presensi masuk tercatat, namun ID presensi tidak valid. Silakan refresh halaman atau hubungi admin.
                                    </div>
                                <?php endif; ?>

                                <div class="mt-3 text-muted">
                                    Anda masuk pukul:
                                    <strong>
                                        <?= htmlspecialchars(date('H:i:s', strtotime($waktu_masuk_safe))) ?>
                                    </strong>
                                </div>

                            <?php else: ?>

                                <form action="?url=proses_presensi_masuk_backend" method="POST">
                                    <button type="submit" class="btn btn-success btn-presensi">
                                        <i class="fa fa-fingerprint"></i> PRESENSI MASUK
                                    </button>
                                </form>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php
    // include footer jika ada, kalau tidak close body/html agar page valid
    if (file_exists($footerFile)) {
        include $footerFile;
    } else {
        echo "</div></body></html>";
    }
    ?>
</div>

<script>
    function updateJam() {
        const now = new Date();
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const detik = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('jam-digital');
        if (el) el.textContent = jam + ':' + menit + ':' + detik;
    }
    setInterval(updateJam, 1000);
    updateJam();
</script>