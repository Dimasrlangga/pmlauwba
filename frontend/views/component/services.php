<?php 
// Pastikan base URL terdefinisi
if (!isset($base)) {
    $base = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    if ($base === '/' || $base === '\\') $base = '';
}
?>
<!-- Service Start -->
<div id="services" class="container-fluid py-5">
  <div class="container py-5">
    <div class="text-center">
      <div class="title wow fadeInUp" data-wow-delay="0.1s">
        <div class="title-center">
          <h5>Services</h5>
          <h1>IT Training & Courses</h1>
        </div>
      </div>
    </div>
    <div class="service-item service-item-left">
      <div class="row g-0 align-items-center">
        <div class="col-md-5">
          <div class="service-img p-5 wow fadeInRight" data-wow-delay="0.2s">
            <img class="img-fluid rounded-circle" src="<?= $base ?>/template_frontend/img/webdesign.png" alt="">
          </div>
        </div>
        <div class="col-md-7">
          <div class="service-text px-5 px-md-0 py-md-5 wow fadeInRight" data-wow-delay="0.5s">
            <h3 class="text-uppercase">Training/Kursus WEBSITE (WEB DESIGN & PROGRAMMING)</h3>
            <p class="mb-4">Tempat Kursus & Training Website Jogja, Yogyakarta, Jakarta, Tangerang, Makassar, Bandung, Semarang, Solo, Medan, Surabaya, Bandung, Lampung, Palembang, Malang, Pekanbaru, Balikpapan, Padang, Aceh, Bali, Kalimantan dan Papua.
              Program ini didesain khusus bagi Anda yang ingin sampai mahir didalam membuat Website dengan dibimbing dari NOL/Dasar dan GRATIS mengulang sampai BISA!.</p>
            <a class="btn btn-outline-primary border-2 px-4" target="_blank" 
       rel="noopener noreferrer" href="https://lauwba.com/training-19-pelatihan-membuat-website-web-design--programming.html">Read More <i
                class="fa fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div class="service-item service-item-right">
      <div class="row g-0 align-items-center">
        <div class="col-md-5 order-md-1 text-md-end">
          <div class="service-img p-5 wow fadeInLeft" data-wow-delay="0.2s">
            <img class="img-fluid rounded-circle" src="<?= $base ?>/template_frontend/img/digital.png" alt="">
          </div>
        </div>
        <div class="col-md-7">
          <div class="service-text px-5 px-md-0 py-md-5 text-md-end wow fadeInLeft" data-wow-delay="0.5s">
            <h3 class="text-uppercase">Training & Kursus DIGITAL MARKETING</h3>
            <p class="mb-4">Tempat Kursus & Training Digital Marketing Jogja, Yogyakarta, Jakarta, Tangerang, Makassar, Bandung, Semarang, Solo, Medan, Surabaya, Bandung, Lampung, Palembang, Malang, Pekanbaru, Balikpapan, Padang, Aceh, Bali, Kalimantan dan Papua.
              Kini kami kembali membuka kelas kursus/training Digital Marketing setelah sebelumnya kami telah meluluskan -+ 10.000 peserta yang berasal dari kalangan Umum, Dosen, Guru, utusan Instansi Pemerintahan, Utusan Perusahaan dan Mahasiswa yang berasal dari hampir seluruh wilayah Indonesia serta dari luar negeri seperti Malaysia, Australia, Qatar dan Filipina.</p>
            <a class="btn btn-outline-primary border-2 px-4" target="_blank" 
       rel="noopener noreferrer" href="https://lauwba.com/training-12-training--kursus-digital-marketing.html">Read More <i
                class="fa fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div class="service-item service-item-left">
      <div class="row g-0 align-items-center">
        <div class="col-md-5">
          <div class="service-img p-5 wow fadeInRight" data-wow-delay="0.2s">
            <img class="img-fluid rounded-circle" src="<?= $base ?>/template_frontend/img/laravel.png" alt="">
          </div>
        </div>
        <div class="col-md-7">
          <div class="service-text px-5 px-md-0 py-md-5 wow fadeInRight" data-wow-delay="0.5s">
            <h3 class="text-uppercase">Training Web Framework LARAVEL</h3>
            <p class="mb-4">Tempat Kursus & Training Web LARAVEL Jogja, Yogyakarta, Jakarta, Tangerang, Makassar, Bandung, Semarang, Solo, Medan, Surabaya, Bandung, Lampung, Palembang, Malang, Pekanbaru, Balikpapan, Padang, Aceh, Bali, Kalimantan dan Papua.
              Program ini di desain khusus bagi Anda yang ingin sampai mahir didalam membuat Web Menggunakan Framework LARAVEL dengan dibimbing dari NOL/Dasar dan GRATIS mengulang sampai BISA!.</p>
            <a class="btn btn-outline-primary border-2 px-4" target="_blank" 
       rel="noopener noreferrer" href="https://lauwba.com/training-web-framework-laravel.html">Read More <i
                class="fa fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div class="service-item service-item-right">
      <div class="row g-0 align-items-center">
        <div class="col-md-5 order-md-1 text-md-end">
          <div class="service-img p-5 wow fadeInLeft" data-wow-delay="0.2s">
            <img class="img-fluid rounded-circle" src="<?= $base ?>/template_frontend/img/flutter.png" alt="">
          </div>
        </div>
        <div class="col-md-7">
          <div class="service-text px-5 px-md-0 py-md-5 text-md-end wow fadeInLeft" data-wow-delay="0.5s">
            <h3 class="text-uppercase">Training & Kursus FLUTTER</h3>
            <p class="mb-4">Flutter Merupakan sebuah framework aplikasi mobil sumber terbuka yang diciptakan oleh Google. Flutter digunakan dalam pengembangan aplikasi untuk sistem operasi Android dan iOS, Kursus Online, Kursus Online Flutter, serta menjadi metode utama untuk membuat aplikasi Google Fuchsia.
            Pada Training ini  belajar membuat aplikasi  yang bisa berjalan di aplikasi android dan ios sekaligus. Dengan kursus ini peserta akan dapat membuat aplikasi mobile dengan mudah. Training ini sangat cocok bagi pemula yang belum ada dasar programming sekalipun.</p>
            <a class="btn btn-outline-primary border-2 px-4" target="_blank" 
       rel="noopener noreferrer" href="https://lauwba.com/training-11-training--kursus-flutter-.html">Read More <i
                class="fa fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Service End -->