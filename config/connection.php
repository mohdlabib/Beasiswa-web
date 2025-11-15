<?php
/**
 * File: connection.php
 * Deskripsi: File konfigurasi koneksi database dan fungsi helper
 * Fungsi:
 * - Koneksi ke database MySQL
 * - Fungsi helper untuk validasi dan sanitasi input
 *
 * Fitur:
 * - Database connection dengan mysqli
 * - Fungsi clean_input() untuk mencegah SQL Injection dan XSS
 * - Fungsi validate_email() untuk validasi format email
 * - Fungsi validate_phone() untuk validasi format nomor HP
 */

/**
 * KONFIGURASI DATABASE
 * Sesuaikan dengan konfigurasi database Anda
 */
define('DB_HOST', 'localhost');     // Host database (biasanya localhost)
define('DB_USER', 'root');          // Username database
define('DB_PASS', '');              // Password database (kosong untuk default XAMPP/Laragon)
define('DB_NAME', 'beasiswaBnsp');  // Nama database

/**
 * KONEKSI KE DATABASE
 * Menggunakan mysqli untuk koneksi ke MySQL
 */
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8 untuk mendukung karakter Indonesia
mysqli_set_charset($conn, "utf8mb4");

/**
 * Fungsi untuk membersihkan input dari user
 * Mencegah SQL Injection dan XSS Attack
 *
 * @param string $data - Data input dari user
 * @return string - Data yang sudah dibersihkan
 */
function clean_input($data) {
    global $conn;
    $data = trim($data);                            // Hapus spasi di awal dan akhir
    $data = stripslashes($data);                    // Hapus backslashes
    $data = htmlspecialchars($data);                // Konversi karakter khusus ke HTML entities (XSS prevention)
    $data = mysqli_real_escape_string($conn, $data); // Escape karakter khusus SQL (SQL Injection prevention)
    return $data;
}

/**
 * Fungsi untuk validasi format email
 *
 * @param string $email - Email yang akan divalidasi
 * @return bool - true jika valid, false jika tidak valid
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Fungsi untuk validasi format nomor HP
 * Hanya menerima angka 0-9
 *
 * @param string $phone - Nomor HP yang akan divalidasi
 * @return bool - true jika valid (hanya angka), false jika tidak valid
 */
function validate_phone($phone) {
    return preg_match('/^[0-9]+$/', $phone);
}
?>

