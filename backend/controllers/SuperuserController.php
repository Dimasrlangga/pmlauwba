<?php
// Lokasi: backend/controllers/SuperuserController.php
require_once APP_ROOT . '/helpers/auth.php'; // tambahkan ini
requireBackendAuth();
// Pastikan APP_ROOT sudah didefinisikan di index.php root
// Require model User (sesuaikan path sesuai struktur projectmu — saya asumsikan app/models/User.php)
require_once APP_ROOT . '/models/User.php';

class SuperuserController {
    private $koneksi;
    private $userModel;

    public function __construct($db) {
        $this->koneksi = $db;
        $this->userModel = new User($db);
    }

    // Dashboard (contoh)
    public function index() {
        if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['superuser', 'admin', 'manager'])) {
            header("Location: ?url=login&error=" . urlencode("Akses ditolak"));
            exit;
        }

        $judul_halaman = "Dashboard";
        $view = APP_ROOT . '/backend/views/dashboard.php';
        if (file_exists($view)) include $view;
        else echo "<h3>Dashboard view tidak ditemukan.</h3>";
    }

    // Tampilkan daftar user
    public function kelolaUser() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
        header("Location: ?url=login&error=" . urlencode("Silakan login sebagai Superuser"));
        exit;
    }

    $data_users = $this->userModel->getAll();

    $view = APP_ROOT . '/backend/views/pages/kelola_user/kelola_user.php';
    if (file_exists($view)) include $view;
    else echo "<p>View kelola_user.php tidak ditemukan.</p>";
}


    // Tampilkan form tambah
    public function showTambahForm() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
            header("Location: ?url=login&error=" . urlencode("Silakan login sebagai Superuser"));
            exit;
        }

        $view = APP_ROOT . '/backend/views/pages/kelola_user/tambah_user.php';
        if (file_exists($view)) include $view;
        else echo "<p>View tambah_user.php tidak ditemukan.</p>";
    }

    // Proses tambah (POST)
    public function prosesTambahUser() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
            header("Location: ?url=login&error=" . urlencode("Silakan login sebagai Superuser"));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?url=tambah_user&error=" . urlencode("Invalid request"));
            exit;
        }

        $nama = trim($_POST['nama_lengkap'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';

        if ($nama === '' || $username === '' || $password === '' || $role === '') {
            header("Location: ?url=tambah_user&error=" . urlencode("Semua field wajib diisi"));
            exit;
        }

        if ($this->userModel->findByUsername($username)) {
            header("Location: ?url=tambah_user&error=" . urlencode("Username '{$username}' sudah digunakan"));
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'nama_lengkap' => $nama,
            'username' => $username,
            'password_hash' => $password_hash,
            'role' => $role
        ];

        if ($this->userModel->create($data)) {
            header("Location: ?url=kelola_user&success=" . urlencode("User baru berhasil ditambahkan"));
        } else {
            header("Location: ?url=tambah_user&error=" . urlencode("Gagal menambahkan user"));
        }
        exit;
    }

    // Tampilkan form edit
    public function showEditForm() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
            header("Location: ?url=login&error=" . urlencode("Silakan login sebagai Superuser"));
            exit;
        }

        $id_user = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id_user <= 0) {
            header("Location: ?url=kelola_user&error=" . urlencode("ID user tidak valid"));
            exit;
        }

        $user = $this->userModel->findById($id_user);
        if (!$user) {
            header("Location: ?url=kelola_user&error=" . urlencode("User tidak ditemukan"));
            exit;
        }

        // $user tersedia untuk view
        $view = APP_ROOT . '/backend/views/pages/kelola_user/edit_user.php';
        if (file_exists($view)) include $view;
        else echo "<p>View edit_user.php tidak ditemukan.</p>";
    }

    // Proses edit (POST)
    public function prosesEditUser() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
            header("Location: ?url=login&error=" . urlencode("Silakan login sebagai Superuser"));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_user'])) {
            header("Location: ?url=kelola_user&error=" . urlencode("Invalid request"));
            exit;
        }

        $id_user = intval($_POST['id_user']);
        $nama = trim($_POST['nama_lengkap'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($nama === '' || $username === '' || $role === '') {
            header("Location: ?url=edit_user&id={$id_user}&error=" . urlencode("Field wajib diisi"));
            exit;
        }

        $current = $this->userModel->findById($id_user);
        if (!$current) {
            header("Location: ?url=kelola_user&error=" . urlencode("User tidak ditemukan"));
            exit;
        }

        if ($username !== $current['username'] && $this->userModel->findByUsername($username)) {
            header("Location: ?url=edit_user&id={$id_user}&error=" . urlencode("Username sudah digunakan"));
            exit;
        }

        $data = [
            'nama_lengkap' => $nama,
            'username' => $username,
            'role' => $role,
            'password_hash' => null
        ];
        if (!empty($password)) $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);

        if ($this->userModel->update($id_user, $data)) {
            header("Location: ?url=kelola_user&success=" . urlencode("User berhasil diperbarui"));
        } else {
            header("Location: ?url=edit_user&id={$id_user}&error=" . urlencode("Gagal memperbarui user"));
        }
        exit;
    }

    // Proses hapus (GET ?id=)
    public function prosesHapusUser() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'superuser') {
            header("Location: ?url=login&error=" . urlencode("Silakan login sebagai Superuser"));
            exit;
        }

        $id_user = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id_user <= 0) {
            header("Location: ?url=kelola_user&error=" . urlencode("ID user tidak valid"));
            exit;
        }

        if (isset($_SESSION['id_user']) && $id_user == $_SESSION['id_user']) {
            header("Location: ?url=kelola_user&error=" . urlencode("Anda tidak dapat menghapus akun Anda sendiri"));
            exit;
        }

        if ($this->userModel->delete($id_user)) {
            header("Location: ?url=kelola_user&success=" . urlencode("User berhasil dihapus"));
        } else {
            header("Location: ?url=kelola_user&error=" . urlencode("Gagal menghapus user. Mungkin superuser tidak dapat dihapus"));
        }
        exit;
    }
}
