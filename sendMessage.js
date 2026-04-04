
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

chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendBtn.click();
});
