<?php
// Lokasi: frontend/controllers/PesertaController.php

// Panggil Model (Jalur sudah disesuaikan ke ROOT/models)
require_once APP_ROOT . '/models/Presensi.php';
require_once APP_ROOT . '/models/LogHarian.php';
require_once APP_ROOT . '/models/IzinSakit.php';

class PesertaController {
    
    private $koneksi;
    private $presensiModel;
    private $logHarianModel;
    private $izinSakitModel;

    public function __construct($db) {
        $this->koneksi = $db;
        $this->presensiModel = new Presensi($db);
        $this->logHarianModel = new LogHarian($db);
        $this->izinSakitModel = new IzinSakit($db);
    }

    /**
     * Menampilkan halaman Dashboard Frontend
     */
    public function index() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login&error=Akses ditolak"); // Ubah ?url jadi ?page
            exit;
        }
        // PERBAIKAN JALUR VIEW: frontend/views/pages/
        include APP_ROOT . '/frontend/views/pages/dashboard.php';
    }

    // ======================================================
    // --- FUNGSI PRESENSI ---
    // ======================================================

    public function presensi() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Presensi Harian";
        $halaman_aktif = "presensi";
        $id_user = $_SESSION['id_user'];
        
        $presensi_hari_ini = $this->presensiModel->checkPresensiHariIni($id_user);
        
        $sudah_masuk = false;
        $sudah_keluar = false;
        $waktu_masuk = null;
        $id_presensi = null;

        if ($presensi_hari_ini) {
            $sudah_masuk = true;
            $waktu_masuk = $presensi_hari_ini['waktu_masuk'];
            $id_presensi = $presensi_hari_ini['id_presensi'];
            if ($presensi_hari_ini['waktu_keluar'] != null) {
                $sudah_keluar = true;
            }
        }
        
        // PERBAIKAN JALUR VIEW
        include APP_ROOT . '/frontend/views/pages/presensi_peserta.php';
    }

    public function prosesMasuk() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login"); exit;
        }

        $id_user = $_SESSION['id_user'];
        $cek = $this->presensiModel->checkPresensiHariIni($id_user);
        
        if (!$cek) {
            $this->presensiModel->presensiMasuk($id_user);
            header("Location: ?page=presensi_peserta&success=Presensi masuk berhasil dicatat.");
        } else {
            header("Location: ?page=presensi_peserta&error=Anda sudah melakukan presensi masuk hari ini.");
        }
        exit;
    }

    public function prosesKeluar() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login"); exit;
        }
        
        $id_presensi = $_POST['id_presensi'] ?? 0;
        
        if ($id_presensi > 0) {
            $this->presensiModel->presensiKeluar($id_presensi);
            header("Location: ?page=presensi_peserta&success=Presensi keluar berhasil dicatat.");
        } else {
            header("Location: ?page=presensi_peserta&error=Terjadi kesalahan.");
        }
        exit;
    }

    // ======================================================
    // --- FUNGSI LOG HARIAN ---
    // ======================================================

    public function logHarian() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Log Harian Peserta";
        $halaman_aktif = "log";
        $id_user = $_SESSION['id_user'];
        $log_untuk_diedit = null; 

        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $id_log = $_GET['id'];
            $log_untuk_diedit = $this->logHarianModel->findById($id_log);
            
            if (!$log_untuk_diedit || $log_untuk_diedit['id_user'] != $id_user) {
                $log_untuk_diedit = null; 
                header("Location: ?page=log_harian_peserta&error=Log tidak ditemukan.");
                exit;
            }
        }
        $daftar_log = $this->logHarianModel->getByIdUser($id_user);
        
        // PERBAIKAN JALUR VIEW
        include APP_ROOT . '/frontend/views/pages/log_harian_peserta.php';
    }

    public function prosesSimpanLog() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login"); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_SESSION['id_user'];
            $tanggal = $_POST['tanggal'];
            $deskripsi = $_POST['deskripsi'];
            $id_log = $_POST['id_log'] ?? null; 

            if (empty($tanggal) || empty($deskripsi)) {
                header("Location: ?page=log_harian_peserta&error=Tanggal dan deskripsi tidak boleh kosong.");
                exit;
            }

            if ($id_log) {
                $log = $this->logHarianModel->findById($id_log);
                if ($log && $log['id_user'] == $id_user) {
                    if ($this->logHarianModel->update($id_log, $tanggal, $deskripsi)) {
                        header("Location: ?page=log_harian_peserta&success=Log berhasil diperbarui.");
                    } else {
                        header("Location: ?page=log_harian_peserta&error=Gagal memperbarui log.");
                    }
                } else {
                    header("Location: ?page=log_harian_peserta&error=Akses ditolak.");
                }
            } else {
                if ($this->logHarianModel->create($id_user, $tanggal, $deskripsi)) {
                    header("Location: ?page=log_harian_peserta&success=Log baru berhasil disimpan.");
                } else {
                    header("Location: ?page=log_harian_peserta&error=Gagal menyimpan log.");
                }
            }
            exit;
        }
    }

    public function prosesHapusLog() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login"); exit;
        }

        $id_log = $_GET['id'] ?? 0;
        $id_user = $_SESSION['id_user'];

        if ($id_log > 0) {
            if ($this->logHarianModel->delete($id_log, $id_user)) {
                header("Location: ?page=log_harian_peserta&success=Log berhasil dihapus.");
            } else {
                header("Location: ?page=log_harian_peserta&error=Gagal menghapus log.");
            }
        } else {
            header("Location: ?page=log_harian_peserta&error=ID log tidak valid.");
        }
        exit;
    }

    // ======================================================
    // --- FUNGSI IZIN/SAKIT ---
    // ======================================================
    
    public function izinSakit() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login&error=Akses ditolak");
            exit;
        }

        $judul_halaman = "Ajukan Izin / Sakit";
        $halaman_aktif = "izin";
        $id_user = $_SESSION['id_user'];
        
        $daftar_riwayat = $this->izinSakitModel->getByIdUser($id_user); 

        // PERBAIKAN JALUR VIEW
        include APP_ROOT . '/frontend/views/pages/izin_peserta.php';
    }

    public function prosesAjukanIzin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login"); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_SESSION['id_user'];
            $tipe = $_POST['tipe'];
            $tgl_mulai = $_POST['tanggal_mulai'];
            $tgl_selesai = $_POST['tanggal_selesai'];
            $keterangan = $_POST['keterangan'];
            $nama_file_bukti = null;

            if (isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] == 0) {
                // Upload path relatif dari index.php
                $upload_dir = 'uploads/'; 
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file = $_FILES['file_bukti'];
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

                if (in_array($file_ext, $allowed_ext)) {
                    if ($file_size < 5000000) { 
                        $nama_file_bukti = uniqid('bukti_', true) . '.' . $file_ext;
                        $upload_path = $upload_dir . $nama_file_bukti;

                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            // Berhasil
                        } else {
                            header("Location: ?page=izin_peserta&error=Gagal memindahkan file.");
                            exit;
                        }
                    } else {
                        header("Location: ?page=izin_peserta&error=Ukuran file terlalu besar (Maks 5MB).");
                        exit;
                    }
                } else {
                    header("Location: ?page=izin_peserta&error=Format file tidak diizinkan (Hanya JPG, PNG, PDF).");
                    exit;
                }
            }

            $data = [
                'id_user' => $id_user,
                'tipe' => $tipe,
                'tanggal_mulai' => $tgl_mulai,
                'tanggal_selesai' => $tgl_selesai,
                'keterangan' => $keterangan,
                'file_bukti' => $nama_file_bukti
            ];

            if ($this->izinSakitModel->create($data)) {
                header("Location: ?page=izin_peserta&success=Pengajuan berhasil dikirim.");
            } else {
                header("Location: ?page=izin_peserta&error=Gagal mengirim pengajuan.");
            }
            exit;
        }
    }

    public function prosesBatalIzin() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'peserta') {
            header("Location: ?page=login"); exit;
        }

        $id_izin = $_GET['id'] ?? 0;
        $id_user = $_SESSION['id_user'];

        if ($id_izin > 0) {
            $izin_data = $this->izinSakitModel->findById($id_izin);
            $rows_deleted = $this->izinSakitModel->cancelPengajuan($id_izin, $id_user);

            if ($rows_deleted > 0) {
                if ($izin_data && !empty($izin_data['file_bukti'])) {
                    $file_path = 'uploads/' . $izin_data['file_bukti'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                header("Location: ?page=izin_peserta&success=Pengajuan berhasil dibatalkan.");
            } else {
                header("Location: ?page=izin_peserta&error=Gagal membatalkan pengajuan (mungkin sudah diproses).");
            }
        } else {
            header("Location: ?page=izin_peserta&error=ID tidak valid.");
        }
        exit;
    }

} 
?>