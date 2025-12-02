<?php 
// Pastikan base URL terdefinisi
if (!isset($base)) {
    $base = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    if ($base === '/' || $base === '\\') $base = '';
}
?>

<!-- About Start -->
<div id="about" class="container-fluid bg-secondary">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 pb-0 pb-lg-5 py-5">
                <div class="pb-0 pb-lg-5 py-5">
                    <div class="title wow fadeInUp" data-wow-delay="0.1s">
                        <div class="title-left">
                            <h5>History</h5>
                            <h1>About Our Company</h1>
                        </div>
                    </div>
                    <p class="mb-4 wow fadeInUp" data-wow-delay="0.2s">PT. Lauwba Techno Indonesia merupakan perusahaan yang bergerak dibidang teknologi informasi khususnya IT Consultant, Software Development, IT Training & Digital Marketing dengan SKT.KEMENKUMHAM RI NO AHU-0022789.AH.01.01</p>
                    <ul class="list-group list-group-flush mb-5 wow fadeInUp" data-wow-delay="0.3s">
                        <li class="list-group-item bg-dark text-body border-secondary ps-0"><i class="fa fa-check-circle text-primary me-1"></i> NPWP : 91.302.590.4-541.000</li>
                        <li class="list-group-item bg-dark text-body border-secondary ps-0"><i class="fa fa-check-circle text-primary me-1"></i> PT. Lauwba Techno Indonesia</li>
                        <li class="list-group-item bg-dark text-body border-secondary ps-0"><i class="fa fa-check-circle text-primary me-1"></i> 0274-4435 9440</li>
                    </ul>
                    <div class="row wow fadeInUp" data-wow-delay="0.4s">
                        <!-- Perbaikan: href="#!" diganti agar tidak error JS -->
                        <div class="col-6"><a href="javascript:void(0);" class="btn btn-outline-primary border-2 py-3 w-100">Become A Participant</a></div>
                        <div class="col-6"><a href="https://lauwba.com/" class="btn btn-primary py-3 w-100" target="_blank" rel="noopener noreferrer">Details About Us</a></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.5s">
                <!-- PERBAIKAN: Gunakan 'template_frontend' -->
                <!-- Fitur Tambahan: onerror untuk fallback gambar jika 'lauwba.png' tidak ada -->
                <img class="img-fluid" 
                     src="<?= $base ?>/template_frontend/img/lauwba.png" 
                     alt="About Lauwba"
                     onerror="this.onerror=null;this.src='<?= $base ?>/template_frontend/img/lauwbalogo.png';">
            </div>
        </div>
    </div>
</div>
<!-- About End -->