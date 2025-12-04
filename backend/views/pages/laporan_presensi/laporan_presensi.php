<?php
// File: backend/views/pages/laporan_presensi/laporan_presensi.php
// Versi aman: include via APP_ROOT, fallback koneksi, safe output

// Pastikan APP_ROOT didefinisikan (biasanya di index.php root)
if (!defined('APP_ROOT')) {
    $maybeRoot = realpath(__DIR__ . '/../../../../');
    if ($maybeRoot) define('APP_ROOT', $maybeRoot);
    else define('APP_ROOT', __DIR__);
}

// Pastikan session aktif
if (!session_id()) session_start();

// Paths component
$componentDir = APP_ROOT . '/backend/views/component';
$headerFile    = $componentDir . '/header.php';
$navbarFile    = $componentDir . '/navbar.php';
$sidebarFile   = $componentDir . '/sidebar.php';
$footerFile    = $componentDir . '/footer.php';

// Koneksi DB: gunakan $koneksi dari controller kalau tersedia, jika tidak cari database.php di root
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    $dbFile = APP_ROOT . '/database.php';
    if (file_exists($dbFile)) {
        include_once $dbFile; // file ini diharapkan mendefinisikan $koneksi (mysqli)
    } else {
        // fallback manual (ubah credential jika perlu)
        $koneksi = mysqli_connect("localhost", "root", "", "pmlauwba");
        if (!$koneksi) die("Koneksi database gagal: " . mysqli_connect_error());
    }
}

// Inisialisasi variabel aman
$judul_halaman = "Laporan Presensi Keseluruhan";
$role_user     = $_SESSION['role'] ?? 'guest';

// Ambil data presensi jika controller belum mengisi $daftar_presensi
if (!isset($daftar_presensi) || !is_array($daftar_presensi)) {
    $query = "SELECT p.*, u.nama_lengkap 
              FROM presensi p 
              JOIN users u ON p.id_user = u.id_user 
              ORDER BY p.tanggal DESC, p.waktu_masuk DESC";
    $result = mysqli_query($koneksi, $query);
    $daftar_presensi = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) $daftar_presensi[] = $row;
    }
}

// Include header & sidebar via absolute path
if (file_exists($headerFile)) include $headerFile;
else {
    echo "<!doctype html><html><head><meta charset='utf-8'><title>" . htmlspecialchars($judul_halaman) . "</title></head><body>";
}

if (file_exists($navbarFile)) include $navbarFile;

if (file_exists($sidebarFile)) include $sidebarFile;
?>

<!-- Print-only CSS: sembunyikan elemen non-tabel saat cetak -->
<style>
    /* Default: pastikan area utama cukup lebar saat cetak */
    @media print {

        /* sembunyikan elemen layout yang tidak ingin dicetak */
        .sidebar,
        .main-sidebar,
        .navbar,
        .header,
        .page-header,
        .breadcrumbs,
        .card-header .btn,
        .btn,
        footer,
        .footer,
        .nav-home,
        .breadcrumbs,
        .topbar {
            display: none !important;
        }

        /* tampilkan konten utama full-width */
        .main-panel,
        .container,
        .page-inner,
        .card,
        .card-body,
        .table-responsive {
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            background: #fff !important;
            width: 100% !important;
        }

        /* pastikan tabel terbentang dan teks cukup besar */
        table {
            font-size: 12pt !important;
            border-collapse: collapse !important;
            width: 100% !important;
        }

        table th,
        table td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
        }

        /* hilangkan elemen yang fleksibel untuk mencegah pemotongan */
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        #myChartLegend {
            display: none !important;
        }

        /* sebaiknya semua link jadi teks biasa (opsional) */
        a::after {
            content: none !important;
        }
    }

    /* Jika ingin pada layar (preview) menampilkan tombol cetak kecil */
    .print-only-btn {
        margin-left: auto;
    }
</style>


