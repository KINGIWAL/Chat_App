// LOAD KONTAK ---------------------------------------Berhasil
// loadContacts untuk menampilkan list nama kontak yang sudah terdaftar
function loadContacts() {
    fetch('/Chat_app/api/getContacts.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('contactList').innerHTML = data;//untuk menampilkna datanya di HMTL
            // Tambahkan event listener ke kontak yang baru dimuat
            const contacts = document.querySelectorAll('.contact');
            contacts.forEach(contact => {
                contact.addEventListener('click', () => {
                    currentContact = Number(contact.dataset.id);
                    // chatHeader.textContent = "Chat with " + contact.textContent;
                    
                    chatMessages.innerHTML = "";
                    loadMessages(currentContact);
                });
            });
        })
        .catch(err => {
            console.error("Error ambil kontak:", err);
        });
}

loadContacts(); //untuk menampilkan kontak kontak yang sudah terdata





// Load Messages--------------------------------------------berhasil 
// Ambil pesan dari server
// Ambil pesan lama dari server (PHP)
function loadMessages(currentContact) {
    fetch("/Chat_app/api/getMessages.php", {
        method: "POST",
        body: new URLSearchParams({ id_Penerima: currentContact })
    })
        .then(response => response.text())
        .then(html => {
            // tampilkan hasil query (HTML dari PHP) ke dalam container chat
            chatMessages.innerHTML = html;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        })
        .catch(error => console.error("Error load messages:", error));
}



let socket;
function connectWebSocket() {
    // Buka koneksi WebSocket (sertakan id_user sebagai query param)
    socket = new WebSocket(`ws://10.12.0.209:8081?id_user=${currentUser}`);
    //bagian ini otomatis aktif ketika pertama kali membuka websitenya
    socket.addEventListener("open", () => {
    console.log("Connected to WebSocket server");
});
    //bagian ini aktif ketika mengirim pesan
    socket.addEventListener("message", (event) => {
        const data = JSON.parse(event.data);
        if (!currentContact) return;

        if (
            (data.id_pengirim == currentUser && data.id_penerima == currentContact) ||
            (data.id_pengirim == currentContact && data.id_penerima == currentUser)
        ) {
            const isSent = data.id_pengirim == currentUser;

            const message = document.createElement('div');
            message.className = `message ${isSent ? 'sent' : 'received'}`;

            // tampilkan teks + waktu
            message.innerHTML = `
            <div class="message">${data.text}</div>
            <div class="time">${data.time}</div>
        `;

            chatMessages.appendChild(message);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });




//Otomatis terpanggil jika tertutup atau putus koneksi
socket.addEventListener("close", () => {
    console.log("Disconnected, reconnecting...");
    setTimeout(connectWebSocket, 2000); // reconnect TANPA reload
});
}
connectWebSocket();//pemicu





// Kirim pesan ke server-----------------------------------------------Berhasil
sendBtn.addEventListener('click', () => {
    const text = chatInput.value.trim();

    if (!currentContact) {
        alert("Pilih kontak dulu");
        return;
    }

    if (text === '') return;

    if (socket.readyState !== WebSocket.OPEN) {
        console.warn("Socket belum siap");
        return;
    }

    socket.send(JSON.stringify({
        id_pengirim: currentUser,
        id_penerima: currentContact,
        text: text
    }));

    chatInput.value = '';
});

// supaya bisa langsung enter 
chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendBtn.click();
});



// HAPUS KONTAK

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-contact')) {
        const id = e.target.getAttribute('data-id');
        if (confirm("Yakin ingin menghapus kontak ini?")) {
            fetch('/Chat_app/api/delete_contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                // e.target.parentElement.remove();
                e.target.closest('.contact').remove();

            });
        }
    }
});



// submit form edit
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new URLSearchParams(new FormData(this));

    fetch('/Chat_app/api/edit_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(msg => {
        // update tampilan nama kontak
        const id = document.getElementById('editId').value;
        const contact = document.querySelector(`.contact[data-id='${id}']`);
        contact.querySelector('.contact-name').textContent = document.getElementById('editNama').value;
        contact.dataset.nomor = document.getElementById('editNomor').value;                 
        closeEdit();
    });
});

function closeEdit() {
    document.getElementById('editOverlay').classList.remove('active');
}


// klik di luar popup untuk menutup
document.getElementById('editOverlay').addEventListener('click', function(e) {
    // jika klik langsung pada overlay (bukan isi popup)
    if (e.target === this) {
        closeEdit();
    }
});