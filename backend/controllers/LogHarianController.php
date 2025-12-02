<?php
// Lokasi: backend/controllers/LogHarianController.php

// PERBAIKAN 1: Path Model harus ke root, bukan app/models
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
            // PERBAIKAN 2: Redirect pakai ?page=
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Laporan Log Aktivitas Harian";
        $log_edit = null; 

        // --- Cek apakah ini mode EDIT (Hanya Superuser/Admin) ---
        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && in_array($_SESSION['role'], ['superuser', 'admin'])) {
            $id_log = $_GET['id'];
            
            $log_edit_data = $this->logModel->getAllLogs(); 
            foreach ($log_edit_data as $log) {
                if ($log['id_log'] == $id_log) {
                    $log_edit = $log;
                    break;
                }
            }
            
            if (!$log_edit) {
                // PERBAIKAN 2: Redirect pakai ?page=
                header("Location: ?page=log_harian&error=Log tidak ditemukan.");
                exit;
            }
        }

        // --- Ambil Data Log ---
        $daftar_log = $this->logModel->getAllLogs(); 

        // PERBAIKAN 3: Path View ke backend/views/
        include APP_ROOT . '/backend/views/log_harian.php';
    }

    /**
     * Memproses penambahan atau update log harian
     */
    public function prosesSimpan() {
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin'])) {
            header("Location: ?page=login"); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_SESSION['id_user']; 
            $tanggal = $_POST['tanggal'];
            $deskripsi = $_POST['deskripsi'];
            $id_log = $_POST['id_log'] ?? null; 

            if (empty($tanggal) || empty($deskripsi)) {
                header("Location: ?page=log_harian&error=Tanggal dan deskripsi tidak boleh kosong.");
                exit;
            }

            if ($id_log) {
                // --- PROSES UPDATE ---
                if ($this->logModel->updateAsAdmin($id_log, $tanggal, $deskripsi)) {
                    header("Location: ?page=log_harian&success=Log berhasil diperbarui.");
                } else {
                    header("Location: ?page=log_harian&error=Gagal memperbarui log.");
                }

            } else {
                // --- PROSES CREATE ---
                if ($this->logModel->create($id_user, $tanggal, $deskripsi)) {
                    header("Location: ?page=log_harian&success=Log baru (pribadi) berhasil disimpan.");
                } else {
                    header("Location: ?page=log_harian&error=Gagal menyimpan log.");
                }
            }
            exit;
        }
    }

    /**
     * Memproses penghapusan log
     */
    public function prosesHapus() {
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin'])) {
            header("Location: ?page=login"); exit;
        }

        $id_log = $_GET['id'] ?? 0;

        if ($id_log > 0) {
            if ($this->logModel->deleteAsAdmin($id_log)) {
                header("Location: ?page=log_harian&success=Log berhasil dihapus.");
            } else {
                header("Location: ?page=log_harian&error=Gagal menghapus log.");
            }
        } else {
            header("Location: ?page=log_harian&error=ID log tidak valid.");
        }
        exit;
    }
}
?>