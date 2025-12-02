<?php
// Lokasi: app/views/backend/edit_presensi.php

// Variabel $judul_halaman, $presensi
// didapat dari LaporanPresensiController

// Panggil header
include 'partials/header.php';

if (!$presensi) {
    echo "<div class='card'><p>Error: Data presensi tidak ditemukan.</p></div>";
    include 'partials/footer.php';
    exit;
}

// Ubah format data untuk input datetime-local (Y-m-d\TH:i)
$input_masuk = $presensi['waktu_masuk'] ? date('Y-m-d\TH:i', strtotime($presensi['waktu_masuk'])) : '';
$input_keluar = $presensi['waktu_keluar'] ? date('Y-m-d\TH:i', strtotime($presensi['waktu_keluar'])) : '';
?>

<div class="card">
    <h2>Edit Catatan Presensi</h2>
    <p>Anda sedang mengedit catatan presensi (ID: <?php echo $presensi['id_presensi']; ?>)</p>

    <form action="?url=proses_edit_presensi" method="POST">
        <input type="hidden" name="id_presensi" value="<?php echo $presensi['id_presensi']; ?>">

        <style>
            /* (CSS ini sama seperti di tambah_user.php) */
            .form-group { margin-bottom: 15px; }
            .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
            .form-group input, .form-group select {
                width: 100%; padding: 8px; border: 1px solid #ddd;
                border-radius: 4px; box-sizing: border-box;
            }
        </style>

        <?php
        if (isset($_GET['error'])) {
            echo '<p style="color:red;background:#ffebee;padding:10px;border-radius:4px;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <!-- Input tanggal menggunakan type="date" -->
            <input type="date" id="tanggal" name="tanggal" 
                   value="<?php echo htmlspecialchars($presensi['tanggal']); ?>" required>
        </div>

        <div class="form-group">
            <label for="waktu_masuk">Waktu Masuk</label>
            <!-- Input waktu menggunakan type="datetime-local" -->
            <input type="datetime-local" id="waktu_masuk" name="waktu_masuk" 
                   value="<?php echo htmlspecialchars($input_masuk); ?>" required>
        </div>

        <div class="form-group">
            <label for="waktu_keluar">Waktu Keluar</label>
            <input type="datetime-local" id="waktu_keluar" name="waktu_keluar"
                   value="<?php echo htmlspecialchars($input_keluar); ?>">
            <small>Kosongkan jika belum presensi keluar.</small>
        </div>

        <div class="form-group">
            <label for="status_presensi">Status Presensi</label>
            <select id="status_presensi" name="status_presensi" required>
                <option value="hadir" <?php if ($presensi['status_presensi'] == 'hadir') echo 'selected'; ?>>Hadir</option>
                <option value="izin" <?php if ($presensi['status_presensi'] == 'izin') echo 'selected'; ?>>Izin</option>
                <option value="sakit" <?php if ($presensi['status_presensi'] == 'sakit') echo 'selected'; ?>>Sakit</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="?url=laporan_presensi" class="btn" style="background-color:#6c757d; color:white;">Batal</a>
    </form>
</div>

<?php
// Panggil footer
include 'partials/footer.php';
?>