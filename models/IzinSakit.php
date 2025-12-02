<?php
// Lokasi: app/models/IzinSakit.php

class IzinSakit {
    private $koneksi;
    private $tabel = 'izin_sakit';

    public function __construct($db) {
        $this->koneksi = $db;
    }

    // ========================================================
    // --- FUNGSI UNTUK BACKEND (Admin/Superuser/Manager) ---
    // ========================================================

    /**
     * Mengambil SEMUA data pengajuan dari SEMUA user (untuk laporan)
     * Digabung (JOIN) dengan tabel user untuk dapat nama
     */
    public function getAll() {
        $query = "
            SELECT 
                i.*, 
                u_pengaju.nama_lengkap as nama_pengaju,
                u_perespon.nama_lengkap as nama_perespon
            FROM 
                " . $this->tabel . " i
            JOIN 
                users u_pengaju ON i.id_user = u_pengaju.id_user
            LEFT JOIN
                users u_perespon ON i.direspon_oleh = u_perespon.id_user
            ORDER BY 
                i.diajukan_pada DESC
        ";
        
        $result = $this->koneksi->query($query);
        $daftar_pengajuan = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $daftar_pengajuan[] = $row;
            }
        }
        return $daftar_pengajuan;
    }

    /**
     * Mengambil satu data pengajuan (untuk cek kepemilikan)
     */
    public function findById($id_izin) {
        $stmt = $this->koneksi->prepare("SELECT * FROM " . $this->tabel . " WHERE id_izin = ?");
        $stmt->bind_param("i", $id_izin);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Memperbarui status pengajuan (Approve/Reject)
     */
    public function updateStatus($id_izin, $status, $id_perespon) {
        $stmt = $this->koneksi->prepare(
            "UPDATE " . $this->tabel . " 
             SET status_approval = ?, direspon_oleh = ? 
             WHERE id_izin = ?"
        );
        $stmt->bind_param("sii", $status, $id_perespon, $id_izin);
        return $stmt->execute();
    }

    /**
     * Menghapus data pengajuan (Hanya Superuser)
     */
    public function deleteById($id_izin) {
        $stmt = $this->koneksi->prepare("DELETE FROM " . $this->tabel . " WHERE id_izin = ?");
        $stmt->bind_param("i", $id_izin);
        return $stmt->execute();
    }


    // ========================================================
    // --- FUNGSI BARU UNTUK FRONTEND (Peserta) ---
    // ========================================================

    /**
     * Menyimpan pengajuan izin/sakit baru dari peserta
     * $data adalah array: [id_user, tipe, tgl_mulai, tgl_selesai, keterangan, file_bukti (opsional)]
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->tabel . " 
                    (id_user, tipe, tanggal_mulai, tanggal_selesai, keterangan, file_bukti, status_approval) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("isssss",
            $data['id_user'],
            $data['tipe'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['keterangan'],
            $data['file_bukti']
        );
        return $stmt->execute();
    }

    /**
     * Mengambil riwayat pengajuan milik satu user
     */
    public function getByIdUser($id_user) {
        $query = "
            SELECT i.*, u.nama_lengkap as nama_perespon 
            FROM " . $this->tabel . " i
            LEFT JOIN users u ON i.direspon_oleh = u.id_user
            WHERE i.id_user = ?
            ORDER BY i.diajukan_pada DESC
        ";
        
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $riwayat = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $riwayat[] = $row;
            }
        }
        return $riwayat;
    }

    /**
     * Membatalkan pengajuan (oleh peserta sendiri)
     * Hanya bisa jika status masih 'pending'
     */
    public function cancelPengajuan($id_izin, $id_user) {
        $stmt = $this->koneksi->prepare(
            "DELETE FROM " . $this->tabel . " 
             WHERE id_izin = ? AND id_user = ? AND status_approval = 'pending'"
        );
        $stmt->bind_param("ii", $id_izin, $id_user);
        
        if ($stmt->execute()) {
            // Kembalikan jumlah baris yang terhapus (seharusnya 1 jika berhasil)
            return $stmt->affected_rows;
        }
        return 0;
    }
}
?>