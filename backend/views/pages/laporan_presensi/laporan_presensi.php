<?php
// Lokasi: app/views/backend/views/pages/laporan_presensi/laporan_presensi.php

// 1. Panggil Header
include '../../component/header.php';

// 2. KONEKSI DATABASE (Perbaikan Error Include)
if (file_exists('database.php')) {
    include 'database.php';
} else {
    // Koneksi manual jika file tidak ada
    $koneksi = mysqli_connect("localhost", "root", "", "pmlauwba");
    if (!$koneksi) { die("Koneksi database gagal: " . mysqli_connect_error()); }
}

// 3. Inisialisasi Variabel
$judul_halaman = "Laporan Presensi Keseluruhan";
$role_user     = $_SESSION['role'] ?? 'guest';

// 4. QUERY AMBIL DATA
// Mengambil data presensi + nama user
$query = "SELECT p.*, u.nama_lengkap 
          FROM presensi p 
          JOIN users u ON p.id_user = u.id_user 
          ORDER BY p.tanggal DESC, p.waktu_masuk DESC";

$result = mysqli_query($koneksi, $query);
$daftar_presensi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $daftar_presensi[] = $row;
    }
}

// 5. Panggil Sidebar
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
                    <li class="nav-item"><a href="#">Laporan</a></li>
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
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Presensi Seluruh User</h4>
                                <button class="btn btn-primary btn-round ms-auto" onclick="window.print()">
                                    <i class="fa fa-print"></i> Cetak Laporan
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama User</th>
                                            <th>Tanggal</th>
                                            <th>Waktu Masuk</th>
                                            <th>Waktu Keluar</th>
                                            <th>Status</th>
                                            <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($daftar_presensi)): ?>
                                            <?php $no = 1; foreach ($daftar_presensi as $presensi): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <span class="fw-bold"><?= htmlspecialchars($presensi['nama_lengkap']) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars(date('d M Y', strtotime($presensi['tanggal']))) ?></td>
                                                <td>
                                                    <?= $presensi['waktu_masuk'] ? htmlspecialchars(date('H:i', strtotime($presensi['waktu_masuk']))) : '<span class="text-muted">-</span>' ?>
                                                </td>
                                                <td>
                                                    <?= $presensi['waktu_keluar'] ? htmlspecialchars(date('H:i', strtotime($presensi['waktu_keluar']))) : '<span class="text-muted">-</span>' ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $status = $presensi['status_presensi'];
                                                    $badge_class = 'badge-secondary';
                                                    
                                                    if ($status == 'hadir') {
                                                        $badge_class = 'badge-success';
                                                    } elseif ($status == 'izin') {
                                                        $badge_class = 'badge-warning';
                                                    } elseif ($status == 'sakit' || $status == 'alpha') {
                                                        $badge_class = 'badge-danger';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badge_class ?>">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                </td>
                                                
                                                <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="?url=hapus_presensi&id=<?= $presensi['id_presensi'] ?>" 
                                                           class="btn btn-link btn-danger" 
                                                           data-bs-toggle="tooltip" title="Hapus Data"
                                                           onclick="return confirm('Anda yakin ingin menghapus catatan presensi ini?');">
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
                "order": [], // Default sort dimatikan agar ikut urutan SQL
                "language": {
                    "sSearch": "Cari Data:",
                    "sEmptyTable": "Belum ada data presensi",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "oPaginate": {
                        "sNext": "Berikutnya",
                        "sPrevious": "Sebelumnya"
                    }
                }
            });
        });
    </script>
</div>