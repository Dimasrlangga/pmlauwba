<?php
// backend/views/component/navbar.php
// Navbar sekarang dibungkus card agar konsisten seperti index.php
// Jika suatu halaman ingin NON-CARD, set $wrap_navbar_card = false sebelum include file ini.

@include __DIR__ . '/path.php';

$nama_user = $_SESSION['nama_lengkap'] ?? 'Guest';
$role_user = $_SESSION['role'] ?? 'guest';
$nama_user_esc = htmlspecialchars($nama_user, ENT_QUOTES, 'UTF-8');
$role_user_esc = htmlspecialchars(ucfirst($role_user), ENT_QUOTES, 'UTF-8');

if (empty($assets)) {
    $assets = '/assets'; // sesuaikan jika perlu
}

// default: pakai card. Halaman bisa override dengan $wrap_navbar_card = false;
if (!isset($wrap_navbar_card)) $wrap_navbar_card = true;
?>

<style>
  /* Card wrapper untuk navbar supaya terlihat terpisah (mirip index) */
  .card-navbar {
    margin: 0; /* jangan dorong layout */
    border: 0;
    border-radius: 0.5rem;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    background: #ffffff;
  }
  .card-navbar .card-body {
    padding: 0; /* biarkan navbar mengatur padding */
  }

  /* Pastikan navbar berada di dalam flow header (tidak fixed) */
  .navbar-header {
    position: relative;
    background: transparent;
    box-shadow: none;
    padding: 0;
  }

  .navbar-header .container-fluid {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }

  .navbar-form .input-group .form-control {
    max-width: 380px;
    min-width: 120px;
  }

  .profile-username { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; display: inline-block; vertical-align: middle; }

  @media (max-width: 991.98px) {
    .navbar-form { display: none !important; }
    .navbar-header .container-fluid { padding-left: 0.5rem; padding-right: 0.5rem; }
  }
</style>

<?php if ($wrap_navbar_card): ?>
  <div class="card card-navbar mb-0">
    <div class="card-body p-0">
<?php endif; ?>

<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
  <div class="container-fluid">
    <!-- Search (desktop) -->
    <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
      <div class="input-group">
        <div class="input-group-prepend">
          <button type="submit" class="btn btn-search pe-1">
            <i class="fa fa-search search-icon"></i>
          </button>
        </div>
        <input type="text" placeholder="Search ..." class="form-control" />
      </div>
    </nav>

    <!-- Right side items -->
    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
      <!-- Search (mobile) -->
      <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
          <i class="fa fa-search"></i>
        </a>
        <ul class="dropdown-menu dropdown-search animated fadeIn">
          <form class="navbar-left navbar-form nav-search p-2">
            <div class="input-group">
              <input type="text" placeholder="Search ..." class="form-control" />
            </div>
          </form>
        </ul>
      </li>

      <!-- (Tambahkan item lain seperti messages/notifications sesuai kebutuhan) -->

      <!-- User -->
      <li class="nav-item topbar-user dropdown hidden-caret">
        <a class="dropdown-toggle profile-pic d-flex align-items-center" data-bs-toggle="dropdown" href="#" aria-expanded="false">
          <div class="avatar-sm me-2">
            <img src="<?= $assets ?>/assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle" />
          </div>
          <span class="profile-username">
            <span class="op-7">Hi,</span>
            <span class="fw-bold ms-1"><?= $nama_user_esc ?></span>
          </span>
        </a>

        <ul class="dropdown-menu dropdown-user animated fadeIn">
          <div class="dropdown-user-scroll scrollbar-outer">
            <li>
              <div class="user-box d-flex align-items-center">
                <div class="avatar-lg me-3">
                  <img src="<?= $assets ?>/assets/img/profile.jpg" alt="image profile" class="avatar-img rounded" />
                </div>
                <div class="u-text">
                  <h4 class="mb-0"><?= $nama_user_esc ?></h4>
                  <p class="text-muted mb-1"><?= $role_user_esc ?></p>
                  <a href="?url=profile" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                </div>
              </div>
            </li>

            <li>
              <div class="dropdown-divider"></div>
              <!-- <a class="dropdown-item" href="?url=profile">My Profile</a> -->
              <!-- <a class="dropdown-item" href="?url=inbox">Inbox</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="?url=settings">Account Setting</a>
              <div class="dropdown-divider"></div> -->
              <a class="dropdown-item" href="?page=logout">Logout</a>
            </li>
          </div>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<?php if ($wrap_navbar_card): ?>
    </div>
  </div>
<?php endif; ?>
