<?php
// File: backend/views/pages/kelola_izin/kelola_izin.php
// Versi aman: include via APP_ROOT, fallback koneksi, safe output

// Pastikan APP_ROOT ada (biasanya didefinisikan di index.php root)
if (!defined('APP_ROOT')) {
    $maybeRoot = realpath(__DIR__ . '/../../../../');
    if ($maybeRoot) define('APP_ROOT', $maybeRoot);
    else define('APP_ROOT', __DIR__);
}

// Pastikan session aktif
if (!session_id()) session_start();

// Paths component
$componentDir  = APP_ROOT . '/backend/views/component';
$headerFile    = $componentDir . '/header.php';
$navbarFile    = $componentDir . '/navbar.php';    // <-- navbar ditambahkan
$sidebarFile   = $componentDir . '/sidebar.php';
$footerFile    = $componentDir . '/footer.php';

// Koneksi DB: gunakan $koneksi dari controller kalau tersedia, jika tidak cari database.php di root
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    $dbFile = APP_ROOT . '/database.php';
    if (file_exists($dbFile)) {
        include_once $dbFile; // file ini harus mendefinisikan $koneksi (mysqli)
    } else {
        // fallback manual (sesuaikan cred sesuai environment)
        $koneksi = mysqli_connect("localhost", "root", "", "pmlauwba");
        if (!$koneksi) die("Koneksi database gagal: " . mysqli_connect_error());
    }
}

// Inisialisasi variabel aman
$judul_halaman    = "Kelola Izin & Sakit";
$daftar_pengajuan = $daftar_pengajuan ?? [];
$role_user        = $_SESSION['role'] ?? 'guest';

// Jika controller belum mengisi $daftar_pengajuan, ambil dari DB
if (empty($daftar_pengajuan)) {
    $q = "SELECT i.*, u.nama_lengkap AS nama_pengaju
          FROM izin_sakit i
          JOIN users u ON i.id_user = u.id_user
          ORDER BY i.diajukan_pada DESC";
    $res = mysqli_query($koneksi, $q);
    $daftar_pengajuan = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) $daftar_pengajuan[] = $r;
    }
}

// Include header via absolute path (fallback minimal)
if (file_exists($headerFile)) {
    include $headerFile;
} else {
    echo "<!doctype html><html><head><meta charset='utf-8'><title>" . htmlspecialchars($judul_halaman) . "</title></head><body>";
}

// Include navbar jika ada (posisi: setelah header, sebelum sidebar)
// Jika navbar tidak ditemukan, kita beri fallback HTML kecil supaya tata letak tetap rapi.
if (file_exists($navbarFile)) {
    include $navbarFile;
} else {
    // fallback sederhana (sesuaikan kelas / HTML dengan template Anda)
    echo '<nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="?url=dashboard_backend">LauwbaLog</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="?url=profile">Profile</a></li>
                    <li><a href="?url=logout">Logout</a></li>
                </ul>
            </div>
          </nav>';
}

// Include sidebar
if (file_exists($sidebarFile)) include $sidebarFile;
?>

<div class="main-panel">
    <div class="container">
        <div class="page-inner">

            <div class="page-header">
                <h4 class="page-title"><?= htmlspecialchars($judul_halaman) ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="?url=dashboard_backend"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Approval Izin</a></li>
                </ul>
            </div>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
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
                                        <?php if (!empty($daftar_pengajuan)): $no = 1; ?>
                                            <?php foreach ($daftar_pengajuan as $izin): 
                                                $id_izin = intval($izin['id_izin'] ?? 0);
                                                $status = $izin['status_approval'] ?? 'pending';
                                            ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($izin['diajukan_pada'] ?? 'now'))) ?></td>
                                                    <td><span class="fw-bold"><?= htmlspecialchars($izin['nama_pengaju'] ?? '-') ?></span></td>
                                                    <td>
                                                        <span class="badge <?= ($izin['tipe'] ?? '') === 'sakit' ? 'badge-danger' : 'badge-warning' ?>">
                                                            <?= ucfirst(htmlspecialchars($izin['tipe'] ?? '-')) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars(date('d M', strtotime($izin['tanggal_mulai'] ?? 'now'))) ?> s.d.
                                                        <?= htmlspecialchars(date('d M Y', strtotime($izin['tanggal_selesai'] ?? 'now'))) ?>
                                                    </td>
                                                    <td><?= nl2br(htmlspecialchars($izin['keterangan'] ?? '')) ?></td>
                                                    <td>
                                                        <?php
                                                        $badge_class = 'badge-secondary';
                                                        if ($status === 'disetujui') $badge_class = 'badge-success';
                                                        elseif ($status === 'ditolak') $badge_class = 'badge-danger';
                                                        ?>
                                                        <span class="badge <?= $badge_class ?>"><?= ucfirst(htmlspecialchars($status)) ?></span>
                                                        <?php if (!empty($izin['nama_perespon'])): ?>
                                                            <div class="text-muted small mt-1">Oleh: <?= htmlspecialchars($izin['nama_perespon']) ?></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($status === 'pending' && in_array($role_user, ['superuser','admin'])): ?>
                                                            <div class="form-button-action">
                                                                <a href="?url=proses_approval_izin&id=<?= $id_izin ?>&status=disetujui" class="btn btn-link btn-success btn-lg" onclick="return confirm('Setujui izin ini?');" title="Setujui"><i class="fa fa-check"></i></a>
                                                                <a href="?url=proses_approval_izin&id=<?= $id_izin ?>&status=ditolak" class="btn btn-link btn-danger btn-lg" onclick="return confirm('Tolak izin ini?');" title="Tolak"><i class="fa fa-times"></i></a>
                                                            </div>

                                                        <?php elseif ($role_user === 'manager'): ?>
                                                            <span class="text-muted"><i class="fa fa-eye"></i> View</span>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>

                                                        <?php if ($role_user === 'superuser'): ?>
                                                            <a href="?url=hapus_izin&id=<?= $id_izin ?>" class="btn btn-link btn-danger btn-sm" onclick="return confirm('Hapus data ini permanen?');" title="Hapus"><i class="fa fa-trash"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" class="text-center text-muted">Belum ada data pengajuan.</td></tr>
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

    <?php
    // Include footer
    if (file_exists($footerFile)) include $footerFile;
    else echo "</body></html>";
    ?>
</div>

<!-- Optional: DataTables / JS -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function(){
        $('#tabel-izin').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "emptyTable": "Belum ada data pengajuan",
                "search": "Cari:",
                "paginate": {"previous": "Sebelumnya","next":"Selanjutnya"}
            }
        });
    });
</script>
