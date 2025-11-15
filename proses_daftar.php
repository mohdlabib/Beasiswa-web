<?php
/**
 * File: proses_daftar.php
 * Deskripsi: Proses backend untuk menyimpan data pendaftaran beasiswa
 * Fungsi: Validasi server-side dan insert data ke database
 *
 * Fitur:
 * - Validasi semua input (nama, email, nomor HP, semester, IPK)
 * - Validasi format email dan nomor HP
 * - Validasi file upload (tipe dan ukuran)
 * - Upload file ke folder uploads/
 * - Insert data ke tabel pendaftaran_beasiswa
 * - Set status_ajuan = "belum diverifikasi" secara otomatis
 * - Redirect ke halaman daftar dengan status success/error
 */

// Include file koneksi database
require_once 'config/connection.php';

/**
 * Konstanta IPK Mahasiswa
 * Harus sama dengan nilai di daftar.php
 */
define('IPK_MAHASISWA', 3.4);

// Cek apakah request method adalah POST
// Jika bukan POST, redirect ke halaman daftar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: daftar');
    exit();
}

// Array untuk menampung error validasi
$errors = array();

/**
 * Ambil dan bersihkan data dari form
 * Menggunakan fungsi clean_input() untuk mencegah XSS
 */
$nama = isset($_POST['nama']) ? clean_input($_POST['nama']) : '';
$email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
$nomor_hp = isset($_POST['nomor_hp']) ? clean_input($_POST['nomor_hp']) : '';
$semester = isset($_POST['semester']) ? clean_input($_POST['semester']) : '';
$ipk = IPK_MAHASISWA; // IPK dari konstanta, bukan dari input user
$pilihan_beasiswa = isset($_POST['pilihan_beasiswa']) ? clean_input($_POST['pilihan_beasiswa']) : '';

/**
 * VALIDASI DATA INPUT
 * Semua validasi dilakukan di server-side untuk keamanan
 */

// Validasi Nama
if (empty($nama)) {
    $errors[] = "Nama harus diisi";
}

// Validasi Email (format dan keberadaan)
if (empty($email)) {
    $errors[] = "Email harus diisi";
} elseif (!validate_email($email)) {
    $errors[] = "Format email tidak valid";
}

// Validasi Nomor HP (hanya angka, panjang 10-15 digit)
if (empty($nomor_hp)) {
    $errors[] = "Nomor HP harus diisi";
} elseif (!validate_phone($nomor_hp)) {
    $errors[] = "Nomor HP hanya boleh berisi angka";
} elseif (strlen($nomor_hp) < 10 || strlen($nomor_hp) > 15) {
    $errors[] = "Nomor HP harus 10-15 digit";
}

// Validasi Semester (hanya 1-8 untuk S1)
if (empty($semester)) {
    $errors[] = "Semester harus dipilih";
} elseif (!is_numeric($semester) || $semester < 1 || $semester > 8) {
    $errors[] = "Semester tidak valid";
}

// Validasi IPK (minimal 3.0)
if ($ipk < 3.0) {
    $errors[] = "IPK tidak memenuhi syarat minimal (3.0)";
}

// Validasi Pilihan Beasiswa
if (empty($pilihan_beasiswa)) {
    $errors[] = "Pilihan beasiswa harus dipilih";
}

/**
 * VALIDASI DAN UPLOAD FILE
 * File yang diperbolehkan: PDF, JPG, JPEG, ZIP
 * Ukuran maksimal: 2MB
 */

// Tentukan folder upload dan inisialisasi nama berkas
$upload_dir = 'uploads/';
$berkas_name = '';

// Buat folder uploads jika belum ada
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Cek apakah file berhasil diupload
if (isset($_FILES['berkas']) && $_FILES['berkas']['error'] === UPLOAD_ERR_OK) {
    // Ambil informasi file
    $file_tmp = $_FILES['berkas']['tmp_name'];
    $file_name = $_FILES['berkas']['name'];
    $file_size = $_FILES['berkas']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validasi ekstensi file (hanya PDF, JPG, JPEG, ZIP)
    $allowed_ext = array('pdf', 'jpg', 'jpeg', 'zip');
    if (!in_array($file_ext, $allowed_ext)) {
        $errors[] = "Format file tidak valid. Hanya PDF, JPG, dan ZIP yang diperbolehkan";
    }

    // Validasi ukuran file (maksimal 2MB)
    $max_size = 2 * 1024 * 1024; // 2MB dalam bytes
    if ($file_size > $max_size) {
        $errors[] = "Ukuran file terlalu besar. Maksimal 2MB";
    }

    // Jika tidak ada error, lakukan upload
    if (empty($errors)) {
        // Generate nama file unik dengan timestamp untuk menghindari duplikasi
        $berkas_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
        $upload_path = $upload_dir . $berkas_name;

        // Upload file ke folder uploads
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            $errors[] = "Gagal mengupload file";
        }
    }
} else {
    // Jika file tidak diupload
    $errors[] = "File berkas harus diupload";
}

/**
 * CEK ERROR VALIDASI
 * Jika ada error, redirect kembali ke form dengan pesan error
 */
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: daftar?status=error');
    exit();
}

/**
 * INSERT DATA KE DATABASE
 * Status ajuan otomatis diset "belum diverifikasi"
 */

// Set status ajuan default
$status_ajuan = 'belum diverifikasi';

// Query INSERT ke tabel pendaftaran_beasiswa
$query = "INSERT INTO pendaftaran_beasiswa
          (nama, email, nomor_hp, semester, ipk, pilihan_beasiswa, berkas, status_ajuan)
          VALUES
          ('$nama', '$email', '$nomor_hp', '$semester', '$ipk', '$pilihan_beasiswa', '$berkas_name', '$status_ajuan')";

// Eksekusi query
$result = mysqli_query($conn, $query);

/**
 * CEK HASIL INSERT
 * Jika berhasil: redirect dengan status=success
 * Jika gagal: hapus file yang sudah diupload dan redirect dengan status=error
 */
if ($result) {
    // Berhasil insert data
    header('Location: daftar?status=success');
} else {
    // Gagal insert data, hapus file yang sudah diupload
    if (!empty($berkas_name) && file_exists($upload_dir . $berkas_name)) {
        unlink($upload_dir . $berkas_name);
    }

    header('Location: daftar?status=error');
}

// Tutup koneksi database
mysqli_close($conn);
exit();
?>

