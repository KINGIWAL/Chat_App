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
                <div style="
                    text-align:center;
                    background:linear-gradient(145deg,#1e3c72,#2a5298);
                    padding:25px;
                    border-radius:20px;
                    box-shadow:0 10px 25px rgba(0,0,0,0.3);
                    color:#fff;
                    margin:20px;
                    font-family:'Segoe UI',sans-serif;
                    transition:all 0.3s ease;
                ">
                    <h2 style="margin-bottom:20px;font-size:24px;font-weight:600;">My Profile</h2>
                    <div style="position:relative;display:inline-block;">
                        <img src="${foto}" alt="Foto Profil"
                            width="130" height="130"
                            style="border-radius:50%;
                            object-fit:cover;
                            border:5px solid #ffffff;
                            box-shadow:0 0 20px rgba(0,255,255,0.6);
                            transition:transform 0.3s ease;">
                    </div>
                    <h3 style="margin:15px 0 5px;font-size:20px;font-weight:600;">${data.username}</h3>
                    <p style="margin:0;font-size:14px;color:#cce7ff;">No Hp: ${data.nomor_user}</p>
                    <button onclick="openEditProfile('${data.username}','${data.nomor_user}','${foto}')"
                        style="margin-top:20px;padding:10px 25px;border:none;border-radius:12px;
                        background:linear-gradient(135deg,#00f2fe,#4facfe);
                        color:#fff;font-weight:bold;cursor:pointer;
                        box-shadow:0 6px 15px rgba(0,0,0,0.3);
                        transition:transform 0.3s ease;">
                        Edit Profile
                    </button>
                </div>
                `;


                document.getElementById('profileContent').innerHTML = html;
            }
            document.getElementById('profileOverlay').classList.add('active');
        });
}
function openEditProfile(username, nomor_user, foto) {
    const editHtml = `
<div style="
    background:linear-gradient(145deg,#1e3c72,#2a5298);
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.3);
    color:#fff;
    margin:20px;
    font-family:'Segoe UI',sans-serif;
    text-align:center;
">
    <h3 style="margin-bottom:20px;font-size:22px;font-weight:600;">Edit Profile</h3>
    <form id="editProfileForm" enctype="multipart/form-data" 
          style="display:flex;flex-direction:column;align-items:center;gap:8px;width:100%;">

        <!-- Gambar tetap rata tengah -->
        <img src="${foto}" alt="Foto Profil"
            width="120" height="120"
            style="align-self:center;border-radius:50%;
            object-fit:cover;margin-top:10px;
            border:5px solid #ffffff;
            box-shadow:0 0 20px rgba(0,255,255,0.6);">

        <!-- Label & input rata kiri -->
        <label style="font-size:14px;font-weight:500;align-self:flex-start;">Username</label>
        <input type="text" name="username" value="${username}" required
            style="width:100%;border:none;border-radius:10px;
            outline:none;font-size:14px;text-align:left;
            box-shadow:0 0 10px rgba(255,255,255,0.3);">

        <label style="font-size:14px;font-weight:500;align-self:flex-start;">Foto Profil</label>
        <input type="file" name="foto" 
">


        <button type="submit"
            style="padding:10px;border:none;border-radius:12px;
            background:linear-gradient(135deg,#00f2fe,#4facfe);
            color:#fff;font-weight:bold;cursor:pointer;
            box-shadow:0 6px 15px rgba(0,0,0,0.3);
            transition:transform 0.3s ease;">
            Simpan Perubahan
        </button>
    </form>
</div>

`;

    document.getElementById('profileContent').innerHTML = editHtml;
    document.getElementById('profileOverlay').classList.add('active');

    // Tambahkan event listener untuk submit form
    document.getElementById('editProfileForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('api/edit_profile.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                // bisa reload profil setelah edit
                openProfile();
            })
            .catch(err => alert("Terjadi kesalahan koneksi!"));
    });
}




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
