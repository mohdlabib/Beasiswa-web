<?php
/**
 * File: daftar.php
 * Deskripsi: Halaman form pendaftaran beasiswa
 * Fungsi: Menampilkan form registrasi beasiswa dengan validasi IPK
 *
 * Fitur:
 * - Input data mahasiswa (nama, email, nomor HP, semester)
 * - IPK otomatis dari konstanta (tidak bisa diinput manual)
 * - Validasi IPK minimal 3.0
 * - Auto-disable form jika IPK < 3.0
 * - Auto-focus ke pilihan beasiswa jika IPK >= 3.0
 * - Upload berkas syarat (PDF/JPG/ZIP max 2MB)
 * - Validasi client-side dan server-side
 */

// Include file koneksi database
require_once 'config/connection.php';

/**
 * Konstanta IPK Mahasiswa
 * Nilai IPK didapat dari sistem secara otomatis
 * Ubah nilai ini untuk testing dengan IPK berbeda
 * Contoh: 3.4 (memenuhi syarat) atau 2.9 (tidak memenuhi syarat)
 */
define('IPK_MAHASISWA', 3.4);

// Cek apakah IPK memenuhi syarat minimal (3.0)
$ipk_memenuhi = IPK_MAHASISWA >= 3.0;

// Query untuk mengambil data jenis beasiswa dari database
$query = "SELECT * FROM jenis_beasiswa ORDER BY id ASC";
$result = mysqli_query($conn, $query);

// Inisialisasi variabel untuk pesan notifikasi
$message = '';
$message_type = '';

// Cek status dari URL parameter (redirect dari proses_daftar.php)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = 'Pendaftaran beasiswa berhasil! Data Anda telah tersimpan.';
        $message_type = 'success';
    } elseif ($_GET['status'] == 'error') {
        $message = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Beasiswa - Sistem Pendaftaran</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation Tabs -->
    <div class="header-tabs">
        <div class="container">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="/">Pilihan Beasiswa</a>
                </li>
                <li class="nav-item">
                    <!-- Tab aktif untuk halaman Daftar -->
                    <a class="nav-link active" href="daftar">Daftar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="hasil">Hasil</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="main-container">
            <!-- Page Title -->
            <h1 class="page-title">Daftar Beasiswa</h1>
            <p class="page-subtitle">Lengkapi formulir pendaftaran beasiswa di bawah ini</p>

            <!-- Alert Notification (jika ada pesan dari proses pendaftaran) -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-edit me-2"></i>Registrasi Beasiswa
                </div>
                <div class="card-body">
                    <form action="proses_daftar" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Masukkan Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                           placeholder="Nama lengkap" required>
                                    <div id="nama_error" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Masukkan Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="email@example.com" required>
                                    <div id="email_error" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_hp">Nomor HP</label>
                                    <input type="text" class="form-control" id="nomor_hp" name="nomor_hp"
                                           placeholder="08xxxxxxxxxx" required>
                                    <div id="nomor_hp_error" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester">Semester saat ini</label>
                                    <select class="form-select" id="semester" name="semester" required>
                                        <option value="">Pilih</option>
                                        <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <div id="semester_error" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ipk">IPK terakhir</label>
                                    <input type="text" class="form-control ipk-display" id="ipk" name="ipk"
                                           value="<?php echo number_format(IPK_MAHASISWA, 2); ?>" readonly>
                                    <?php if (!$ipk_memenuhi): ?>
                                    <div class="alert alert-danger mt-3 mb-0">
                                        <strong>MAAF!</strong> IPK Anda tidak memenuhi syarat minimal (3.0) untuk mendaftar beasiswa.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pilihan_beasiswa">Pilihan Beasiswa</label>
                                    <select class="form-select" id="pilihan_beasiswa" name="pilihan_beasiswa"
                                            <?php echo !$ipk_memenuhi ? 'disabled' : 'required'; ?>>
                                        <option value="">Pilih</option>
                                        <?php
                                        if ($result && mysqli_num_rows($result) > 0) {
                                            mysqli_data_seek($result, 0);
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo '<option value="' . htmlspecialchars($row['nama_beasiswa']) . '">';
                                                echo htmlspecialchars($row['nama_beasiswa']);
                                                echo '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div id="pilihan_beasiswa_error" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="berkas">Upload Berkas Syarat</label>
                                    <input type="file" class="form-control" id="berkas" name="berkas"
                                           accept=".pdf,.jpg,.jpeg,.zip"
                                           <?php echo !$ipk_memenuhi ? 'disabled' : 'required'; ?>>
                                    <small class="text-muted">PDF, JPG, ZIP (Max 2MB)</small>
                                    <div id="berkas_error" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-2">
                                <button type="submit" class="btn btn-primary w-100"
                                        <?php echo !$ipk_memenuhi ? 'disabled' : ''; ?>>
                                    Daftar
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="reset" class="btn btn-secondary w-100">
                                    Batal
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Catatan Penting</h6>
                <ul class="mb-0 small">
                    <li>Pastikan semua data yang diisi sudah benar</li>
                    <li>IPK minimal untuk mendaftar adalah <strong>3.0</strong></li>
                    <li>File yang diupload maksimal <strong>2 MB</strong></li>
                    <li>Setelah mendaftar, status ajuan adalah <strong>"belum diverifikasi"</strong></li>
                </ul>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2024 Sistem Pendaftaran Beasiswa</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            <?php if ($ipk_memenuhi): ?>
            const pilihanBeasiswa = document.getElementById('pilihan_beasiswa');
            if (pilihanBeasiswa && !pilihanBeasiswa.disabled) {
                pilihanBeasiswa.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(function() {
                    pilihanBeasiswa.focus();
                }, 500);
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>

