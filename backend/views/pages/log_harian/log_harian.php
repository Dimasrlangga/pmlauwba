<?php
// Lokasi: app/views/backend/views/pages/log_harian/log_harian.php

// 1. Panggil Header
include '../../component/header.php';

// 2. Koneksi Database (Perbaikan Error include)
if (file_exists('database.php')) {
    include 'database.php';
} else {
    $koneksi = mysqli_connect("localhost", "root", "", "pmlauwba");
    if (!$koneksi) { die("Koneksi database gagal: " . mysqli_connect_error()); }
}

// 3. Inisialisasi Variabel
$judul_halaman = "Laporan Log Aktivitas";
$role_user     = $_SESSION['role'] ?? 'guest'; 
$id_user_login = $_SESSION['id_user'] ?? 0; // Penting untuk input data
$log_edit      = null;

// 4. Logika Ambil Data (JOIN dengan tabel users untuk dapat nama)
// Kita ambil id_log, tanggal, deskripsi, dan nama_lengkap dari tabel users
$query = "SELECT l.*, u.nama_lengkap 
          FROM log_harian l 
          JOIN users u ON l.id_user = u.id_user 
          ORDER BY l.tanggal DESC, l.created_at DESC";
$result = mysqli_query($koneksi, $query);

$daftar_log = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $daftar_log[] = $row;
    }
}

// 5. Logika Ambil Data Edit (Jika ada parameter ?action=edit&id=...)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_edit = $_GET['id'];
    $q_edit  = "SELECT l.*, u.nama_lengkap FROM log_harian l JOIN users u ON l.id_user = u.id_user WHERE id_log = '$id_edit'";
    $r_edit  = mysqli_query($koneksi, $q_edit);
    $log_edit = mysqli_fetch_assoc($r_edit);
}

// 6. Panggil Sidebar
include '../../component/sidebar.php';
?>

<div class="main-panel">
    <div class="container">
        <div class="page-inner">
            
            <div class="page-header">
                <h4 class="page-title"><?= $judul_halaman ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="?url=dashboard"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Log Aktivitas</a></li>
                </ul>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" role="alert"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <div class="row">
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <?php echo $log_edit ? 'Edit Log Aktivitas' : 'Isi Log Aktivitas Harian'; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="index.php?url=proses_simpan_log" method="POST">
                                
                                <input type="hidden" name="id_user" value="<?= $id_user_login ?>">
                                <?php if ($log_edit): ?>
                                    <input type="hidden" name="id_log" value="<?= $log_edit['id_log']; ?>">
                                    <input type="hidden" name="mode" value="edit"> 
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                                   value="<?= htmlspecialchars($log_edit['tanggal'] ?? date('Y-m-d')); ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi Kegiatan</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi_kegiatan" rows="2" required placeholder="Apa yang Anda kerjakan hari ini?"><?= htmlspecialchars($log_edit['deskripsi_kegiatan'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-action">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> <?php echo $log_edit ? 'Simpan Perubahan' : 'Simpan Log'; ?>
                                    </button>
                                    
                                    <?php if ($log_edit): ?>
                                        <a href="?url=log_harian" class="btn btn-danger">Batal Edit</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Riwayat Aktivitas (Semua User)</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No</th>
                                            <th>Nama User</th>
                                            <th>Tanggal</th>
                                            <th>Deskripsi Kegiatan</th>
                                            <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                <th style="width: 15%">Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($daftar_log)): ?>
                                            <?php $no = 1; foreach ($daftar_log as $log): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <span class="fw-bold"><?= htmlspecialchars($log['nama_lengkap']) ?></span>
                                                </td>
                                                <td><?= date('d M Y', strtotime($log['tanggal'])) ?></td>
                                                <td><?= nl2br(htmlspecialchars($log['deskripsi_kegiatan'])) ?></td>
                                                
                                                <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="?url=log_harian&action=edit&id=<?= $log['id_log'] ?>" 
                                                           class="btn btn-link btn-warning btn-lg"
                                                           data-bs-toggle="tooltip" title="Edit Log">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="?url=proses_hapus_log&id=<?= $log['id_log'] ?>" 
                                                           class="btn btn-link btn-danger" 
                                                           data-bs-toggle="tooltip" title="Hapus Log"
                                                           onclick="return confirm('Yakin ingin menghapus log ini?');">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
        </div> 
    </div>
    
    <?php include '../../component/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#basic-datatables').DataTable({
                "pageLength": 10,
                "order": [], // Matikan default sort agar sesuai urutan SQL (terbaru di atas)
                "language": {
                    "sEmptyTable":   "Tidak ada data log aktivitas",
                    "sProcessing":   "Sedang memproses...",
                    "sLengthMenu":   "Tampilkan _MENU_ data",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 data",
                    "sInfoFiltered": "(disaring dari _MAX_ data keseluruhan)",
                    "sSearch":       "Cari:",
                    "oPaginate": {
                        "sFirst":    "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext":     "Selanjutnya",
                        "sLast":     "Terakhir"
                    }
                }
            });
        });
    </script>
</div>