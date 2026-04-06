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
    <link rel="stylesheet" href="css/Index.css">
    <title>Chat App</title>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar kontak -->
<div class="contacts">
    <div class="contacts-header">Contacts</div><!-- title kontak  -->
    
    <div class="contact-list" id="contactList">
        <!-- Kontak akan dimuat dari getContacts.php -->
    </div>
</div>


        <!-- Chat area -->
        <div class="chat-container">
            <div class="chat-header" id="chatHeader">💬 My Chat App</div>
            <div class="dropdown">
            <button onclick="toggleDropdown()">Menu</button><!-- pemicu dropdown  --> 
            
            <div class="dropdown-content">
                <a href="#" onclick="openPopup()">Tambah Kontak</a>   <!-- memunculkan inputkan kontak  -->   
            </div>
        </div>

    <!-- Dropdown -->
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

    <div class="chat-messages" id="chatMessages"><!-- tempat menampilkan pesan  --></div>

            <div class="chat-input">
                <input type="text" id="chatInput" name="pesan" placeholder="Type a message..."> <!-- tempat menginputkan pesan  -->
                
                <button id="sendBtn">Send</button><!-- btn untuk menginput pesan  -->
            </div>
        </div>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatHeader = document.getElementById('chatHeader');
        const contacts = document.querySelectorAll('.contact');
        const currentUser = Number(<?= json_encode($_SESSION['id_user'] ?? 0) ?>);
        let currentContact = null;
        </script>
    
<script src="js/Index.js"></script>
<script src="js/Dropdown.js"></script>
</body>

</html>