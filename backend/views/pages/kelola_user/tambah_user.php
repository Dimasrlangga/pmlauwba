<?php
// Lokasi: backend/views/pages/kelola_user/tambah_user.php
// Versi rapih: berfungsi di-include controller atau dibuka langsung

// 1) APP_ROOT fallback
if (!defined('APP_ROOT')) {
    $calculatedRoot = realpath(__DIR__ . '/../../../../');
    define('APP_ROOT', $calculatedRoot ?: __DIR__);
}

// 2) bootstrap jika dibuka langsung
if (!isset($judul_halaman)) {
    if (!session_id()) session_start();

    // include DB jika ada
    $dbFile = APP_ROOT . '/database.php';
    if (file_exists($dbFile)) {
        require_once $dbFile;
    } else {
        $koneksi = new mysqli('127.0.0.1', 'root', '', 'pmlauwba');
        if ($koneksi->connect_errno) die("Koneksi DB gagal: " . $koneksi->connect_error);
    }

    // require login
    if (!isset($_SESSION['logged_in'])) {
        header("Location: /pmlauwba/?url=login");
        exit;
    }
}

// 3) include component
$headerPath  = APP_ROOT . '/backend/views/component/header.php';
$sidebarPath = APP_ROOT . '/backend/views/component/sidebar.php';
$footerPath  = APP_ROOT . '/backend/views/component/footer.php';

if (file_exists($headerPath)) include $headerPath;
if (file_exists($sidebarPath)) include $sidebarPath;

$judul_halaman = "Tambah User Baru";
?>

<div class="main-panel">
  <div class="container">
    <div class="page-inner">
      <div class="page-header"><h4 class="page-title"><?= htmlspecialchars($judul_halaman) ?></h4></div>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
      <?php endif; ?>

      <form action="/pmlauwba/?url=proses_tambah_user" method="POST">
        <div class="form-group">
          <label for="nama_lengkap">Nama Lengkap</label>
          <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" name="role" class="form-control" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="peserta">Peserta</option>
            <option value="manager">Manager</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Tambah User</button>
        <a href="/pmlauwba/?url=kelola_user" class="btn btn-secondary">Batal</a>
      </form>

    </div>
  </div>

  <?php if (file_exists($footerPath)) include $footerPath; ?>
</div>
