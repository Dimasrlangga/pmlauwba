<?php
// ==========================================================
// FILE: index.php (Di Root Folder PMLAUWBA)
// ==========================================================

// 1. Definisikan Root Aplikasi
define('APP_ROOT', __DIR__);

// 2. Mulai Session
if (!session_id()) session_start();

// 3. Load Koneksi Database
if (!file_exists(APP_ROOT . '/database.php')) {
    die("Error: File database.php tidak ditemukan di root folder.");
}
require_once APP_ROOT . '/database.php';
require_once APP_ROOT . '/helpers/auth.php';

// ==========================================================
// 4. LOAD MODELS & CONTROLLERS
// ==========================================================

// --- Load Models (DARI FOLDER ROOT MODELS) ---
// Perbaikan: Path dihapus '/frontend' nya
require_once APP_ROOT . '/models/User.php';
require_once APP_ROOT . '/models/Presensi.php';
require_once APP_ROOT . '/models/LogHarian.php';
require_once APP_ROOT . '/models/IzinSakit.php';

// --- Load Controllers ---
// Backend
require_once APP_ROOT . '/backend/controllers/AuthController.php';
require_once APP_ROOT . '/backend/controllers/SuperuserController.php';
require_once APP_ROOT . '/backend/controllers/PresensiController.php';
require_once APP_ROOT . '/backend/controllers/LogHarianController.php';
require_once APP_ROOT . '/backend/controllers/IzinSakitController.php';
require_once APP_ROOT . '/backend/controllers/LaporanPresensiController.php';

// Frontend
require_once APP_ROOT . '/frontend/controllers/PesertaController.php';


// ==========================================================
// 5. INISIALISASI CONTROLLER
// ==========================================================
$authController = new AuthController($koneksi);
$superuserController = new SuperuserController($koneksi);
$presensiController = new PresensiController($koneksi);
$logHarianController = new LogHarianController($koneksi);
$izinSakitController = new IzinSakitController($koneksi);
$laporanPresensiController = new LaporanPresensiController($koneksi);
$pesertaController = new PesertaController($koneksi);


// ==========================================================
// 6. ROUTING LOGIC
// ==========================================================
$page = $_GET['page'] ?? $_GET['url'] ?? 'home';

switch ($page) {
    // --- Actions ---
    case 'proses_login':
        $authController->processLogin();
        exit;
    case 'logout':
        $authController->logout();
        exit;
    case 'proses_presensi_masuk_peserta':
        $pesertaController->prosesMasuk();
        exit;
    case 'proses_presensi_keluar_peserta':
        $pesertaController->prosesKeluar();
        exit;
    case 'proses_simpan_log_peserta':
        $pesertaController->prosesSimpanLog();
        exit;
    case 'proses_hapus_log_peserta':
        $pesertaController->prosesHapusLog();
        exit;
    case 'proses_ajukan_izin_peserta':
        $pesertaController->prosesAjukanIzin();
        exit;
    case 'proses_batal_izin_peserta':
        $pesertaController->prosesBatalIzin();
        exit;

        // Backend actions
    case 'proses_tambah_user':
        $superuserController->prosesTambahUser();
        exit;
    case 'proses_edit_user':
        $superuserController->prosesEditUser();
        exit;
    case 'proses_hapus_user':
        $superuserController->prosesHapusUser();
        exit;

        // tampilkan halaman presensi backend
    case 'presensi_backend':
        // panggil controller backend presensi
        ob_clean();
        $presensiController->index();
        exit;
        break;

    // proses presensi masuk (backend)
    case 'proses_presensi_masuk_backend':
        $presensiController->prosesMasuk();
        exit;
        break;

    // proses presensi keluar (backend)
    case 'proses_presensi_keluar_backend':
        $presensiController->prosesKeluar();
        exit;
        break;



    // --- Backend pages (kelola user / forms) ---
    case 'kelola_user':
        // menampilkan daftar user
        $superuserController->kelolaUser();
        exit;
    case 'tambah_user':
        // menampilkan form tambah user
        $superuserController->showTambahForm();
        exit;
    case 'edit_user':
        // menampilkan form edit user (memerlukan ?id=)
        $superuserController->showEditForm();
        exit;
    case 'hapus_user':
        // langsung proses hapus lewat GET id (kamu sudah punya method)
        $superuserController->prosesHapusUser();
        exit;
}

// Helper function
function includeComponent($path)
{
    if (file_exists($path)) {
        include $path;
    } else {
        echo "<br><b>Error:</b> File komponen tidak ditemukan: " . $path;
    }
}

// Path ke folder component
$componentPath = APP_ROOT . '/frontend/views/component';
?>

<!DOCTYPE html>
<html lang="id">

<?php includeComponent($componentPath . '/head.php'); ?>

<body>
    <div id="top"></div>

    <?php includeComponent($componentPath . '/header.php'); ?>

    <main style="min-height: 600px; padding-top: 20px;">
        <?php
        switch ($page) {
            // --- Halaman Statis ---
            case 'home':
                includeComponent($componentPath . '/home.php');
                break;
            case 'about':
                includeComponent($componentPath . '/about.php');
                break;
            case 'services':
                includeComponent($componentPath . '/services.php');
                break;
            case 'team':
                includeComponent($componentPath . '/team.php');
                break;
            case 'testimonial':
                includeComponent($componentPath . '/testimonial.php');
                break;
            case 'contact':
                includeComponent($componentPath . '/contact.php');
                break;

            // --- Halaman Sistem (Frontend Peserta) ---
            case 'login':
                $authController->showLoginForm();
                break;

            case 'dashboard_frontend':
                $pesertaController->index();
                break;

            case 'presensi_peserta':
                $pesertaController->presensi();
                break;

            case 'log_harian_peserta':
                $pesertaController->logHarian();
                break;

            case 'izin_peserta':
                $pesertaController->izinSakit();
                break;

            // --- Halaman Backend (Admin) ---
            case 'dashboard_backend':
                requireBackendAuth();
                ob_clean();
                $superuserController->index();
                exit;
                break;

            case 'presensi_backend':
                requireBackendAuth();
                ob_clean();
                $presensiController->index();
                exit;
                break;

            default:
                echo "<div class='container text-center' style='padding: 100px;'>";
                echo "<h1>404</h1><p>Halaman tidak ditemukan.</p>";
                echo "</div>";
                break;
        }
        ?>
    </main>

    <?php includeComponent($componentPath . '/footer.php'); ?>

    <?php includeComponent($componentPath . '/script.php'); ?>

</body>

</html>