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
        requireBackendAuth();
        $this->userModel = new User($koneksi);
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

    // Fungsi untuk MEMPROSES data login dari form
    public function processLogin()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {

            // --- LOGIN BERHASIL ---
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            if (in_array($user['role'], ['superuser', 'admin', 'manager'])) {
                header("Location: ?page=dashboard_backend");
            } else {
                header("Location: ?page=dashboard_frontend");
            }
            exit;

            // PERBAIKAN 3: Ubah '?url=' menjadi '?page='
            // Karena index.php Anda yang baru menggunakan logika $_GET['page']
            switch ($user['role']) {
                case 'superuser':
                case 'admin':
                case 'manager':
                    header("Location: ?page=dashboard_backend");
                    break;
                case 'peserta':
                    header("Location: ?page=dashboard_frontend");
                    break;
                default:
                    header("Location: ?page=login&error=Role tidak dikenal");
                    break;
            }
            exit;
        } else {
            // --- LOGIN GAGAL ---
            header("Location: ?page=login&error=Username atau password salah");
            exit;
        }
    }

    // Fungsi untuk Logout
    public function logout()
    {
        session_unset();
        session_destroy();

        // Redirect kembali ke login
        header("Location: ?page=login&pesan=Anda telah logout");
        exit;
    }
}
