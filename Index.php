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
    <link rel="stylesheet" href="css/index_style.css">
    <title>Chat App</title>

</head>

<body>
    <div class="app-container">
        <!-- Sidebar kontak -->
<div class="contacts">
    <div class="contacts-header">Kontak</div><!-- title kontak  -->
    <div class="contact-list" id="contactList">
        <!-- Kontak akan dimuat dari getContacts.php -->
    </div>
</div>



        <div class="chat-container">
            <div class="chat-header" id="chatHeader">💬 My Chat App</div>
            <div class="dropdown">
            <button onclick="toggleDropdown()"><img src="icons/menu.svg" width="20" alt="Menu"></button><!-- pemicu dropdown  --> 
            
            <!-- Dropdown -->
            <div class="dropdown-content">
                <a href="#" onclick="openPopup()"><img src="icons/Add person.svg" width="25" alt="Add Contact"></a>   
                <a href="#" onclick="openProfile()"><img src="icons/account.svg" width="25" alt="My Account"></a>
                <a href="Form_Login.HTML"><img src="icons/logout.svg" width="25" alt="logout"></a>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages"><!-- tempat menampilkan pesan  --></div>
        
        <div class="chat-input">
            <input type="text" id="chatInput" name="pesan" placeholder="Type a message..."> <!-- tempat menginputkan pesan  -->
            <button id="sendBtn"><img src="icons/send.svg" width="25" alt="Send"></button><!-- btn untuk menginput pesan  -->
        </div>
        <!-- Popup Profile -->
        <div class="overlay" id="profileOverlay">
            <div class="popup">
                <h2>My Profile</h2>
                <div id="profileContent">
                    <!-- Data user akan dimuat via JS -->
                </div>
            </div>
        </div>
        <!-- Form Add Contacts  -->
        <div class="overlay" id="overlay">
            <div class="popup">
                <h2 class="hAddKontak">Tambah Kontak</h2>
                <form id="contactForm">
                    <input type="text" id="nama" name="nama" placeholder="Nama Kontak ..." required>
                    <input type="text" id="nomor" name="nomor" placeholder="Nomor Kontak ..." required>
                    <button type="submit">Simpan</button>
                </form>
            </div>
        </div>     
        <!-- Form Edit Kontak -->
        <div class="overlay" id="editOverlay">
            <div class="popup">
                <h2>Edit Kontak</h2>
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    <input type="text" id="editNama" name="nama" placeholder="Nama Kontak ..." required>
                    <input type="text" id="editNomor" name="nomor" placeholder="Nomor Kontak ..." required>
                    <button type="submit">Simpan Perubahan</button>
                </form>
            </div>
        </div>
        </div>

        <script>
            const chatMessages = document.getElementById('chatMessages');
            const chatInput = document.getElementById('chatInput');
            const sendBtn = document.getElementById('sendBtn');
            const overlay = document.getElementById('overlay');
            const chatHeader = document.getElementById('chatHeader');
            const contacts = document.querySelectorAll('.contact');
            const currentUser = Number(<?= json_encode($_SESSION['id_user'] ?? 0) ?>);
            let currentContact = null;
            </script>
    
    <script src="js/Index_frontend.js"></script>
    <script src="js/Dropdown.js"></script>
</body>

</html>

<!-- 
#Langkah selanjutnya verifikasi

#fitur mengedit myprofile   -->