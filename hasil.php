<?php
require_once 'config/connection.php';

$query = "SELECT * FROM pendaftaran_beasiswa ORDER BY tanggal_daftar DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

$total_pendaftar = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pendaftaran - Sistem Pendaftaran</title>
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
                    <a class="nav-link" href="/">Pilihan Beasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="daftar">Daftar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="hasil">Hasil</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="main-container">
            <h1 class="page-title">Hasil Pendaftaran</h1>
            <p class="page-subtitle">Data mahasiswa yang telah mendaftar beasiswa</p>

            <div class="alert alert-info mb-4">
                Total Pendaftar: <strong><?php echo $total_pendaftar; ?></strong> orang
            </div>

            <div class="card">
                <div class="card-header">
                    Data Pendaftar Beasiswa
                </div>
                <div class="card-body p-0">
                    <?php if ($total_pendaftar > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Nomor HP</th>
                                    <th class="text-center">Semester</th>
                                    <th class="text-center">IPK</th>
                                    <th>Pilihan Beasiswa</th>
                                    <th class="text-center">Berkas</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nomor_hp']); ?></td>
                                    <td class="text-center"><?php echo $row['semester']; ?></td>
                                    <td class="text-center">
                                        <strong><?php echo number_format($row['ipk'], 2); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['pilihan_beasiswa']); ?></td>
                                    <td class="text-center">
                                        <a href="uploads/<?php echo htmlspecialchars($row['berkas']); ?>"
                                           target="_blank" class="btn btn-sm btn-secondary">
                                            LIHAT
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status = $row['status_ajuan'];
                                        $badge_class = 'warning';

                                        if ($status == 'diverifikasi') {
                                            $badge_class = 'success';
                                        } elseif ($status == 'ditolak') {
                                            $badge_class = 'danger';
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>">
                                            <?php echo strtoupper($status); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php echo date('d/m/Y H:i', strtotime($row['tanggal_daftar'])); ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning text-center m-4">
                        <h5>BELUM ADA DATA PENDAFTAR</h5>
                        <p class="mb-3">Silakan daftar beasiswa terlebih dahulu</p>
                        <a href="daftar" class="btn btn-primary">
                            Daftar Sekarang
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    Keterangan Status Ajuan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-3">PROSES</span>
                                <div>
                                    <strong>Belum Diverifikasi</strong>
                                    <p class="small mb-0" style="color: var(--grey);">Sedang dalam proses verifikasi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-3">DITERIMA</span>
                                <div>
                                    <strong>Diverifikasi</strong>
                                    <p class="small mb-0" style="color: var(--grey);">Pendaftaran diterima</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-3">DITOLAK</span>
                                <div>
                                    <strong>Ditolak</strong>
                                    <p class="small mb-0" style="color: var(--grey);">Tidak memenuhi syarat</p>
                                </div>
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

