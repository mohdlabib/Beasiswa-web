/**
 * File: script.js
 * Deskripsi: JavaScript untuk validasi form pendaftaran beasiswa (client-side)
 * Fungsi: Validasi input sebelum form disubmit ke server
 *
 * Fitur:
 * - Validasi nama (tidak boleh kosong)
 * - Validasi email (format email yang benar)
 * - Validasi nomor HP (hanya angka, 10-15 digit)
 * - Validasi semester (harus dipilih)
 * - Validasi pilihan beasiswa (jika tidak disabled)
 * - Validasi file upload (format dan ukuran)
 * - Auto-remove karakter non-numeric pada input nomor HP
 * - Real-time validation saat blur (kehilangan focus)
 */

/**
 * Fungsi utama untuk validasi form
 * Dipanggil saat form di-submit (onsubmit event)
 * Return: true jika semua validasi lolos, false jika ada error
 */
function validateForm() {
    let isValid = true;

    // Validasi Nama
    const nama = document.getElementById('nama').value.trim();
    if (nama === '') {
        showError('nama', 'Nama harus diisi');
        isValid = false;
    } else {
        clearError('nama');
    }

    // Validasi Email (format email yang benar)
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regex untuk validasi email
    if (email === '') {
        showError('email', 'Email harus diisi');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('email', 'Format email tidak valid');
        isValid = false;
    } else {
        clearError('email');
    }

    // Validasi Nomor HP (hanya angka, panjang 10-15 digit)
    const nomorHp = document.getElementById('nomor_hp').value.trim();
    const phonePattern = /^[0-9]+$/; // Regex untuk validasi hanya angka
    if (nomorHp === '') {
        showError('nomor_hp', 'Nomor HP harus diisi');
        isValid = false;
    } else if (!phonePattern.test(nomorHp)) {
        showError('nomor_hp', 'Nomor HP hanya boleh berisi angka');
        isValid = false;
    } else if (nomorHp.length < 10 || nomorHp.length > 15) {
        showError('nomor_hp', 'Nomor HP harus 10-15 digit');
        isValid = false;
    } else {
        clearError('nomor_hp');
    }

    // Validasi Semester (harus dipilih)
    const semester = document.getElementById('semester').value;
    if (semester === '') {
        showError('semester', 'Semester harus dipilih');
        isValid = false;
    } else {
        clearError('semester');
    }

    // Validasi Pilihan Beasiswa (jika tidak disabled)
    const pilihanBeasiswa = document.getElementById('pilihan_beasiswa');
    if (!pilihanBeasiswa.disabled && pilihanBeasiswa.value === '') {
        showError('pilihan_beasiswa', 'Pilihan beasiswa harus dipilih');
        isValid = false;
    } else {
        clearError('pilihan_beasiswa');
    }

    // Validasi Upload Berkas (jika tidak disabled)
    const berkas = document.getElementById('berkas');
    if (!berkas.disabled && berkas.files.length === 0) {
        showError('berkas', 'Berkas syarat harus diupload');
        isValid = false;
    } else if (berkas.files.length > 0) {
        const file = berkas.files[0];
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'zip'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const maxSize = 2 * 1024 * 1024; // 2MB dalam bytes

        // Cek ekstensi file
        if (!allowedExtensions.includes(fileExtension)) {
            showError('berkas', 'Format file harus PDF, JPG, atau ZIP');
            isValid = false;
        }
        // Cek ukuran file
        else if (file.size > maxSize) {
            showError('berkas', 'Ukuran file maksimal 2MB');
            isValid = false;
        } else {
            clearError('berkas');
        }
    }

    return isValid;
}

/**
 * Fungsi untuk menampilkan pesan error
 * @param {string} fieldId - ID dari input field
 * @param {string} message - Pesan error yang akan ditampilkan
 */
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');

    // Tambahkan class 'is-invalid' untuk styling Bootstrap
    field.classList.add('is-invalid');

    // Tampilkan pesan error
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

/**
 * Fungsi untuk menghapus pesan error
 * @param {string} fieldId - ID dari input field
 */
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');

    // Hapus class 'is-invalid'
    field.classList.remove('is-invalid');

    // Sembunyikan pesan error
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

/**
 * Event Listener saat halaman selesai dimuat
 * Menambahkan real-time validation pada field email dan nomor HP
 */
document.addEventListener('DOMContentLoaded', function() {

    // Real-time validation untuk Email (saat blur/kehilangan focus)
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                showError('email', 'Format email tidak valid');
            } else {
                clearError('email');
            }
        });
    }

    // Real-time validation untuk Nomor HP
    const phoneField = document.getElementById('nomor_hp');
    if (phoneField) {
        // Auto-remove karakter non-numeric saat mengetik
        phoneField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Validasi panjang digit saat blur
        phoneField.addEventListener('blur', function() {
            if (this.value && (this.value.length < 10 || this.value.length > 15)) {
                showError('nomor_hp', 'Nomor HP harus 10-15 digit');
            } else {
                clearError('nomor_hp');
            }
        });
    }
});

