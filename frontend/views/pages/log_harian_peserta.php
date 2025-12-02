<!-- Header Halaman -->
<div class="container-fluid bg-primary py-5 bg-header" style="margin-bottom: 50px;">
    <div class="row py-5">
        <div class="col-12 pt-lg-5 mt-lg-5 text-center">
            <h1 class="display-4 text-white animated zoomIn">Log Harian</h1>
            <a href="?page=dashboard_frontend" class="h5 text-white">Dashboard</a>
            <i class="far fa-circle text-white px-2"></i>
            <a href="#" class="h5 text-white">Log Aktivitas</a>
        </div>
    </div>
</div>

<!-- Konten Log Harian -->
<div class="container-xxl py-5">
    <div class="container">
        
        <!-- Notifikasi Pesan -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_GET['error']) ?>
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
            
            <!-- KOLOM KIRI: FORM INPUT -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="m-0 text-white text-uppercase">
                            <i class="fa fa-edit me-2"></i> 
                            <?php echo $log_untuk_diedit ? 'Edit Log' : 'Tambah Log'; ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="?page=proses_simpan_log_peserta" method="POST">
                            
                            <!-- Jika mode edit, simpan ID -->
                            <?php if ($log_untuk_diedit): ?>
                                <input type="hidden" name="id_log" value="<?php echo $log_untuk_diedit['id_log']; ?>">
                                <div class="alert alert-warning py-2 small">
                                    <i class="fa fa-info-circle"></i> Mengedit data tanggal: <b><?php echo htmlspecialchars($log_untuk_diedit['tanggal']); ?></b>
                                </div>
                            <?php endif; ?>

                            <!-- Input Tanggal -->
                            <div class="mb-3">
                                <label for="tanggal" class="form-label fw-bold">Tanggal Kegiatan</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                       value="<?php echo htmlspecialchars($log_untuk_diedit['tanggal'] ?? date('Y-m-d')); ?>" 
                                       required>
                            </div>

                            <!-- Input Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label fw-bold">Deskripsi Kegiatan</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="6" 
                                          placeholder="Jelaskan apa yang Anda kerjakan hari ini..." required><?php echo htmlspecialchars($log_untuk_diedit['deskripsi_kegiatan'] ?? ''); ?></textarea>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-2">
                                    <i class="fa fa-save me-2"></i> 
                                    <?php echo $log_untuk_diedit ? 'Simpan Perubahan' : 'Simpan Log Baru'; ?>
                                </button>
                                
                                <?php if ($log_untuk_diedit): ?>
                                    <a href="?page=log_harian_peserta" class="btn btn-outline-secondary py-2">Batal Edit</a>
                                <?php endif; ?>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: TABEL DATA -->
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.3s">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="text-primary m-0 text-uppercase"><i class="fa fa-list me-2"></i> Riwayat Aktivitas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="py-3 ps-4" width="5%">No</th>
                                        <th class="py-3" width="20%">Tanggal</th>
                                        <th class="py-3">Deskripsi Kegiatan</th>
                                        <th class="py-3 text-center" width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($daftar_log)): ?>
                                        <?php $no = 1; foreach ($daftar_log as $log): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-muted"><?php echo $no++; ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fa fa-calendar-alt me-1"></i>
                                                        <?php echo htmlspecialchars(date('d M Y', strtotime($log['tanggal']))); ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted small">
                                                    <?php echo nl2br(htmlspecialchars($log['deskripsi_kegiatan'])); ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="?page=log_harian_peserta&action=edit&id=<?php echo $log['id_log']; ?>" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="?page=proses_hapus_log_peserta&id=<?php echo $log['id_log']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Hapus"
                                                           onclick="return confirm('Apakah Anda yakin ingin menghapus log ini?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="80" class="mb-3 opacity-50" alt="Empty">
                                                <p class="mb-0">Belum ada catatan log aktivitas.</p>
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