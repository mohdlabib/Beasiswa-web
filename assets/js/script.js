function validateForm() {
    let isValid = true;

    const nama = document.getElementById('nama').value.trim();
    if (nama === '') {
        showError('nama', 'Nama harus diisi');
        isValid = false;
    } else {
        clearError('nama');
    }

    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        showError('email', 'Email harus diisi');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('email', 'Format email tidak valid');
        isValid = false;
    } else {
        clearError('email');
    }

    const nomorHp = document.getElementById('nomor_hp').value.trim();
    const phonePattern = /^[0-9]+$/;
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

    const semester = document.getElementById('semester').value;
    if (semester === '') {
        showError('semester', 'Semester harus dipilih');
        isValid = false;
    } else {
        clearError('semester');
    }

    const pilihanBeasiswa = document.getElementById('pilihan_beasiswa');
    if (!pilihanBeasiswa.disabled && pilihanBeasiswa.value === '') {
        showError('pilihan_beasiswa', 'Pilihan beasiswa harus dipilih');
        isValid = false;
    } else {
        clearError('pilihan_beasiswa');
    }

    const berkas = document.getElementById('berkas');
    if (!berkas.disabled && berkas.files.length === 0) {
        showError('berkas', 'Berkas syarat harus diupload');
        isValid = false;
    } else if (berkas.files.length > 0) {
        const file = berkas.files[0];
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'zip'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const maxSize = 2 * 1024 * 1024;

        if (!allowedExtensions.includes(fileExtension)) {
            showError('berkas', 'Format file harus PDF, JPG, atau ZIP');
            isValid = false;
        } else if (file.size > maxSize) {
            showError('berkas', 'Ukuran file maksimal 2MB');
            isValid = false;
        } else {
            clearError('berkas');
        }
    }

    return isValid;
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');

    field.classList.add('is-invalid');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + '_error');

    field.classList.remove('is-invalid');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
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

    const phoneField = document.getElementById('nomor_hp');
    if (phoneField) {
        phoneField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        phoneField.addEventListener('blur', function() {
            if (this.value && (this.value.length < 10 || this.value.length > 15)) {
                showError('nomor_hp', 'Nomor HP harus 10-15 digit');
            } else {
                clearError('nomor_hp');
            }
        });
    }
});

