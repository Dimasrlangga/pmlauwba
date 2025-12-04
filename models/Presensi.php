<?php
// Lokasi: models/Presensi.php

class Presensi {
    private $koneksi;
    private $tabel = 'presensi';

    public function __construct($db) {
        $this->koneksi = $db;
    }

    // ==========================================================
    // --- FUNGSI UNTUK PESERTA/USER (Self-service) ---
    // ==========================================================

    /**
     * Mengecek status presensi user pada hari ini
     * Mengembalikan data presensi jika ada, atau null
     */
    public function checkPresensiHariIni($id_user) {
        // Ambil tanggal hari ini
        $tanggal_hari_ini = date('Y-m-d');

        // Query untuk mencari presensi user hari ini
        $stmt = $this->koneksi->prepare(
            "SELECT * FROM " . $this->tabel . " 
             WHERE id_user = ? AND tanggal = ?
             LIMIT 1"
        );
        $stmt->bind_param("is", $id_user, $tanggal_hari_ini);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Kembalikan datanya (atau null jika tidak ada)
        return $result->fetch_assoc();
    }

    /**
     * Mencatat presensi MASUK
     */
    public function presensiMasuk($id_user) {
        $tanggal = date('Y-m-d');
        // Kita set zona waktu ke Asia/Jakarta agar akurat
        date_default_timezone_set('Asia/Jakarta');
        $waktu_masuk = date('Y-m-d H:i:s');
        $status = 'hadir';

        $stmt = $this->koneksi->prepare(
            "INSERT INTO " . $this->tabel . " (id_user, waktu_masuk, tanggal, status_presensi) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isss", $id_user, $waktu_masuk, $tanggal, $status);
        
        return $stmt->execute();
    }

    /**
     * Mencatat presensi KELUAR
     */
    public function presensiKeluar($id_presensi) {
        // Kita set zona waktu ke Asia/Jakarta
        date_default_timezone_set('Asia/Jakarta');
        $waktu_keluar = date('Y-m-d H:i:s');

        $stmt = $this->koneksi->prepare(
            "UPDATE " . $this->tabel . " 
             SET waktu_keluar = ? 
             WHERE id_presensi = ?"
        );
        $stmt->bind_param("si", $waktu_keluar, $id_presensi);
        
        return $stmt->execute();
    }


    // ==========================================================
    // --- FUNGSI BARU UNTUK ADMIN/SUPERUSER (CRUD Laporan) ---
    // ==========================================================

    /**
     * Mengambil SEMUA data presensi dari SEMUA user (untuk laporan)
     * Digabung (JOIN) dengan tabel user untuk dapat nama
     */
    public function getAll() {
        $query = "
            SELECT 
                p.id_presensi, 
                p.tanggal, 
                p.waktu_masuk, 
                p.waktu_keluar, 
                p.status_presensi,
                u.nama_lengkap
            FROM 
                " . $this->tabel . " p
            JOIN 
                users u ON p.id_user = u.id_user
            ORDER BY 
                p.tanggal DESC, p.waktu_masuk DESC
        ";
        
        $result = $this->koneksi->query($query);
        
        $presensi_logs = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $presensi_logs[] = $row;
            }
        }
        return $presensi_logs;
    }

    /**
     * Mengambil satu data presensi berdasarkan ID (untuk form Edit)
     */
    public function findById($id_presensi) {
        $stmt = $this->koneksi->prepare("SELECT * FROM " . $this->tabel . " WHERE id_presensi = ?");
        $stmt->bind_param("i", $id_presensi);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Mengupdate data presensi (Admin/Superuser)
     * $data adalah array: ['tanggal', 'waktu_masuk', 'waktu_keluar', 'status_presensi']
     */
    public function update($id_presensi, $data) {
        
        // Cek untuk waktu keluar (bisa NULL jika diedit/dihapus)
        $waktu_keluar = $data['waktu_keluar'];
        if (empty($waktu_keluar)) {
            $waktu_keluar = null;
        }

        $stmt = $this->koneksi->prepare(
            "UPDATE " . $this->tabel . " 
             SET tanggal = ?, waktu_masuk = ?, waktu_keluar = ?, status_presensi = ?
             WHERE id_presensi = ?"
        );
        // ssssi: string, string, string, string, integer
        $stmt->bind_param("ssssi", 
            $data['tanggal'], 
            $data['waktu_masuk'], 
            $waktu_keluar, 
            $data['status_presensi'], 
            $id_presensi
        );
        return $stmt->execute();
    }

    /**
     * Menghapus data presensi (Admin/Superuser)
     */
    public function delete($id_presensi) {
        $stmt = $this->koneksi->prepare("DELETE FROM " . $this->tabel . " WHERE id_presensi = ?");
        $stmt->bind_param("i", $id_presensi);
        return $stmt->execute();
    }
}
?>