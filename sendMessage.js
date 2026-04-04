
// Kirim pesan ke server-----------------------------------------------Berhasil
sendBtn.addEventListener('click', () => {
    const text = chatInput.value.trim();

    if (text !== '' && currentContact) {
        socket.send(JSON.stringify({
            id_pengirim: currentUser,
            id_penerima: currentContact,
            text: text
        }));

        chatInput.value = '';
        // loadMessages();
    } else {
        console.warn("Socket belum terbuka, pesan tidak dikirim");
    }
});

chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendBtn.click();
});
