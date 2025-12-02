<?php
// Lokasi: backend/controllers/AuthController.php

// PERBAIKAN 1: Path Model harus ke root models, bukan app/models
require_once APP_ROOT . '/models/User.php';

class AuthController
{
    private $userModel;

    public function __construct($koneksi)
    {
        // Buat objek User baru
        $this->userModel = new User($koneksi);
    }

    // --- TAMBAHAN BARU KHUSUS BACKEND ---
    public function showBackendLoginForm()
    {
        // Arahkan spesifik ke file login_backend.php yang baru dibuat
        $path_view = APP_ROOT . '/backend/views/auth/login_backend.php';

        if (file_exists($path_view)) {
            require_once $path_view;
        } else {
            die("Error: File view login backend tidak ditemukan di: " . $path_view);
        }
    }

    // Fungsi untuk MENAMPILKAN halaman login
    public function showLoginForm()
    {
        // PERBAIKAN 2: Path View Login
        // Kita gunakan APP_ROOT agar path-nya absolut dan tidak error.
        // Pastikan file login.php Anda ada di lokasi ini:
        $path_view = APP_ROOT . '/backend/views/auth/login.php';

        // Cek jika file ada (opsional, untuk debugging)
        if (file_exists($path_view)) {
            require_once $path_view;
        } else {
            // Jika Anda menyimpan login.php di folder lain, ubah $path_view di atas
            die("Error: File view login tidak ditemukan di: " . $path_view);
        }
    }

    public function processLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {

            // Cek apakah user punya hak akses backend
            if (!in_array($user['role'], ['superuser', 'admin', 'manager'])) {
                header("Location: ?page=login&error=Anda tidak memiliki akses ke halaman ini");
                exit;
            }

            // --- LOGIN BERHASIL ---
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            // Redirect ke Dashboard Backend
            header("Location: ?page=dashboard_backend");
            exit;
        } else {
            // --- LOGIN GAGAL ---
            header("Location: ?page=login&error=Username atau password salah");
            exit;
        }
    }

    public function logout() {
        // 1. Kosongkan semua variabel session
        $_SESSION = [];
        
        // 2. Hapus session dari memori
        session_unset();
        
        // 3. Hancurkan session ID
        session_destroy();
        
        // 4. Redirect ke halaman login backend
        // Gunakan script JS sebagai cadangan jika header PHP macet
        echo "<script>alert('Anda berhasil logout!'); window.location.href='?page=login';</script>";
        
        // Cadangan header PHP (biasanya tidak tereksekusi jika JS di atas jalan, tapi bagus untuk keamanan)
        header("Location: ?page=login&pesan=Logout Berhasil");
        exit;
    }
}
