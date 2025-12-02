<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card shadow-lg border-0 rounded-3 mt-5">
                    <div class="card-body p-5">
                        <!-- Judul -->
                        <div class="text-center mb-4">
                            <h2 class="text-primary text-uppercase mb-2">PMLAUWBA</h2>
                            <p class="text-muted">Silakan login untuk melanjutkan</p>
                        </div>

                        <!-- Menampilkan Pesan Error (Logika Lama) -->
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger text-center mb-4 fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($_GET['error']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Menampilkan Pesan Sukses (Logout dll) -->
                        <?php if (isset($_GET['pesan'])): ?>
                            <div class="alert alert-success text-center mb-4 fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i>
                                <?= htmlspecialchars($_GET['pesan']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Form Login -->
                        <!-- Action diarahkan ke ?page=proses_login sesuai routing baru -->
                        <form action="?page=proses_login" method="POST">
                            
                            <!-- Input Username -->
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                <label for="username">Username</label>
                            </div>

                            <!-- Input Password -->
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-3 fs-5 fw-bold">
                                    <i class="fa fa-sign-in-alt me-2"></i> Login Masuk
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                
                <!-- Link Kembali (Opsional) -->
                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none text-muted">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>