
// LOAD KONTAK ---------------------------------------Berhasil
// loadContacts untuk menampilkan list nama kontak yang sudah terdaftar
function loadContacts() {
    fetch('getContacts.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('contactList').innerHTML = data;//untuk menampilkna datanya di HMTL
            // Tambahkan event listener ke kontak yang baru dimuat
            const contacts = document.querySelectorAll('.contact');
            contacts.forEach(contact => {
                contact.addEventListener('click', () => {
                    currentContact = contact.dataset.id; // id_penerima pesan  berupa int
                    chatHeader.textContent = "Chat with " + contact.textContent;//menampilkan dibagian header supaya dinamis
                    loadMessages(currentContact); // ambil pesan untuk kontak ini
                });
            });
        })
        .catch(err => {
            console.error("Error ambil kontak:", err);
        });
}
// pemicu functionnya 
loadContacts();





// Load Messages--------------------------------------------berhasil 
// Ambil pesan dari server
// Buat koneksi WebSocket ke server
// Ambil pesan lama dari server (PHP)
function loadMessages(currentContact) {
    fetch("getMessages.php", {
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

function connectWebSocket() {

socket.addEventListener("open", () => {
    console.log("Connected to WebSocket server");
});

socket.addEventListener("message", (event) => {
    const data = JSON.parse(event.data);
    const isSent = data.id_pengirim === currentUser;
    const bubble = document.createElement('div');
    bubble.className = `message ${isSent ? 'sent' : 'received'}`;
    bubble.innerHTML = `<div class="bubble">${data.text}</div><div class="time">${data.time}</div>`;
    if (
        data.id_pengirim == currentContact ||
        data.id_penerima == currentContact
    ) {
        chatMessages.appendChild(bubble);
    }
});
socket.addEventListener("close", () => {
    console.log("Disconnected, reconnecting...");
    setTimeout(connectWebSocket, 2000); // reconnect TANPA reload
});
}
connectWebSocket();
