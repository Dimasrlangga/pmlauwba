<?php
// Lokasi: backend/controllers/LaporanPresensiController.php

// PERBAIKAN 1: Path Model harus ke root, bukan app/models
require_once APP_ROOT . '/models/Presensi.php';

class LaporanPresensiController
{

    private $koneksi;
    private $presensiModel;

    public function __construct($db)
    {
        $this->koneksi = $db;
        $this->presensiModel = new Presensi($db);
    }

    /**
     * Menampilkan halaman Laporan Presensi (Read)
     * Akses: Superuser, Admin, Manager
     */
    public function index()
    {
        // Keamanan: Cek role
        $allowed_roles = ['superuser', 'admin', 'manager'];
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], $allowed_roles)) {
            // PERBAIKAN 2: Redirect pakai ?page=
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Laporan Presensi";

        // 1. Ambil semua data presensi dari Model
        $daftar_presensi = $this->presensiModel->getAll();

        // PERBAIKAN 3: Path View ke backend/views/
        include APP_ROOT . '/backend/views/pages/laporan_presensi/laporan_presensi.php';
    }

    /**
     * Menampilkan halaman Form Edit Presensi (Update)
     * Akses: Superuser, Admin
     */
    public function showEditForm()
    {
        // Keamanan: Cek role
        $allowed_roles = ['superuser', 'admin'];
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], $allowed_roles)) {
            header("Location: ?page=laporan_presensi&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Edit Catatan Presensi";

        // 1. Ambil ID dari URL
        $id_presensi = $_GET['id'] ?? 0;

        // 2. Ambil data presensi tunggal dari Model
        $presensi = $this->presensiModel->findById($id_presensi);

        // PERBAIKAN 3: Path View ke backend/views/
        include APP_ROOT . '/backend/views/edit_presensi.php';
    }

    /**
     * Memproses Form Edit Presensi (Update)
     * Akses: Superuser, Admin
     */
    public function prosesEdit()
    {
        // Keamanan: Cek role
        $allowed_roles = ['superuser', 'admin'];
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], $allowed_roles)) {
            header("Location: ?page=laporan_presensi&error=Akses ditolak");
            exit;
        }

        // 1. Validasi data POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_presensi'])) {

            $id_presensi = $_POST['id_presensi'];

            // 2. Siapkan data
            // Ubah format 'datetime-local' (Y-m-d\TH:i) ke format 'Y-m-d H:i:s'
            $waktu_masuk = !empty($_POST['waktu_masuk']) ? date('Y-m-d H:i:s', strtotime($_POST['waktu_masuk'])) : null;
            $waktu_keluar = !empty($_POST['waktu_keluar']) ? date('Y-m-d H:i:s', strtotime($_POST['waktu_keluar'])) : null;

            $data = [
                'tanggal' => $_POST['tanggal'], // Ini tipenya 'date' (Y-m-d)
                'waktu_masuk' => $waktu_masuk,
                'waktu_keluar' => $waktu_keluar,
                'status_presensi' => $_POST['status_presensi']
            ];

            // 3. Kirim ke Model untuk di-update
            if ($this->presensiModel->update($id_presensi, $data)) {
                header("Location: ?page=laporan_presensi&success=Data presensi berhasil diperbarui!");
            } else {
                header("Location: ?page=edit_presensi&id=$id_presensi&error=Gagal memperbarui data.");
            }
            exit;
        }

        header("Location: ?page=laporan_presensi");
        exit;
    }

    /**
     * Memproses Hapus Presensi (Delete)
     * Akses: Superuser, Admin
     */
    public function prosesHapus()
    {
        // Keamanan: Cek role
        $allowed_roles = ['superuser', 'admin'];
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], $allowed_roles)) {
            header("Location: ?page=laporan_presensi&error=Akses ditolak");
            exit;
        }

        // Pastikan request method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?page=laporan_presensi&error=Metode tidak diperbolehkan");
            exit;
        }

        // Ambil dan validasi id dari POST
        $id_presensi = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id_presensi <= 0) {
            header("Location: ?page=laporan_presensi&error=ID tidak valid.");
            exit;
        }

        // Panggil model untuk hapus
        if ($this->presensiModel->delete($id_presensi)) {
            header("Location: ?page=laporan_presensi&success=Data presensi berhasil dihapus.");
        } else {
            header("Location: ?page=laporan_presensi&error=Gagal menghapus data. Silakan coba lagi.");
        }
        exit;
    }
}
