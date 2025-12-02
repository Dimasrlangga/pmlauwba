<!-- Header Halaman -->
<div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 50px;">
    <div class="row py-5">
        <div class="col-12 pt-lg-5 mt-lg-5 text-center">
            <h1 class="display-4 text-white animated zoomIn">Ajukan Izin</h1>
            <a href="?page=dashboard_frontend" class="h5 text-white">Dashboard</a>
            <i class="far fa-circle text-white px-2"></i>
            <a href="#" class="h5 text-white">Izin & Sakit</a>
        </div>
    </div>
</div>

<!-- Konten Utama -->
<div class="container-xxl py-5">
    <div class="container">

        <!-- Notifikasi Pesan -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            
            <!-- KOLOM KIRI: FORM PENGAJUAN -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="m-0 text-white text-uppercase"><i class="fa fa-paper-plane me-2"></i> Form Pengajuan</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-4">Silakan isi formulir di bawah ini untuk mengajukan izin atau sakit.</p>
                        
                        <!-- Form -->
                        <form action="?page=proses_ajukan_izin_peserta" method="POST" enctype="multipart/form-data">
                            
                            <!-- Tipe Pengajuan -->
                            <div class="mb-3">
                                <label for="tipe" class="form-label fw-bold">Tipe Pengajuan</label>
                                <select class="form-select" id="tipe" name="tipe" required>
                                    <option value="" selected disabled>-- Pilih Tipe --</option>
                                    <option value="izin">Izin (Keperluan Pribadi)</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                            </div>

                            <!-- Tanggal Mulai -->
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label fw-bold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>

                            <!-- Tanggal Selesai -->
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label fw-bold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-bold">Keterangan / Alasan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="4" 
                                          placeholder="Contoh: Ada acara keluarga atau sakit demam..." required></textarea>
                            </div>

                            <!-- File Bukti -->
                            <div class="mb-4">
                                <label for="file_bukti" class="form-label fw-bold">Upload Bukti (Opsional)</label>
                                <input class="form-control" type="file" id="file_bukti" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text text-muted small">
                                    <i class="fa fa-info-circle"></i> Maks 5MB (JPG, PNG, PDF). <br>Wajib untuk sakit (Surat Dokter).
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2 fw-bold">
                                    <i class="fa fa-send me-2"></i> Kirim Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: TABEL RIWAYAT -->
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="text-primary m-0 text-uppercase"><i class="fa fa-history me-2"></i> Riwayat Pengajuan</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="py-3 ps-4">Tgl Diajukan</th>
                                        <th class="py-3">Tipe</th>
                                        <th class="py-3">Periode Izin</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($daftar_riwayat)): ?>
                                        <?php foreach ($daftar_riwayat as $izin): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <small class="d-block text-muted">
                                                        <i class="fa fa-calendar-alt me-1"></i>
                                                        <?php echo htmlspecialchars(date('d M Y', strtotime($izin['diajukan_pada']))); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars(date('H:i', strtotime($izin['diajukan_pada']))); ?> WIB
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($izin['tipe'] == 'sakit'): ?>
                                                        <span class="badge bg-danger rounded-pill"><i class="fa fa-clinic-medical me-1"></i> Sakit</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark rounded-pill"><i class="fa fa-user-clock me-1"></i> Izin</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="fw-bold">
                                                        <?php echo htmlspecialchars(date('d M', strtotime($izin['tanggal_mulai']))); ?>
                                                        s/d
                                                        <?php echo htmlspecialchars(date('d M Y', strtotime($izin['tanggal_selesai']))); ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted fst-italic">
                                                        "<?php echo substr(htmlspecialchars($izin['keterangan']), 0, 20) . (strlen($izin['keterangan']) > 20 ? '...' : ''); ?>"
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $izin['status_approval'];
                                                    if ($status == 'disetujui') {
                                                        echo '<span class="badge bg-success"><i class="fa fa-check me-1"></i> Disetujui</span>';
                                                    } elseif ($status == 'ditolak') {
                                                        echo '<span class="badge bg-danger"><i class="fa fa-times me-1"></i> Ditolak</span>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary"><i class="fa fa-clock me-1"></i> Menunggu</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($status == 'pending'): ?>
                                                        <!-- Tombol Batal -->
                                                        <a href="?page=proses_batal_izin_peserta&id=<?php echo $izin['id_izin']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Batalkan Pengajuan"
                                                           onclick="return confirm('Anda yakin ingin membatalkan pengajuan ini?');">
                                                            <i class="fa fa-times"></i> Batal
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" class="mb-3 opacity-50" alt="Empty">
                                                <p class="mb-0">Belum ada riwayat pengajuan.</p>
                                            </td>
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