<?php 
// Pastikan base URL terdefinisi
if (!isset($base)) {
    $base = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    if ($base === '/' || $base === '\\') $base = '';
}

// Cek Status Login & Halaman Aktif
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['role'] == 'peserta';
$halaman = $halaman_aktif ?? ''; // Variabel ini dikirim dari Controller
$nama_user = $_SESSION['nama_lengkap'] ?? 'Peserta';
?>

<!-- Spinner Start -->
<div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!-- Spinner End -->

<!-- Navbar Start -->
<div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg navbar-dark px-lg-5">
        <a href="index.php" class="navbar-brand ms-4 ms-lg-0">
            <h2 class="mb-0 text-primary text-uppercase d-flex align-items-center">
                <img src="<?= $base ?>/template_frontend/img/lauwbalogo.png" 
                     alt="Logo LauwbaLog" 
                     class="me-2" 
                     style="height: 100px;">
                <span>LauwbaLog</span>
            </h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-auto p-4 p-lg-0">
                
                <?php if ($is_logged_in): ?>
                    <!-- ============================== -->
                    <!-- MENU KHUSUS PESERTA (LOGIN)    -->
                    <!-- ============================== -->
                    <a href="?page=dashboard_frontend" class="nav-item nav-link <?= ($halaman == 'dashboard') ? 'active' : '' ?>">Dashboard</a>
                    <a href="?page=presensi_peserta" class="nav-item nav-link <?= ($halaman == 'presensi') ? 'active' : '' ?>">Presensi</a>
                    <a href="?page=log_harian_peserta" class="nav-item nav-link <?= ($halaman == 'log') ? 'active' : '' ?>">Log Harian</a>
                    <a href="?page=izin_peserta" class="nav-item nav-link <?= ($halaman == 'izin') ? 'active' : '' ?>">Ajukan Izin</a>

                <?php else: ?>
                    <!-- ============================== -->
                    <!-- MENU UMUM / TAMU (BELUM LOGIN) -->
                    <!-- ============================== -->
                    <a href="index.php#top" class="nav-item nav-link active">Home</a>
                    <a href="index.php#about" class="nav-item nav-link">About</a>
                    <a href="index.php#services" class="nav-item nav-link">Services</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="index.php#team" class="dropdown-item">Our Models</a>
                            <a href="index.php#testimonial" class="dropdown-item">Testimonial</a>
                            <!-- <a href="index.php#404" class="dropdown-item">404 Page</a> -->
                        </div>
                    </div>
                    <a href="index.php#contact" class="nav-item nav-link">Contact</a>
                <?php endif; ?>

            </div>
            
            <div class="d-none d-lg-flex align-items-center">
                <?php if (isset($_SESSION['logged_in'])): ?>
                    <!-- Info User & Logout -->
                    <div class="text-light me-3">
                        <small>Halo,</small><br>
                        <span class="fw-bold"><?= htmlspecialchars($nama_user) ?></span>
                    </div>
                    <a class="btn btn-outline-danger border-2" href="?page=logout">Logout</a>
                <?php else: ?>
                    <!-- Tombol Login -->
                    <a class="btn btn-outline-primary border-2" href="?page=login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Carousel Start (Hanya Tampil di Halaman Home/Landing Page) -->
    <?php 
    // Logika: Tampilkan carousel jika page kosong (home) ATAU page adalah 'home'
    $page_now = $_GET['page'] ?? 'home';
    if ($page_now == 'home'): 
    ?>
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="<?= $base ?>/template_frontend/img/lauwba1.png" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="title mx-5 px-5 animated slideInDown">
                        <div class="title-center">
                            <h5>Welcome</h5>
                            <h1 class="display-1">Lauwba Academy</h1>
                        </div>
                    </div>
                    <p class="fs-5 mb-5 animated slideInDown">PT. Lauwba Techno Indonesia merupakan perusahaan yang bergerak dibidang teknologi informasi khususnya<br> IT Consultant, Software Development, IT Training & Digital Marketing.</p>
                    <a href="https://lauwba.com/" class="btn btn-outline-primary border-2 py-3 px-5 animated slideInDown">Explore More</a>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100" src="<?= $base ?>/template_frontend/img/lauwba2.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="title mx-5 px-5 animated slideInDown">
                        <div class="title-center">
                            <h5>Welcome</h5>
                            <h1 class="display-1">Lauwba Academy</h1>
                        </div>
                    </div>
                    <p class="fs-5 mb-5 animated slideInDown">PT. Lauwba Techno Indonesia merupakan perusahaan yang bergerak dibidang teknologi informasi khususnya<br> IT Consultant, Software Development, IT Training & Digital Marketing.</p>
                    <a href="https://lauwba.com/" class="btn btn-outline-primary border-2 py-3 px-5 animated slideInDown">Explore More</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <?php endif; ?>
    <!-- Carousel End -->

</div>
<!-- Header End -->