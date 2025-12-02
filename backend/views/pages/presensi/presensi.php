<?php
// Lokasi: backend/views/presensi/presensi.php
// Versi yang disesuaikan: hanya ubah action form ke route backend

// safety defaults
$sudah_masuk  = $sudah_masuk  ?? false;
$sudah_keluar = $sudah_keluar ?? false;
$waktu_masuk  = $waktu_masuk  ?? null;
$id_presensi  = $id_presensi  ?? null;

$judul_halaman = "Presensi Harian";

// include header/sidebar/footer (relatif seperti strukturmu)
include '../../component/header.php';
include '../../component/sidebar.php';

// timezone
date_default_timezone_set('Asia/Jakarta');
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
                <h4 class="page-title"><?= $judul_halaman ?></h4>
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
                            <div class="tanggal-digital"><?= date('d F Y') ?></div>

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

                                <form action="?url=proses_presensi_keluar_backend" method="POST">
                                    <!-- cast to int for safety, then htmlspecialchars on string form of it -->
                                    <input type="hidden" name="id_presensi" value="<?= htmlspecialchars((string)$id_presensi_int) ?>">
                                    <button type="submit" class="btn btn-danger btn-presensi">
                                        <i class="fa fa-sign-out-alt"></i> PRESENSI KELUAR
                                    </button>
                                </form>

                                <div class="mt-3 text-muted">
                                    Anda masuk pukul:
                                    <strong>
                                        <?= htmlspecialchars(date('H:i:s', strtotime($waktu_masuk_safe))) ?>
                                    </strong>
                                </div>

                            <?php endif; ?>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../../component/footer.php'; ?>
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