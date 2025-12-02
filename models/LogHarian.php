<?php
// Lokasi: app/models/LogHarian.php

class LogHarian {
    private $koneksi;
    private $tabel = 'log_harian';

    public function __construct($db) {
        $this->koneksi = $db;
    }

    // --- Fungsi untuk Peserta (Pribadi) ---

    /**
     * Mengambil semua log harian milik SATU user
     */
    public function getByIdUser($id_user) {
        $stmt = $this->koneksi->prepare(
            "SELECT * FROM " . $this->tabel . " 
             WHERE id_user = ? ORDER BY tanggal DESC"
        );
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $logs = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }
        return $logs;
    }

    /**
     * Mengambil satu data log berdasarkan ID-nya
     */
    public function findById($id_log) {
        $stmt = $this->koneksi->prepare("SELECT * FROM " . $this->tabel . " WHERE id_log = ?");
        $stmt->bind_param("i", $id_log);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Menyimpan log harian baru (versi aman, untuk peserta/pribadi)
     */
    public function create($id_user, $tanggal, $deskripsi) {
        $stmt = $this->koneksi->prepare(
            "INSERT INTO " . $this->tabel . " (id_user, tanggal, deskripsi_kegiatan) 
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $id_user, $tanggal, $deskripsi);
        
        return $stmt->execute();
    }

    /**
     * Mengupdate log harian (versi aman, untuk peserta/pribadi)
     */
    public function update($id_log, $tanggal, $deskripsi) {
        $stmt = $this->koneksi->prepare(
            "UPDATE " . $this->tabel . " 
             SET tanggal = ?, deskripsi_kegiatan = ? 
             WHERE id_log = ?"
        );
        $stmt->bind_param("ssi", $tanggal, $deskripsi, $id_log);
        
        return $stmt->execute();
    }

    /**
     * Menghapus log harian (versi aman, untuk peserta/pribadi)
     */
    public function delete($id_log, $id_user) {
        $stmt = $this->koneksi->prepare(
            "DELETE FROM " . $this->tabel . " 
             WHERE id_log = ? AND id_user = ?"
        );
        $stmt->bind_param("ii", $id_log, $id_user);
        
        return $stmt->execute();
    }

    // --- Fungsi untuk Laporan (Admin/Manager) ---

    /**
     * Mengambil SEMUA log harian dari SEMUA user (untuk Laporan)
     * Kita gabungkan (JOIN) dengan tabel user untuk dapat nama
     */
    public function getAllLogs() {
        $query = "
            SELECT 
                l.id_log, 
                l.id_user,
                l.tanggal, 
                l.deskripsi_kegiatan, 
                u.nama_lengkap
            FROM 
                " . $this->tabel . " l
            JOIN 
                users u ON l.id_user = u.id_user
            ORDER BY 
                l.tanggal DESC
        ";
        
        $result = $this->koneksi->query($query);
        
        $logs = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }
        return $logs;
    }


    // --- [BARU] FUNGSI KHUSUS SUPERUSER/ADMIN ---

    /**
     * Mengupdate log harian (Akses Admin/Superuser)
     * Tidak perlu cek id_user
     */
    public function updateAsAdmin($id_log, $tanggal, $deskripsi) {
        $stmt = $this->koneksi->prepare(
            "UPDATE " . $this->tabel . " 
             SET tanggal = ?, deskripsi_kegiatan = ? 
             WHERE id_log = ?"
        );
        $stmt->bind_param("ssi", $tanggal, $deskripsi, $id_log);
        
        return $stmt->execute();
    }

    /**
     * Menghapus log harian (Akses Admin/Superuser)
     * Tidak perlu cek id_user
     */
    public function deleteAsAdmin($id_log) {
        $stmt = $this->koneksi->prepare(
            "DELETE FROM " . $this->tabel . " 
             WHERE id_log = ?"
        );
        $stmt->bind_param("i", $id_log);
        
        return $stmt->execute();
    }
}
?>