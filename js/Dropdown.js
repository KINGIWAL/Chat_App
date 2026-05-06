// dropdown

function toggleDropdown() {
    document.querySelector('.dropdown-content').classList.toggle('show');
}

function openPopup() {
    document.getElementById('overlay').classList.add('active');
}

document.getElementById('overlay').addEventListener('click', function (e) {
    if (e.target.id === 'overlay') {
        this.classList.remove('active'); // klik luar popup menutup
    }
});

document.addEventListener('click', function (e) {
    const dropdown = document.querySelector('.dropdown-content');
    const button = document.querySelector('.dropdown button');

    // cek: kalau klik bukan di tombol dan bukan di menu
    if (!dropdown.contains(e.target) && !button.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});


// untuk menyimpan kontak baru 
document.getElementById('contactForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const nama = document.getElementById('nama').value;
    const nomor = document.getElementById('nomor').value;

    fetch('/Chat_app/api/saveContact.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nama=${encodeURIComponent(nama)}&nomor=${encodeURIComponent(nomor)}`
    })
        .then(res => res.text()) // kalau PHP kirim plain text
        .then(data => {
            alert(data);
            document.getElementById('overlay').classList.remove('active');
            loadContacts()
            // Tambahkan kontak baru ke sidebar
            const contactList = document.getElementById('contactList');
            const newContact = document.createElement('div');
            newContact.classList.add('contact');
            newContact.dataset.id = nomor; // sebaiknya id_user dari server
            newContact.textContent = nama;
            contactList.appendChild(newContact);

            // Kalau mau refresh daftar dari server, panggil loadContacts() di sini
            // loadContacts();
        })
        .catch(err => {
            console.error("Error simpan kontak:", err);
        });
});



// Untuk mengecek profile 
function openProfile() {
    fetch('/Chat_app/api/profile.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                document.getElementById('profileContent').innerHTML = "<p>" + data.error + "</p>";
            } else {
                // fallback kalau foto kosong
                let foto = data.foto_profile && data.foto_profile.trim() !== "" ? data.foto_profile : "uploads/default.jpg";

                let html = `
            <div style="text-align:center;
                background:linear-gradient(135deg,#4facfe,#00f2fe);
                padding:20px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0, 0, 0, 0.2);
                color:#fff;
                margin:10px">
            <img src="${foto}" alt="Foto Profil"
                width="120" height="120"
                style="border-radius:50%;
                object-fit:cover;
                margin-bottom:15px;
                border:4px solid #ffffff;
                box-shadow:0 10px 10px rgba(0,0,0,0.3);">
        <h2 style="margin:10px 0 5px 0;color: rgba(0, 0, 0, 0.64);">${data.username}</h2>
        <p style="margin:0;font-size:14px;color: rgb(0, 0, 0);">No Hp: ${data.nomor_user}</p>
    </div>
                `;
                document.getElementById('profileContent').innerHTML = html;
            }
            document.getElementById('profileOverlay').classList.add('active');
        });
}

// UNTUK KOLOM FOTO NANTI KALAU ADA 
// ${data.foto ? `<img src="${data.foto}" alt="Foto Profil" style="width:100px;height:100px;border-radius:50%;margin-bottom:10px;">` : ''}




// klik luar popup menutup
document.getElementById('profileOverlay').addEventListener('click', function (e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});


// toggle dropdown option kontak
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('options-btn')) {
        const dropdown = e.target.nextElementSibling;
        dropdown.classList.toggle('show');
    } else {
        // klik di luar → tutup semua dropdown
        document.querySelectorAll('.options-dropdown').forEach(dd => dd.classList.remove('show'));
    }
});


// -----------------------------------

// buka popup edit dengan data kontak
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('edit-contact')) {
        e.preventDefault();
        const id = e.target.getAttribute('data-id');
        const contact = e.target.closest('.contact');
        const nama = contact.querySelector('.contact-name').textContent;
        const nomor = contact.dataset.nomor;
        // isi form dengan data lama
        document.getElementById('editId').value = id;
        document.getElementById('editNama').value = nama;
        document.getElementById('editNomor').value = nomor;


        document.getElementById('editOverlay').classList.add('active');
    }
});
