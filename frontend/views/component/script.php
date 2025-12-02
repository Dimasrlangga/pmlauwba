<?php
// Pastikan base URL terdefinisi
if (!isset($base)) {
    $base = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    if ($base === '/' || $base === '\\') $base = '';
}
?>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- PERBAIKAN: Menggunakan 'template_frontend' (underscore) -->
<script src="<?= $base ?>/template_frontend/lib/wow/wow.min.js"></script>
<script src="<?= $base ?>/template_frontend/lib/easing/easing.min.js"></script>
<script src="<?= $base ?>/template_frontend/lib/waypoints/waypoints.min.js"></script>
<script src="<?= $base ?>/template_frontend/lib/owlcarousel/owl.carousel.min.js"></script>
<script src="<?= $base ?>/template_frontend/lib/lightbox/js/lightbox.min.js"></script>

<!-- Template JS -->
<script src="<?= $base ?>/template_frontend/js/main.js"></script>