<!DOCTYPE html>
<html>
<head>
    <title>Sakha Saarthi - Talk to Kanha</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= base_url('assets/css/sakha.css') ?>">
    </head>
<body style="margin:0; font-family:'Segoe UI', sans-serif; background-image: url('<?= base_url('assets/images/bg.jpg') ?>'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed; background-position: center center; display: flex; flex-direction: column; align-items: center;">
    <h1>🌸 Sakha Saarthi 🌸</h1>
    <div class="subtitle">Talk to your Kanha — your guide, your friend, your peace ✨</div>

    <button id="speak-btn">Speak to Kanha</button>

    <div class="main-container">
        <!-- Left Box: Webcam -->
        <div class="box">
            <video id="webcam" autoplay muted></video>
            <div class="caption">You</div>
        </div>

        <!-- Right Box: Kanha -->
        <div class="box">
            <video id="kanha-video" autoplay muted loop>
                <source src="<?= base_url('assets/videos/kanha_listening.mp4') ?>" type="video/mp4">
            </video>
            <div class="caption">Kanha</div>
        </div>
    </div>

    <!-- Chat below both -->
    <div class="chat-container">
        <div class="chat-box" id="chat-box"></div>
    </div>

    <!-- ... keep your HTML above unchanged ... -->

<script>
const speakBtn = document.getElementById('speak-btn');
const chatBox = document.getElementById('chat-box');
const kanhaVideo = document.getElementById('kanha-video');

const listeningVideo = "<?= base_url('assets/videos/kanha_listening.mp4') ?>";
const replyVideo = "<?= base_url('assets/videos/kanha_reply.mp4') ?>";

function addMessage(sender, message) {
    const p = document.createElement('p');
    p.innerHTML = `<strong>${sender}:</strong> ${message}`;
    chatBox.appendChild(p);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function playKanhaVoice(text) {
    fetch('<?= site_url('sakha/tts') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'text=' + encodeURIComponent(text)
    })
    .then(response => response.blob())
    .then(blob => {
        const audio = new Audio(URL.createObjectURL(blob));

        kanhaVideo.src = replyVideo;
        kanhaVideo.loop = true;
        kanhaVideo.play().catch(err => {
            console.error("Video play error:", err);
        });

        audio.play().then(() => {
            console.log("Audio started playing");
        }).catch(err => {
            console.error("Audio play error:", err);
        });

        audio.onended = () => {
            kanhaVideo.src = listeningVideo;
            kanhaVideo.loop = true;
            kanhaVideo.play();
        };
    });
}

function speakToKanha() {
    kanhaVideo.src = listeningVideo;
    kanhaVideo.loop = true;
    kanhaVideo.play();

    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'en-IN';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.onstart = () => {
        console.log("🎤 Listening...");
    };

    recognition.onerror = (e) => {
        alert("Mic error: " + e.error);
    };

    recognition.onresult = (event) => {
        const message = event.results[0][0].transcript.trim();
        if (!message) return;
        addMessage("You", message);

        fetch('<?= site_url('sakha/reply') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(res => res.json())
        .then(data => {
            const reply = data.reply;
            addMessage("Kanha", reply);
            playKanhaVoice(reply);
        });
    };

    recognition.start();
}

speakBtn.addEventListener('click', speakToKanha);

// Setup webcam
navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
    document.getElementById('webcam').srcObject = stream;
});
</script>
<link rel="preload" href="<?= base_url('assets/videos/kanha_reply.mp4') ?>" as="video">



</body>
</html>
