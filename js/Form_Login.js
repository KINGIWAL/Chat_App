const form = document.getElementById('loginForm');
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
    if (password.value.trim() === '') {
        passError.textContent = 'Password tidak boleh kosong';
        valid = false;
    }

    if (valid) {
        fetch('api/Login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `username=${encodeURIComponent(username.value)}&password=${encodeURIComponent(password.value)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    window.location.href = "Index.php"; // pindah manual di JS
                } else {
                    result.textContent = data.message;
                    result.className = 'error';
                }
            })
    }
});