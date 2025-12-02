<?php
// Set zona waktu
date_default_timezone_set('Asia/Jakarta');
?>

<!-- Header Halaman -->
<div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 50px;">
    <div class="row py-5">
        <div class="col-12 pt-lg-5 mt-lg-5 text-center">
            <h1 class="display-4 text-white animated zoomIn">Presensi Harian</h1>
            <a href="?page=dashboard_frontend" class="h5 text-white">Dashboard</a>
            <i class="far fa-circle text-white px-2"></i>
            <a href="#" class="h5 text-white">Presensi</a>
        </div>
    </div>
</div>

<!-- Konten Presensi -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-white text-center py-4 border-0">
                        <h4 class="text-primary m-0 text-uppercase">Form Kehadiran</h4>
                        <p class="text-muted mb-0">Silakan lakukan presensi sesuai waktu kerja</p>
                    </div>
                    
                    <div class="card-body p-5 text-center">
                        
                        <!-- Jam Digital Besar -->
                        <div class="mb-4">
                            <h1 id="jam-digital" class="display-1 fw-bold text-dark">--:--:--</h1>
                            <h5 class="text-uppercase text-muted letter-spacing-2">
                                <?php echo date('d F Y'); ?>
                            </h5>
                        </div>

                        <!-- Notifikasi Pesan -->
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger fade show" role="alert">
                                <i class="fa fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($_GET['error']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
                                <?= htmlspecialchars($_GET['success']) ?>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <!-- Logika Tombol Presensi -->
                        <?php if ($sudah_keluar): ?>
                            
                            <!-- KASUS 3: Sudah Selesai -->
                            <div class="alert alert-success border-0 bg-success text-white py-4">
                                <div class="mb-2"><i class="fa fa-check-circle fa-3x"></i></div>
                                <h5 class="text-white">Presensi Selesai!</h5>
                                <p class="mb-0">Terima kasih, Anda telah menyelesaikan presensi hari ini.</p>
                            </div>
                            <div class="mt-3 text-muted">
                                <small><i class="fa fa-clock me-1"></i> Masuk: <?= date('H:i', strtotime($waktu_masuk)) ?> WIB</small>
                            </div>

                        <?php elseif ($sudah_masuk): ?>
                            
                            <!-- KASUS 2: Sudah Masuk, Belum Keluar -->
                            <div class="alert alert-info mb-4">
                                <i class="fa fa-info-circle me-1"></i> 
                                Anda masuk pukul <strong><?= date('H:i', strtotime($waktu_masuk)) ?> WIB</strong>
                            </div>

                            <form action="?page=proses_presensi_keluar_peserta" method="POST">
                                <input type="hidden" name="id_presensi" value="<?php echo $id_presensi; ?>">
                                <button type="submit" class="btn btn-danger btn-lg w-100 py-3 animated pulse infinite">
                                    <i class="fa fa-sign-out-alt me-2"></i> KLIK PRESENSI KELUAR
                                </button>
                            </form>
                            <p class="text-muted mt-3 small">Jangan lupa presensi keluar sebelum pulang.</p>

                        <?php else: ?>
                            
                            <!-- KASUS 1: Belum Presensi Masuk -->
                            <form action="?page=proses_presensi_masuk_peserta" method="POST">
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                                    <i class="fa fa-fingerprint me-2"></i> KLIK PRESENSI MASUK
                                </button>
                            </form>
                            <p class="text-muted mt-3 small">Catat kehadiran Anda tepat waktu.</p>

                        <?php endif; ?>

                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="?page=dashboard_frontend" class="text-decoration-none text-muted">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Script Jam Digital -->
<script>
    function updateJam() {
        var now = new Date();
        var jam = String(now.getHours()).padStart(2, '0');
        var menit = String(now.getMinutes()).padStart(2, '0');
        var detik = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('jam-digital').textContent = jam + ':' + menit + ':' + detik;
    }
    setInterval(updateJam, 1000);
    updateJam(); 
</script>