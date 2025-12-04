<?php
// File: backend/views/pages/log_harian/log_harian.php
// Versi bersih: tidak ada query database, data sudah disiapkan controller

// Pastikan session aktif
if (!session_id()) session_start();

// Path ke component (header/sidebar/footer)
$componentDir = APP_ROOT . '/backend/views/component';
$headerFile    = $componentDir . '/header.php';
$navbarFile    = $componentDir . '/navbar.php';
$sidebarFile   = $componentDir . '/sidebar.php';
$footerFile    = $componentDir . '/footer.php';

// Inisialisasi variabel aman (data sudah dikirim dari controller)
$judul_halaman = $judul_halaman ?? "Laporan Log Aktivitas";
$role_user     = $_SESSION['role'] ?? 'guest';
$id_user_login = intval($_SESSION['id_user'] ?? 0);
$log_edit      = $log_edit ?? null;
$daftar_log    = $daftar_log ?? [];

// Include header & sidebar
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
                    <li class="nav-home">
                        <a href="?url=dashboard_backend"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Log Aktivitas</a></li>
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
                            <div class="card-title"><?= $log_edit ? 'Edit Log Aktivitas' : 'Isi Log Aktivitas Harian' ?></div>
                        </div>
                        <div class="card-body">
                            <form action="?url=proses_simpan_log" method="POST">
                                <input type="hidden" name="id_user" value="<?= $id_user_login ?>">
                                <?php if ($log_edit): ?>
                                    <input type="hidden" name="id_log" value="<?= intval($log_edit['id_log']) ?>">
                                    <input type="hidden" name="mode" value="edit">
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                                value="<?= htmlspecialchars($log_edit['tanggal'] ?? date('Y-m-d')) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi Kegiatan</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi_kegiatan" rows="2" required placeholder="Apa yang Anda kerjakan hari ini?"><?= htmlspecialchars($log_edit['deskripsi_kegiatan'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-action">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> <?= $log_edit ? 'Simpan Perubahan' : 'Simpan Log' ?>
                                    </button>

                                    <?php if ($log_edit): ?>
                                        <a href="?url=log_harian" class="btn btn-danger">Batal Edit</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- tabel riwayat -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Riwayat Aktivitas (Semua User)</h4>
                            <button class="btn btn-primary btn-round ms-auto" onclick="printOnlyTable('log-table-container', 'Laporan Log Aktivitas')">
                                <i class="fa fa-print"></i> Cetak Laporan
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th>Nama User</th>
                                            <th>Tanggal</th>
                                            <th>Deskripsi Kegiatan</th>
                                            <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                <th style="width:15%">Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($daftar_log)): $no = 1; ?>
                                            <?php foreach ($daftar_log as $log): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><span class="fw-bold"><?= htmlspecialchars($log['nama_lengkap']) ?></span></td>
                                                    <td><?= htmlspecialchars(date('d M Y', strtotime($log['tanggal']))) ?></td>
                                                    <td><?= nl2br(htmlspecialchars($log['deskripsi_kegiatan'])) ?></td>
                                                    <?php if (in_array($role_user, ['superuser', 'admin'])): ?>
                                                        <td>
                                                            <div class="form-button-action">
                                                                <a href="?url=log_harian&action=edit&id=<?= intval($log['id_log']) ?>" class="btn btn-link btn-warning btn-lg" title="Edit Log">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="?url=proses_hapus_log&id=<?= intval($log['id_log']) ?>" class="btn btn-link btn-danger" title="Hapus Log" onclick="return confirm('Yakin ingin menghapus log ini?');">
                                                                    <i class="fa fa-times"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="<?= in_array($role_user, ['superuser', 'admin']) ? 5 : 4 ?>" class="text-center">Belum ada data log.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- row -->

        </div> <!-- page-inner -->
    </div> <!-- container -->
    <?php
    // include footer atau tutup HTML jika tidak ada footer
    if (file_exists($footerFile)) {
        include $footerFile;
    } else {
        echo "</body></html>";
    }
    ?>
</div> <!-- main-panel -->

<!-- datatables scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#basic-datatables').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "emptyTable": "Tidak ada data log aktivitas",
                "lengthMenu": "Tampilkan _MENU_ data",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya",
                    "last": "Terakhir"
                }
            }
        });
    });
</script>

<script>
    function getTableHtml(containerId) {
        // Pilih kontainer tabel yang ingin dicetak (prioritaskan id jika diberikan)
        var tableContainer;
        if (containerId) {
            tableContainer = document.getElementById(containerId);
        }
        if (!tableContainer) {
            // fallback: ambil .table-responsive pertama
            tableContainer = document.querySelector('.table-responsive');
        }
        if (!tableContainer) return null;

        // clone supaya tidak merusak DOM asli
        var clone = tableContainer.cloneNode(true);

        // Remove DataTables pagination/search controls jika ada dalam clone
        var controls = clone.querySelectorAll('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate');
        controls.forEach(function(c) {
            c.parentNode && c.parentNode.removeChild(c);
        });

        // Hapus atribut style yang bisa mempengaruhi cetak (opsional)
        var elemsWithStyle = clone.querySelectorAll('[style]');
        elemsWithStyle.forEach(function(el) {
            el.removeAttribute('style');
        });

        return clone.innerHTML;
    }

    function printOnlyTable(containerId, titleText) {
        var tableHtml = getTableHtml(containerId);
        if (!tableHtml) {
            alert('Tabel tidak ditemukan untuk dicetak.');
            return;
        }

        titleText = titleText || 'Laporan';

        // Buat jendela baru
        var printWindow = window.open('', '_blank', 'width=1200,height=800');
        var doc = printWindow.document;

        // Sertakan style minimal agar tabel rapi saat dicetak.
        doc.open();
        doc.write(`
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>${titleText}</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; color: #222; }
                .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
                .header .title { font-size:18px; font-weight:700; }
                table { width: 100%; border-collapse: collapse; font-size: 11pt; }
                table th, table td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: middle; }
                thead th { background: #f6f6f6; }
                @media print {
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">${titleText}</div>
                <div class="meta">Dicetak: ${new Date().toLocaleString()}</div>
            </div>
            <div>${tableHtml}</div>
        </body>
        </html>
    `);
        doc.close();

        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
        };
    }
</script>