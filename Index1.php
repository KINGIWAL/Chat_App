<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: Form_Login.html"); // kalau belum login, balik ke form
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1e1e2f;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .app-container {
            width: 700px;
            height: 600px;
            background: #2c2c3c;
            border-radius: 10px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        /* Sidebar kontak */
        .contacts {
            width: 200px;
            background: #1e1e2f;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #444;
        }

        .contacts-header {
            background: #3f51b5;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }

        .contact-list {
            flex: 1;
            overflow-y: auto;
        }

        .contact {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            transition: background 0.3s;
        }

        .contact:hover {
            background: #3f51b5;
        }

        /* Chat area */
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: #3f51b5;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        .chat-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }

        .message {
            margin: 8px 0;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
            animation: fadeIn 0.3s ease-in;
        }

        .message.sent {
            background: #4caf4f00;
            align-self: flex-end;
        }

        .message.received {
            background: #55555500;
            align-self: flex-start;
        }

        .chat-input {
            display: flex;
            padding: 10px;
            background: #1e1e2f;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            outline: none;
        }

        .chat-input button {
            margin-left: 10px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background: #3f51b5;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }

        .chat-input button:hover {
            background: #303f9f;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
         /* Dropdown */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown button {
        padding: 10px 15px;
        background: #3f51b5;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background: #2c2c3c;
        min-width: 160px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
        z-index: 1;
    }

    .dropdown-content a {
        color: #fff;
        padding: 10px;
        display: block;
        text-decoration: none;
    }

    .dropdown-content a:hover {
        background: #3f51b5;
    }

    .dropdown.show .dropdown-content {
        display: block;
    }

    /* Overlay popup */
    .overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        justify-content: center;
        align-items: center;
        z-index: 2;
    }

    .overlay.active {
        display: flex;
    }

    .popup {
        background: #2c2c3c;
        padding: 20px;
        border-radius: 10px;
        width: 300px;
        box-shadow: 0 0 15px rgba(0,0,0,0.7);
    }

    .popup h2 {
        margin-top: 0;
        text-align: center;
    }

    .popup input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: none;
        border-radius: 5px;
    }

    .popup button {
        width: 100%;
        padding: 10px;
        background: #3f51b5;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .popup button:hover {
        background: #303f9f;
    }

.message.sent { text-align: right; margin: 6px 10px; }
.message.sent .bubble { background: #316b05; display:inline-block; padding:8px; border-radius:8px; }
.message.received { text-align: left; margin: 6px 10px; }
.message.received .bubble { background: #192519; display:inline-block; padding:8px; border-radius:8px; }
.time { font-size:11px; color:gray; margin-top:4px; }


    
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar kontak -->
<div class="contacts">
    <div class="contacts-header">Contacts</div>
    <div class="contact-list" id="contactList">
        <!-- Kontak akan dimuat dari getContacts.php -->
    </div>
</div>


        <!-- Chat area -->
        <div class="chat-container">
            <div class="chat-header" id="chatHeader">💬 My Chat App</div>
            <div class="dropdown">
            <button onclick="toggleDropdown()">Menu</button>
            <div class="dropdown-content">
                <a href="#" onclick="openPopup()">Tambah Kontak</a>
            </div>
        </div>

    <!-- Overlay popup -->
    <div class="overlay" id="overlay">
        <div class="popup">
            <h2>Tambah Kontak</h2>
            <form id="contactForm">
                <input type="text" id="nama" name="nama" placeholder="Nama orang" required>
                <input type="text" id="nomor" name="nomor" placeholder="Nomor orang" required>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>

    <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input">
                <input type="text" id="chatInput" name="pesan" placeholder="Type a message...">
                <button id="sendBtn">Send</button>
            </div>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatHeader = document.getElementById('chatHeader');
        const contacts = document.querySelectorAll('.contact');
        let currentContact = null;
    

        // Kirim pesan ke server-----------------------------------------------Berhasil
        sendBtn.addEventListener('click', () => {
            const text = chatInput.value.trim();
            if (text !== '') {
                // Kirim ke sendMessage.php
                fetch('sendMessage.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id_Penerima=${encodeURIComponent(currentContact)}&Pesan=${encodeURIComponent(text)}`
                })
                    .then(response => response.text())
                    .then(data => {
                    if (data.startsWith("Error")) {
                        // alert("Pesan gagal dikirim: " + data);
                    } else {
                        // alert("Pesan berhasil dikirim: " + data);
                    }
                        chatInput.value = '';
                    })
                    .catch(err => {
                        console.error("Error kirim pesan:", err);
                    });
            }
        });
    
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendBtn.click();
        });




        // LOAD KONTAK ---------------------------------------Berhasil

        function loadContacts() {
            fetch('getContacts.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('contactList').innerHTML = data;

                    // Tambahkan event listener ke kontak yang baru dimuat
                    const contacts = document.querySelectorAll('.contact');
                    contacts.forEach(contact => {
                        contact.addEventListener('click', () => {
                            currentContact = contact.dataset.id; // nomor penerima
                            chatHeader.textContent = "Chat with " + contact.textContent;
                            chatMessages.innerHTML = "";
                            loadMessages(currentContact); // ambil pesan untuk kontak ini
                        });
                    });
                })
                .catch(err => {
                    console.error("Error ambil kontak:", err);
                });
        }

        // Panggil saat halaman pertama kali load
        loadContacts();




        // Load Messages--------------------------------------------berhasil 
        // Ambil pesan dari server
        // Buat koneksi WebSocket ke server
              // Ambil pesan lama dari server (PHP)
        function loadMessages() {
            fetch("getMessages.php", {
                method: "POST",
                body: new URLSearchParams({ id_Penerima: currentContact })
            })
            .then(response => response.text())
            .then(html => {
                // tampilkan hasil query (HTML dari PHP) ke dalam container chat
                chatMessages.innerHTML = html;
            })
            .catch(error => console.error("Error load messages:", error));
        }

        // Jalankan setiap 3 detik
        setInterval(loadMessages, 3000);
        
        
        
        // dropdown

        function toggleDropdown() {
        document.querySelector('.dropdown').classList.toggle('show');
        }

        function openPopup() {
            document.getElementById('overlay').classList.add('active');
        }

        document.getElementById('overlay').addEventListener('click', function(e) {
            if (e.target.id === 'overlay') {
                this.classList.remove('active'); // klik luar popup menutup
            }
        });

        document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const nama = document.getElementById('nama').value;
        const nomor = document.getElementById('nomor').value;

        fetch('saveContact.php', {
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



    </script>
</body>

</html>