<?php
require_once 'config/connection.php';

define('IPK_MAHASISWA', 3.4);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: daftar');
    exit();
}

$errors = array();

$nama = isset($_POST['nama']) ? clean_input($_POST['nama']) : '';
$email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
$nomor_hp = isset($_POST['nomor_hp']) ? clean_input($_POST['nomor_hp']) : '';
$semester = isset($_POST['semester']) ? clean_input($_POST['semester']) : '';
$ipk = IPK_MAHASISWA;
$pilihan_beasiswa = isset($_POST['pilihan_beasiswa']) ? clean_input($_POST['pilihan_beasiswa']) : '';

if (empty($nama)) {
    $errors[] = "Nama harus diisi";
}

if (empty($email)) {
    $errors[] = "Email harus diisi";
} elseif (!validate_email($email)) {
    $errors[] = "Format email tidak valid";
}

if (empty($nomor_hp)) {
    $errors[] = "Nomor HP harus diisi";
} elseif (!validate_phone($nomor_hp)) {
    $errors[] = "Nomor HP hanya boleh berisi angka";
} elseif (strlen($nomor_hp) < 10 || strlen($nomor_hp) > 15) {
    $errors[] = "Nomor HP harus 10-15 digit";
}

if (empty($semester)) {
    $errors[] = "Semester harus dipilih";
} elseif (!is_numeric($semester) || $semester < 1 || $semester > 8) {
    $errors[] = "Semester tidak valid";
}

if ($ipk < 3.0) {
    $errors[] = "IPK tidak memenuhi syarat minimal (3.0)";
}

if (empty($pilihan_beasiswa)) {
    $errors[] = "Pilihan beasiswa harus dipilih";
}

$upload_dir = 'uploads/';
$berkas_name = '';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (isset($_FILES['berkas']) && $_FILES['berkas']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['berkas']['tmp_name'];
    $file_name = $_FILES['berkas']['name'];
    $file_size = $_FILES['berkas']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_ext = array('pdf', 'jpg', 'jpeg', 'zip');
    if (!in_array($file_ext, $allowed_ext)) {
        $errors[] = "Format file tidak valid. Hanya PDF, JPG, dan ZIP yang diperbolehkan";
    }

    $max_size = 2 * 1024 * 1024;
    if ($file_size > $max_size) {
        $errors[] = "Ukuran file terlalu besar. Maksimal 2MB";
    }

    if (empty($errors)) {
        $berkas_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
        $upload_path = $upload_dir . $berkas_name;

        if (!move_uploaded_file($file_tmp, $upload_path)) {
            $errors[] = "Gagal mengupload file";
        }
    }
} else {
    $errors[] = "File berkas harus diupload";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: daftar?status=error');
    exit();
}

$status_ajuan = 'belum diverifikasi';

$query = "INSERT INTO pendaftaran_beasiswa
          (nama, email, nomor_hp, semester, ipk, pilihan_beasiswa, berkas, status_ajuan)
          VALUES
          ('$nama', '$email', '$nomor_hp', '$semester', '$ipk', '$pilihan_beasiswa', '$berkas_name', '$status_ajuan')";

$result = mysqli_query($conn, $query);

if ($result) {
    header('Location: daftar?status=success');
} else {
    if (!empty($berkas_name) && file_exists($upload_dir . $berkas_name)) {
        unlink($upload_dir . $berkas_name);
    }

    header('Location: daftar?status=error');
}

mysqli_close($conn);
exit();
?>

