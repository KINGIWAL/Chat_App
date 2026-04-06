// dropdown

function toggleDropdown() {
    document.querySelector('.dropdown').classList.toggle('show');
}

function openPopup() {
    document.getElementById('overlay').classList.add('active');
}

document.getElementById('overlay').addEventListener('click', function (e) {
    if (e.target.id === 'overlay') {
        this.classList.remove('active'); // klik luar popup menutup
    }
});

document.getElementById('contactForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const nama = document.getElementById('nama').value;
    const nomor = document.getElementById('nomor').value;
    loadContacts();
    fetch('/Chat_app/api/saveContact.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nama=${encodeURIComponent(nama)}&nomor=${encodeURIComponent(nomor)}`
    })
        .then(res => res.text())
        .then(data => {
            alert(data); // tampilkan pesan dari PHP
            document.getElementById('overlay').classList.remove('active');
            // Tambahkan kontak baru ke sidebar tanpa reload
            const contactList = document.getElementById('contactList');
            const newContact = document.createElement('div');
            newContact.classList.add('contact');
            newContact.dataset.id = nomor;
            newContact.textContent = nama;
            contactList.appendChild(newContact);
        })
        .catch(err => {
            console.error("Error simpan kontak:", err);
        });
});     
