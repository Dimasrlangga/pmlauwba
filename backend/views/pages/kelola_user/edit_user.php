<?php
// Lokasi: backend/views/pages/kelola_user/edit_user.php
// Versi rapih: berfungsi baik saat di-include controller maupun dibuka langsung

// 1) APP_ROOT fallback
if (!defined('APP_ROOT')) {
    $calculatedRoot = realpath(__DIR__ . '/../../../../');
    define('APP_ROOT', $calculatedRoot ?: __DIR__);
}

// 2) minimal bootstrap bila dibuka langsung
$bootstrapped_here = false;
if (!isset($user)) {
    $bootstrapped_here = true;

    // start session (jika belum)
    if (!session_id()) session_start();

    // include database (harus membuat $koneksi)
    $dbFile = APP_ROOT . '/database.php';
    if (file_exists($dbFile)) {
        require_once $dbFile;
    } else {
        // fallback koneksi — sesuaikan jika perlu
        $koneksi = new mysqli('127.0.0.1', 'root', '', 'pmlauwba');
        if ($koneksi->connect_errno) {
            die("Koneksi DB gagal: " . $koneksi->connect_error);
        }
    }

    // include Model User (cek beberapa path umum)
    $candidates = [
        APP_ROOT . '/models/User.php',
        APP_ROOT . '/app/models/User.php',
        APP_ROOT . '/backend/models/User.php'
    ];
    $found = false;
    foreach ($candidates as $p) {
        if (file_exists($p)) { require_once $p; $found = true; break; }
    }

    if (!$found || !class_exists('User')) {
        echo "<div class='alert alert-danger'>Model User tidak ditemukan. Cek file models/User.php</div>";
        exit;
    }

    // require login (redirect ke router login jika belum)
    if (!isset($_SESSION['logged_in'])) {
        header("Location: /pmlauwba/?url=login");
        exit;
    }

    // Ambil user berdasarkan id dari GET
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        echo "<div class='alert alert-danger'>ID user tidak valid.</div>";
        exit;
    }

    $userModel = new User($koneksi);
    $user = $userModel->findById($id);
    if (!$user) {
        echo "<div class='alert alert-danger'>User tidak ditemukan.</div>";
        exit;
    }
}

// 3) include component (header/sidebar/footer) — aman jika tidak ada
$headerPath  = APP_ROOT . '/backend/views/component/header.php';
$sidebarPath = APP_ROOT . '/backend/views/component/sidebar.php';
$footerPath  = APP_ROOT . '/backend/views/component/footer.php';

if (file_exists($headerPath)) include $headerPath;
if (file_exists($sidebarPath)) include $sidebarPath;

// 4) tampilkan form
$judul_halaman = "Edit User";
?>

<div class="main-panel">
  <div class="container">
    <div class="page-inner">
      <div class="page-header"><h4 class="page-title"><?= htmlspecialchars($judul_halaman) ?></h4></div>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>

      <form action="/pmlauwba/?url=proses_edit_user" method="POST">
        <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id_user']) ?>">

        <div class="form-group">
          <label for="nama_lengkap">Nama Lengkap</label>
          <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                 value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
        </div>

        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control"
                 value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="form-group">
          <label for="password">Password Baru (Opsional)</label>
          <input type="password" id="password" name="password" class="form-control">
          <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
        </div>

        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" name="role" class="form-control" <?= $user['role'] === 'superuser' ? 'disabled' : '' ?> required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="peserta" <?= $user['role'] === 'peserta' ? 'selected' : '' ?>>Peserta</option>
            <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
            <?php if ($user['role'] === 'superuser'): ?>
              <option value="superuser" selected>Superuser</option>
              <input type="hidden" name="role" value="superuser">
            <?php endif; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="/pmlauwba/?url=kelola_user" class="btn btn-secondary">Batal</a>
      </form>

    </div>
  </div>

  <?php if (file_exists($footerPath)) include $footerPath; ?>
</div>
