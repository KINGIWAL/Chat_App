const form = document.getElementById('registerForm');
const username = document.getElementById('username');
const password = document.getElementById('password');
const userError = document.getElementById('userError');
const passError = document.getElementById('passError');
const result = document.getElementById('result');

form.addEventListener('submit', (e) => {
    e.preventDefault();
    let valid = true;
    userError.textContent = '';
    passError.textContent = '';
    result.textContent = '';

    if (username.value.trim() === '') {
        userError.textContent = 'Username tidak boleh kosong';
        valid = false;
    }
    if (password.value.length < 6) {
        passError.textContent = 'Password minimal 6 karakter';
        valid = false;
    }

    if (valid) {
        // Kirim data ke PHP dengan AJAX
        fetch('api/Register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `username=${encodeURIComponent(username.value)}&password=${encodeURIComponent(password.value)}`
        })
            .then(response => response.text())
            .then(data => {
                result.textContent = data; // tampilkan pesan dari PHP
                result.className = data.includes("berhasil") ? 'success' : 'error';
            })
            .catch(err => {
                result.textContent = "Terjadi kesalahan koneksi!";
                result.className = 'error';
            });
    }
});

