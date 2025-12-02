<?php
// backend/views/component/header.php
// Header component untuk halaman backend

// include optional path.php bila Anda punya (biasanya mendefinisikan $assets atau path lain)
$pathFile = __DIR__ . '/path.php';
if (file_exists($pathFile)) {
    include $pathFile;
}

// Pastikan APP_ROOT sudah didefinisikan (di backend/index.php kita sudah mendefinisikannya)
if (!defined('APP_ROOT')) {
    // fallback safety (shouldn't happen when mengikuti Opsi A)
    define('APP_ROOT', dirname(__DIR__, 3));
}

// Pastikan session sudah aktif
if (!session_id()) session_start();

// Include helper auth (harus berada di APP_ROOT . '/helpers/auth.php')
$authHelper = APP_ROOT . '/helpers/auth.php';
if (file_exists($authHelper)) {
    require_once $authHelper;
} else {
    // Jika helper tidak ditemukan, hentikan agar tidak membuka akses tanpa proteksi
    die("Error: helpers/auth.php tidak ditemukan di: " . htmlspecialchars($authHelper));
}

// Proteksi akses: hanya untuk superuser/admin/manager
// requireBackendAuth() akan redirect/exit jika tidak memenuhi
if (function_exists('requireBackendAuth')) {
    requireBackendAuth();
} else {
    // jika fungsi tidak tersedia, hentikan untuk keamanan
    die("Error: function requireBackendAuth() tidak ditemukan. Periksa helpers/auth.php");
}

// Pastikan $assets tersedia (dapat berasal dari path.php atau backend/index.php)
if (!isset($assets) || empty($assets)) {
    // coba bangun dari script name (fallback)
    $scriptPath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $assets = $scriptPath . '/backend';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PMLAUWBA - Admin</title>

  <link rel="icon" href="<?= htmlspecialchars($assets) ?>/assets/img/kaiadmin/favicon.png" />

  <script src="<?= htmlspecialchars($assets) ?>/assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons"
          ],
          urls: ["<?= htmlspecialchars($assets) ?>/assets/css/fonts.min.css"],
        }
      });
  </script>

  <link rel="stylesheet" href="<?= htmlspecialchars($assets) ?>/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($assets) ?>/assets/css/plugins.min.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($assets) ?>/assets/css/kaiadmin.min.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($assets) ?>/assets/css/demo.css">
</head>
