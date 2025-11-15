<?php
require_once 'config/connection.php';

$query = "SELECT * FROM jenis_beasiswa ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Beasiswa - Sistem Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="header-tabs">
        <div class="container">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="/">Pilihan Beasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="daftar">Daftar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="hasil">Hasil</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="main-container">
            <h1 class="page-title">Pilihan Beasiswa</h1>
            <p class="page-subtitle">Pilih jenis beasiswa yang sesuai dengan kriteria Anda</p>

            <div class="row">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <?php echo htmlspecialchars($row['nama_beasiswa']); ?>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: var(--text-dark); font-size: 0.85rem;">DESKRIPSI</h6>
                            <p class="mb-4" style="color: var(--text-light); line-height: 1.7;">
                                <?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?>
                            </p>

                            <h6 class="fw-bold mb-3" style="color: var(--text-dark); font-size: 0.85rem;">PERSYARATAN</h6>
                            <div class="alert alert-info mb-4" style="line-height: 1.7;">
                                <?php echo nl2br(htmlspecialchars($row['syarat'])); ?>
                            </div>
                            <a href="daftar" class="btn btn-primary w-100">
                                Daftar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-warning">Belum ada data beasiswa tersedia.</div></div>';
                }
                ?>
            </div>

            <div class="card mt-5">
                <div class="card-header">
                    Informasi Penting
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div>
                                <strong style="color: var(--text-dark);">IPK Minimal 3.0</strong>
                                <p class="mb-0 mt-2" style="color: var(--text-light); line-height: 1.6;">Pastikan IPK Anda memenuhi syarat minimal</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div>
                                <strong style="color: var(--text-dark);">Format Berkas</strong>
                                <p class="mb-0 mt-2" style="color: var(--text-light); line-height: 1.6;">PDF, JPG, atau ZIP (Maks. 2MB)</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div>
                                <strong style="color: var(--text-dark);">Data Valid</strong>
                                <p class="mb-0 mt-2" style="color: var(--text-light); line-height: 1.6;">Pastikan semua data yang diisi benar</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div>
                                <strong style="color: var(--text-dark);">Cek Status</strong>
                                <p class="mb-0 mt-2" style="color: var(--text-light); line-height: 1.6;">Lihat status pendaftaran di menu Hasil</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2024 Sistem Pendaftaran Beasiswa</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>

