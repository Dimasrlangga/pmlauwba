<?php
// Lokasi: backend/controllers/IzinSakitController.php
require_once APP_ROOT . '/helpers/auth.php'; // tambahkan ini
requireBackendAuth();
// PERBAIKAN 1: Path Model harus ke root, bukan app/models
require_once APP_ROOT . '/models/IzinSakit.php';

class IzinSakitController {
    
    private $koneksi;
    private $izinModel;

    public function __construct($db) {
        $this->koneksi = $db;
        $this->izinModel = new IzinSakit($db);
    }

    /**
     * Menampilkan halaman Kelola Izin (Read)
     */
    public function index() {
        // Keamanan: Hanya untuk Superuser dan Admin (dan Manager)
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin', 'manager'])) {
            // PERBAIKAN 2: Redirect pakai ?page=
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Kelola Izin & Sakit";

        // 1. Ambil semua data pengajuan dari Model
        $daftar_pengajuan = $this->izinModel->getAll();

        // PERBAIKAN 3: Path View ke backend/views/
        include APP_ROOT . '/backend/views/kelola_izin.php';
    }

    /**
     * Memproses approval (Setujui / Tolak)
     */
    public function prosesApproval() {
        // Keamanan: Hanya untuk Superuser dan Admin
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin'])) {
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        // 1. Ambil data dari URL
        $id_izin = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? ''; // 'disetujui' atau 'ditolak'
        $id_admin = $_SESSION['id_user']; // ID admin/superuser yang merespon

        // 2. Validasi status
        if ($id_izin > 0 && ($status == 'disetujui' || $status == 'ditolak')) {
            
            // 3. Kirim ke Model untuk di-update
            if ($this->izinModel->updateStatus($id_izin, $status, $id_admin)) {
                header("Location: ?page=kelola_izin&success=Status pengajuan berhasil diperbarui.");
            } else {
                header("Location: ?page=kelola_izin&error=Gagal memperbarui status.");
            }
        } else {
            header("Location: ?page=kelola_izin&error=Aksi tidak valid.");
        }
        exit;
    }

    /**
     * Memproses penghapusan data izin
     * (Hanya Superuser yang boleh menghapus data)
     */
    public function prosesHapus() {
        // Keamanan: HANYA Superuser
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
            header("Location: ?page=login&error=Akses ditolak. Hanya Superuser yang boleh menghapus.");
            exit;
        }

        $id_izin = $_GET['id'] ?? 0;

        if ($id_izin > 0) {
            if ($this->izinModel->deleteById($id_izin)) {
                header("Location: ?page=kelola_izin&success=Data pengajuan berhasil dihapus.");
            } else {
                header("Location: ?page=kelola_izin&error=Gagal menghapus data.");
            }
        } else {
            header("Location: ?page=kelola_izin&error=ID tidak valid.");
        }
        exit;
    }
}
?>