<?php
// Lokasi: app/models/User.php

class User {
    private $koneksi;

    // Saat class User dipanggil, kita butuh koneksi database
    public function __construct($db) {
        $this->koneksi = $db;
    }

    // Fungsi untuk mencari user berdasarkan username
    public function findByUsername($username) {
        // 1. Siapkan query SQL untuk mencegah SQL Injection
        $stmt = $this->koneksi->prepare("SELECT * FROM users WHERE username = ?");
        
        // 2. 'Bind' parameter username ke query
        // "s" berarti tipe datanya adalah string
        $stmt->bind_param("s", $username);
        
        // 3. Eksekusi query
        $stmt->execute();
        
        // 4. Ambil hasilnya
        $result = $stmt->get_result();
        
        // 5. Kembalikan data user sebagai array (atau null jika tidak ditemukan)
        return $result->fetch_assoc();
    }

    public function getAll() {
        $query = "SELECT id_user, nama_lengkap, username, role FROM users ORDER BY created_at DESC";
        $result = $this->koneksi->query($query);
        
        $users = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }

    // (Nanti kita akan tambahkan fungsi createUser, updateUser, dll di sini)

    public function create($data) {
        // 1. Ambil data
        $nama_lengkap = $data['nama_lengkap'];
        $username = $data['username'];
        $password_hash = $data['password_hash'];
        $role = $data['role'];

        // 2. Siapkan query (mencegah SQL Injection)
        $stmt = $this->koneksi->prepare(
            "INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)"
        );
        
        // "ssss" berarti 4 parameter semuanya adalah string
        $stmt->bind_param("ssss", $nama_lengkap, $username, $password_hash, $role);
        
        // 3. Eksekusi
        if ($stmt->execute()) {
            return true; // Berhasil
        } else {
            return false; // Gagal (misal: username sudah ada/duplikat)
        }
    }

    /**
     * Mengambil satu user berdasarkan ID
     */
    public function findById($id_user) {
        $stmt = $this->koneksi->prepare("SELECT * FROM users WHERE id_user = ?");
        $stmt->bind_param("i", $id_user); // "i" berarti tipe datanya integer
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Kembalikan data 1 user
    }

    /**
     * Mengupdate data user
     * $data adalah array asosiatif
     */
    public function update($id_user, $data) {
        $nama_lengkap = $data['nama_lengkap'];
        $username = $data['username'];
        $role = $data['role'];

        // Cek apakah password juga diupdate
        if (!empty($data['password_hash'])) {
            // Jika ada password baru
            $password_hash = $data['password_hash'];
            $stmt = $this->koneksi->prepare(
                "UPDATE users SET nama_lengkap = ?, username = ?, password = ?, role = ? WHERE id_user = ?"
            );
            // "ssssi" - 4 string, 1 integer
            $stmt->bind_param("ssssi", $nama_lengkap, $username, $password_hash, $role, $id_user);
        } else {
            // Jika password tidak diubah
            $stmt = $this->koneksi->prepare(
                "UPDATE users SET nama_lengkap = ?, username = ?, role = ? WHERE id_user = ?"
            );
            // "sssi" - 3 string, 1 integer
            $stmt->bind_param("sssi", $nama_lengkap, $username, $role, $id_user);
        }

        // Eksekusi
        if ($stmt->execute()) {
            return true; // Berhasil
        } else {
            return false; // Gagal (misal: username duplikat)
        }
    }

    public function delete($id_user) {
        // Hati-hati: Superuser tidak boleh dihapus
        $user = $this->findById($id_user);
        if ($user && $user['role'] == 'superuser') {
            return false; // Gagal, superuser tidak boleh dihapus
        }

        $stmt = $this->koneksi->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->bind_param("i", $id_user); // "i" untuk integer
        
        if ($stmt->execute()) {
            return true; // Berhasil dihapus
        } else {
            return false; // Gagal
        }
    }
}
?>