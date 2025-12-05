<?php
// Lokasi: backend/controllers/PresensiController.php

if (!defined('APP_ROOT')) {
    // Harus didefinisikan di index.php root. Jika belum, hitung dari lokasi file.
    $ar = realpath(__DIR__ . '/../../');
    define('APP_ROOT', $ar ?: __DIR__);
}

require_once APP_ROOT . '/models/Presensi.php';

class PresensiController {
    
    private $koneksi;
    private $presensiModel;

    public function __construct($db) {
        $this->koneksi = $db;
        $this->presensiModel = new Presensi($db);
    }

    /**
     * Menampilkan halaman Presensi (backend)
     */
    public function index() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Keamanan: Cek apakah user sudah login (backend)
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin', 'peserta'])) {
            header("Location: ?url=login&error=" . urlencode("Akses ditolak"));
            exit;
        }

        $id_user = $_SESSION['id_user'];

        // Cek status presensi hari ini
        $presensi_hari_ini = $this->presensiModel->checkPresensiHariIni($id_user);
        
        $judul_halaman = "Presensi Harian";
        $sudah_masuk = false;
        $sudah_keluar = false;
        $waktu_masuk = null;
        $id_presensi = null; 

        if ($presensi_hari_ini) {
            $sudah_masuk = true;
            $waktu_masuk = $presensi_hari_ini['waktu_masuk'] ?? null;
            $id_presensi = $presensi_hari_ini['id_presensi'] ?? null;

            if (!empty($presensi_hari_ini['waktu_keluar'])) {
                $sudah_keluar = true;
            }
        }

        // Path view backend - PERBAIKAN: gunakan path yang benar
        $view = APP_ROOT . '/backend/views/pages/presensi/presensi.php';
        if (file_exists($view)) {
            include $view;
        } else {
            echo "<p>View presensi tidak ditemukan di: $view</p>";
        }
    }

    /**
     * Memproses Presensi Masuk (dipanggil dari form)
     * Route yang disarankan: ?url=proses_presensi_masuk_backend
     */
    public function prosesMasuk() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['logged_in'])) {
            header("Location: ?url=login"); 
            exit;
        }

        $id_user = $_SESSION['id_user'];
        
        $cek = $this->presensiModel->checkPresensiHariIni($id_user);
        if (!$cek) {
            $ok = $this->presensiModel->presensiMasuk($id_user);
            if ($ok) {
                header("Location: ?url=presensi_backend&success=" . urlencode("Presensi masuk berhasil dicatat."));
            } else {
                header("Location: ?url=presensi_backend&error=" . urlencode("Gagal menyimpan presensi masuk."));
            }
        } else {
            header("Location: ?url=presensi_backend&error=" . urlencode("Anda sudah melakukan presensi masuk hari ini."));
        }
        exit;
    }

    /**
     * Memproses Presensi Keluar
     * Route: ?url=proses_presensi_keluar_backend
     */
    public function prosesKeluar() {
        // Pastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['logged_in'])) {
            header("Location: ?url=login"); 
            exit;
        }

        $id_presensi = isset($_POST['id_presensi']) ? intval($_POST['id_presensi']) : 0;
        
        if ($id_presensi > 0) {
            $ok = $this->presensiModel->presensiKeluar($id_presensi);
            if ($ok) {
                header("Location: ?url=presensi_backend&success=" . urlencode("Presensi keluar berhasil dicatat."));
            } else {
                header("Location: ?url=presensi_backend&error=" . urlencode("Gagal menyimpan presensi keluar."));
            }
        } else {
            header("Location: ?url=presensi_backend&error=" . urlencode("Terjadi kesalahan (ID presensi tidak valid)."));
        }
        exit;
    }
}
?>