<div class="main-panel">
    <div class="container">
        <div class="page-inner">

            <div class="page-header">
                <h4 class="page-title"><?= htmlspecialchars($judul_halaman) ?></h4>
                <ul class="breadcrumbs">
                    <li class="nav-home"><a href="?url=dashboard_backend"><i class="icon-home"></i></a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Laporan</a></li>
                </ul>
            </div>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success" role="alert"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 class="card-title">Data Presensi Seluruh User</h4>
                                <button class="btn btn-primary btn-round ms-auto" onclick="printOnlyTable()">
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
                                        <?php if (!empty($daftar_presensi)): $no = 1; ?>
                                            <?php foreach ($daftar_presensi as $presensi): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><span class="fw-bold"><?= htmlspecialchars($presensi['nama_lengkap'] ?? '-') ?></span></td>
                                                    <td><?= htmlspecialchars(date('d M Y', strtotime($presensi['tanggal'] ?? 'now'))) ?></td>
                                                    <td>
                                                        <?= !empty($presensi['waktu_masuk']) ? htmlspecialchars(date('H:i', strtotime($presensi['waktu_masuk']))) : '<span class="text-muted">-</span>' ?>
                                                    </td>
                                                    <td>
                                                        <?= !empty($presensi['waktu_keluar']) ? htmlspecialchars(date('H:i', strtotime($presensi['waktu_keluar']))) : '<span class="text-muted">-</span>' ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = $presensi['status_presensi'] ?? 'hadir';
                                                        $badge_class = 'badge-secondary';
                                                        if ($status === 'hadir') $badge_class = 'badge-success';
                                                        elseif ($status === 'izin') $badge_class = 'badge-warning';
                                                        elseif ($status === 'sakit' || $status === 'alpha') $badge_class = 'badge-danger';
                                                        ?>
                                                        <span class="badge <?= $badge_class ?>"><?= ucfirst(htmlspecialchars($status)) ?></span>
                                                    </td>

                                                    <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                        <td>
                                                            <div class="form-button-action">
                                                                <form method="post" action="?page=proses_hapus_presensi" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus catatan presensi ini?');">
                                                                    <input type="hidden" name="id" value="<?= intval($presensi['id_presensi'] ?? 0) ?>">
                                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                                                    <button type="submit" class="btn btn-link btn-danger" title="Hapus Data">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </form>

                                                            </div>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="<?= in_array($role_user, ['superuser', 'admin']) ? 7 : 6 ?>" class="text-center text-muted">Belum ada data presensi.</td>
                                            </tr>
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
    if (file_exists($footerFile)) include $footerFile;
    else echo "</body></html>";
    ?>

</div>

<!-- scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#basic-datatables').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "Cari Data:",
                "emptyTable": "Belum ada data presensi",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Berikutnya"
                }
            }
        });
    });
</script>

<script>
    function getTableHtml() {
        // pilih kontainer tabel yang ingin dicetak
        var tableContainer = document.querySelector('.table-responsive');
        if (!tableContainer) return null;

        // clone supaya tidak merusak DOM asli
        var clone = tableContainer.cloneNode(true);

        // Remove DataTables pagination/search controls jika ada dalam clone
        var controls = clone.querySelectorAll('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate');
        controls.forEach(function(c) {
            c.parentNode && c.parentNode.removeChild(c);
        });

        // Pastikan header kolom tidak bergantung pada JS (beberapa plugin menambahkan elemen)
        return clone.innerHTML;
    }

    function printOnlyTable() {
        var tableHtml = getTableHtml();
        if (!tableHtml) {
            alert('Tabel tidak ditemukan untuk dicetak.');
            return;
        }

        // Buat jendela baru
        var printWindow = window.open('', '_blank', 'width=1200,height=800');
        var doc = printWindow.document;

        // Sertakan style minimal agar tabel rapi saat dicetak. Anda bisa tambahkan CDN DataTables CSS jika diperlukan.
        doc.open();
        doc.write(`
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Cetak Laporan Presensi</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- DataTables css jika ingin mengikuti style datatables (opsional) -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; color: #222; }
                h2 { text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; font-size: 12pt; }
                table th, table td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: middle; }
                thead th { background: #f6f6f6; }
                @media print {
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <h2>Laporan Presensi Keseluruhan</h2>
            <div>${tableHtml}</div>
        </body>
        </html>
    `);
        doc.close();

        // Tunggu sejenak agar browser me-render content, lalu cetak
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            // opsi: auto-close setelah cetak (ada browser yg blokir)
            // printWindow.close();
        };
    }
</script>