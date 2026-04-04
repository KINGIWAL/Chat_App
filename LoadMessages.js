
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
                    currentContact = Number(contact.dataset.id);
                    chatHeader.textContent = "Chat with " + contact.textContent;

                    chatMessages.innerHTML = "";
                    loadMessages(currentContact);
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
let socket;
function connectWebSocket() {
    // Buka koneksi WebSocket (sertakan id_user sebagai query param)
    socket = new WebSocket(`ws://192.168.1.3:8081?id_user=${currentUser}`);
socket.addEventListener("open", () => {
    console.log("Connected to WebSocket server");
});

    socket.addEventListener("message", (event) => {
        const data = JSON.parse(event.data);

        if (!currentContact) return;

        if (
            (data.id_pengirim == currentUser && data.id_penerima == currentContact) ||
            (data.id_pengirim == currentContact && data.id_penerima == currentUser)
        ) {
            const isSent = data.id_pengirim == currentUser;

            const bubble = document.createElement('div');
            bubble.className = `message ${isSent ? 'sent' : 'received'}`;
            bubble.innerHTML = `
            <div class="bubble">${data.text}</div>
            <div class="time">${data.time}</div>
        `;

            chatMessages.appendChild(bubble);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
socket.addEventListener("close", () => {
    console.log("Disconnected, reconnecting...");
    setTimeout(connectWebSocket, 2000); // reconnect TANPA reload
});
}
connectWebSocket();
