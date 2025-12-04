<?php
// Lokasi: backend/controllers/LogHarianController.php

// Path Model yang benar
require_once APP_ROOT . '/models/LogHarian.php';

class LogHarianController {
    
    private $koneksi;
    private $logModel;

    public function __construct($db) {
        $this->koneksi = $db;
        $this->logModel = new LogHarian($db);
    }

    /**
     * Menampilkan halaman Log Harian (Form Create + Tabel Read)
     */
    public function index() {
        // Keamanan: Hanya untuk Superuser, Admin, dan Manager
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin', 'manager'])) {
            header("Location: ?url=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Laporan Log Aktivitas Harian";
        $log_edit = null; 

        // --- Cek apakah ini mode EDIT (Hanya Superuser/Admin) ---
        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && in_array($_SESSION['role'], ['superuser', 'admin'])) {
            $id_log = intval($_GET['id']);
            
            $log_edit = $this->logModel->findById($id_log);
            
            if (!$log_edit) {
                header("Location: ?url=log_harian&error=Log tidak ditemukan.");
                exit;
            }
        }

        // --- Ambil Data Log ---
        $daftar_log = $this->logModel->getAllLogs(); 

        // Path View yang benar
        include APP_ROOT . '/backend/views/pages/log_harian/log_harian.php';
    }

    /**
     * Memproses penambahan atau update log harian
     */
    public function prosesSimpan() {
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin', 'manager'])) {
            header("Location: ?url=login"); 
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = intval($_SESSION['id_user']); 
            $tanggal = $_POST['tanggal'] ?? '';
            // PERBAIKAN: Gunakan nama parameter yang sama dengan form
            $deskripsi = $_POST['deskripsi_kegiatan'] ?? '';
            $id_log = isset($_POST['id_log']) ? intval($_POST['id_log']) : null;
            $mode = $_POST['mode'] ?? 'create';

            // Validasi input
            if (empty($tanggal) || empty($deskripsi)) {
                header("Location: ?url=log_harian&error=Tanggal dan deskripsi tidak boleh kosong.");
                exit;
            }

            // Validasi format tanggal
            $date_obj = DateTime::createFromFormat('Y-m-d', $tanggal);
            if (!$date_obj || $date_obj->format('Y-m-d') !== $tanggal) {
                header("Location: ?url=log_harian&error=Format tanggal tidak valid.");
                exit;
            }

            if ($mode === 'edit' && $id_log) {
                // --- PROSES UPDATE ---
                if ($this->logModel->updateAsAdmin($id_log, $tanggal, $deskripsi)) {
                    header("Location: ?url=log_harian&success=Log berhasil diperbarui.");
                } else {
                    header("Location: ?url=log_harian&error=Gagal memperbarui log.");
                }

            } else {
                // --- PROSES CREATE ---
                if ($this->logModel->create($id_user, $tanggal, $deskripsi)) {
                    header("Location: ?url=log_harian&success=Log baru berhasil disimpan.");
                } else {
                    header("Location: ?url=log_harian&error=Gagal menyimpan log.");
                }
            }
            exit;
        } else {
            header("Location: ?url=log_harian&error=Method tidak valid.");
            exit;
        }
    }

    /**
     * Memproses penghapusan log
     */
    public function prosesHapus() {
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin'])) {
            header("Location: ?url=login"); 
            exit;
        }

        $id_log = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id_log > 0) {
            if ($this->logModel->deleteAsAdmin($id_log)) {
                header("Location: ?url=log_harian&success=Log berhasil dihapus.");
            } else {
                header("Location: ?url=log_harian&error=Gagal menghapus log.");
            }
        } else {
            header("Location: ?url=log_harian&error=ID log tidak valid.");
        }
        exit;
    }
}
?>