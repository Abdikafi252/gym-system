<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
include 'dbcon.php';
$page = 'ai-assistant';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>AI Gym Assistant | M*A GYM</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .chat-container { height: 400px; overflow-y: auto; padding: 20px; background: #f9f9f9; border-bottom: 2px solid #ddd; }
        .chat-message { margin-bottom: 15px; padding: 10px 15px; border-radius: 15px; max-width: 80%; }
        .bot { background: #3b82f6; color: white; align-self: flex-start; }
        .user { background: #e2e8f0; color: #1e293b; align-self: flex-end; margin-left: auto; }
        .chat-input { display: flex; padding: 10px; background: white; }
        .chat-input input { flex-grow: 1; border: 1px solid #ddd; padding: 10px; border-radius: 5px 0 0 5px; }
        .chat-input button { border: none; background: #3b82f6; color: white; padding: 0 20px; border-radius: 0 5px 5px 0; }
    </style>
</head>
<body>

<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">AI Assistant</a> </div>
        <h1>Somali AI Gym Assistant</h1>
    </div>

    <div class="container-fluid">
        <hr>
        <div class="row-fluid">
            <div class="span8 offset2">
                <div class="widget-box">
                    <div class="widget-title"> <span class="icon"> <i class="fas fa-robot"></i> </span>
                        <h5>AI Assistant (Somali & English)</h5>
                    </div>
                    <div class="chat-container d-flex flex-column" id="chatBox">
                        <div class="chat-message bot">
                            Asc! Waxaan ahay AI Assistant-ka M*A GYM. Maxaan kaa caawiyaa maanta? (Jimicsi, Cunto, Miisaan dhimista...)
                        </div>
                    </div>
                    <div class="chat-input">
                        <input type="text" id="userInput" placeholder="Halkan wax ku qor..." onkeypress="if(event.keyCode==13) sendMessage()">
                        <button onclick="sendMessage()">DIR <i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="../js/jquery.min.js"></script>
<script src="../js/matrix.js"></script>

<script>
function sendMessage() {
    const input = document.getElementById('userInput');
    const message = input.value.trim();
    if (!message) return;

    appendMessage('user', message);
    input.value = '';

    fetch('../api/ai-chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(res => res.json())
    .then(data => {
        appendMessage('bot', data.response);
    });
}

function appendMessage(sender, text) {
    const chatBox = document.getElementById('chatBox');
    const div = document.createElement('div');
    div.className = 'chat-message ' + sender;
    div.innerText = text;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>

</body>
</html>
