<!-- Header Halaman (Banner Biru di Atas) -->
<div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 50px;">
    <div class="row py-5">
        <div class="col-12 pt-lg-5 mt-lg-5 text-center">
            <h1 class="display-4 text-white animated zoomIn">Dashboard Peserta</h1>
            <a href="index.php" class="h5 text-white">Home</a>
            <i class="far fa-circle text-white px-2"></i>
            <a href="#" class="h5 text-white">Dashboard</a>
        </div>
    </div>
</div>

<!-- Konten Utama Dashboard -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            
            <!-- Kolom Kiri: Sambutan & Statistik -->
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <div class="h-100">
                    <h6 class="section-title bg-white text-start text-primary pe-3">Dashboard</h6>
                    <h1 class="display-6 mb-4">Selamat Datang, <span class="text-primary"><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></span>!</h1>
                    <p class="mb-4">Ini adalah halaman utama Anda. Di sini Anda dapat melakukan presensi harian, mengisi log aktivitas magang, serta mengajukan izin atau sakit.</p>
                    
                    <div class="row g-4 mb-4 pb-2">
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.1s">
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-white" style="width: 60px; height: 60px;">
                                    <i class="fa fa-users fa-2x text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h2 class="text-primary mb-1" data-toggle="counter-up">1</h2>
                                    <p class="fw-medium mb-0">Status Aktif</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-white" style="width: 60px; height: 60px;">
                                    <i class="fa fa-check fa-2x text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h2 class="text-primary mb-1" data-toggle="counter-up">100</h2>
                                    <p class="fw-medium mb-0">% Kehadiran</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Menu Cepat -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="bg-light rounded h-100 d-flex align-items-center p-5">
                    <div class="w-100">
                        <h3 class="mb-4">Menu Cepat</h3>
                        <p class="mb-4">Akses fitur utama magang Anda dengan cepat melalui tombol di bawah ini:</p>
                        
                        <div class="d-grid gap-3">
                            <a href="?page=presensi_peserta" class="btn btn-primary py-3 px-5">
                                <i class="fa fa-clock me-2"></i> Mulai Presensi
                            </a>
                            <a href="?page=log_harian_peserta" class="btn btn-dark py-3 px-5">
                                <i class="fa fa-book me-2"></i> Isi Log Harian
                            </a>
                            <a href="?page=izin_peserta" class="btn btn-outline-primary py-3 px-5">
                                <i class="fa fa-envelope-open-text me-2"></i> Ajukan Izin
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>