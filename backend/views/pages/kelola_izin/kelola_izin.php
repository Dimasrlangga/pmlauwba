<?php
// Lokasi: app/views/backend/pages/kelola_izin/kelola_izin.php

// 1. Inisialisasi Variabel (Defensive Coding)
$judul_halaman    = "Kelola Izin & Sakit";
$daftar_pengajuan = $daftar_pengajuan ?? [];
$role_user        = $_SESSION['role'] ?? 'guest';

// 2. Panggil Header & Sidebar dengan Path yang Benar
// Mundur 2 langkah (../../) dari folder 'kelola_izin' -> 'pages' -> 'views'
include '../../component/header.php';
include 'database.php';
include '../../component/sidebar.php';
?>

<div class="main-panel">
    <div class="container">
        <div class="page-inner">
            
            <div class="page-header">
                <h4 class="page-title"><?= $judul_halaman ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="?url=dashboard">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Approval Izin</a>
                    </li>
                </ul>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Daftar Pengajuan Masuk</div>
                            <p class="card-category">Setujui atau tolak pengajuan yang masuk dari peserta.</p>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="datatable table table-striped table-hover" id="tabel-izin">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tgl Diajukan</th>
                                            <th>Nama Pengaju</th>
                                            <th>Tipe</th>
                                            <th>Tanggal Izin</th>
                                            <th>Keterangan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($daftar_pengajuan)): ?>
                                            <?php $no = 1; foreach ($daftar_pengajuan as $izin): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($izin['diajukan_pada']))) ?></td>
                                                <td><span class="fw-bold"><?= htmlspecialchars($izin['nama_pengaju']) ?></span></td>
                                                <td>
                                                    <span class="badge badge-<?= $izin['tipe'] == 'sakit' ? 'danger' : 'warning' ?>">
                                                        <?= ucfirst($izin['tipe']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars(date('d M', strtotime($izin['tanggal_mulai']))) ?> s.d.
                                                    <?= htmlspecialchars(date('d M Y', strtotime($izin['tanggal_selesai']))) ?>
                                                </td>
                                                <td><?= nl2br(htmlspecialchars($izin['keterangan'])) ?></td>
                                                <td>
                                                    <?php
                                                    $status = $izin['status_approval'];
                                                    $badge_class = 'badge-secondary';
                                                    if ($status == 'disetujui') $badge_class = 'badge-success';
                                                    elseif ($status == 'ditolak') $badge_class = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?= $badge_class ?>">
                                                        <?= ucfirst($status) ?>
                                                    </span>
                                                    <?php if ($izin['nama_perespon']): ?>
                                                        <div class="text-muted small mt-1">Oleh: <?= htmlspecialchars($izin['nama_perespon']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($status == 'pending' && in_array($role_user, ['superuser', 'admin'])): ?>
                                                        <div class="form-button-action">
                                                            <a href="?url=proses_approval_izin&id=<?= $izin['id_izin'] ?>&status=disetujui" 
                                                               class="btn btn-link btn-success btn-lg"
                                                               data-bs-toggle="tooltip" title="Setujui"
                                                               onclick="return confirm('Setujui izin ini?');">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                            <a href="?url=proses_approval_izin&id=<?= $izin['id_izin'] ?>&status=ditolak" 
                                                               class="btn btn-link btn-danger btn-lg"
                                                               data-bs-toggle="tooltip" title="Tolak"
                                                               onclick="return confirm('Tolak izin ini?');">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </div>

                                                    <?php elseif ($role_user == 'manager'): ?>
                                                        <span class="text-muted"><i class="fa fa-eye"></i> View</span>

                                                    <?php elseif ($status != 'pending'): ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>

                                                    <?php if ($role_user == 'superuser'): ?>
                                                        <a href="?url=hapus_izin&id=<?= $izin['id_izin'] ?>" 
                                                           class="btn btn-link btn-danger btn-sm"
                                                           onclick="return confirm('Hapus data ini permanen?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Belum ada data pengajuan.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> </div> </div> <?php
    // 3. Panggil Footer dengan Path Benar
    include '../../component/footer.php';
    ?>
</div>