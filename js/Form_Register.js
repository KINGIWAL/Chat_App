const form = document.getElementById('registerForm');
const username = document.getElementById('username');
const password = document.getElementById('password');
const userError = document.getElementById('userError');
const passError = document.getElementById('passError');
const result = document.getElementById('result');

console.log("Form:", form);
console.log("Username:", username.value);
console.log("Password:", password.value);

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
        const formData = new FormData(form); // ambil semua input termasuk file

        fetch('api/Register.php', {
            method: 'POST',
            body: formData // kirim langsung FormData
        })
            .then(response => response.text())
            .then(data => {
                result.textContent = data; // tampilkan pesan dari PHP
                result.className = data.includes("berhasil") ? 'success' : 'error';
                if (data.includes("berhasil")) {
                    window.location.href = "index.php";
                }
            })
            .catch(err => {
                result.textContent = "Terjadi kesalahan koneksi!";
                result.className = 'error';
            });
    }
});

