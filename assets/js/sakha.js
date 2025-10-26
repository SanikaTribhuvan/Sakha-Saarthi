const speakBtn = document.getElementById('speak-btn');
speakBtn.onclick = () => {
    let recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'en-IN';
    recognition.start();

    recognition.onresult = function(event) {
        const userMessage = event.results[0][0].transcript;
        addMessage("You: " + userMessage);

        fetch('sakha/reply', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'message=' + encodeURIComponent(userMessage)
        })
        .then(res => res.json())
        .then(data => {
            const reply = data.reply;
            addMessage("Kanha: " + reply);
            playKanhaVoice(reply);  // 🔥 NEW FUNCTION CALL
        });
    };
};

function playKanhaVoice(text) {
    fetch('sakha/tts', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'text=' + encodeURIComponent(text)
    })
    .then(res => res.blob())
    .then(blob => {
        const audioUrl = URL.createObjectURL(blob);
        const audio = new Audio(audioUrl);
        audio.play();
    })
    .catch(err => console.error('TTS error:', err));
}

function addMessage(msg) {
    const box = document.getElementById('chat-box');
    const p = document.createElement('p');
    p.textContent = msg;
    box.appendChild(p);
    box.scrollTop = box.scrollHeight;
}

// Webcam Init
const video = document.getElementById('webcam');
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    })
    .catch(err => {
        console.error("Webcam error:", err);
    });
