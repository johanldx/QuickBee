<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';

$allowedInApp = allowedInApp();

if (!$allowedInApp[0]) {
    header($allowedInApp[1], true, 301);
    exit;
}

$getMessages = false;
$activeConversation = true;

$db = new Database();
$conn = $db->connect();
$sql = "SELECT id, active FROM conversation WHERE user = :id";
$params = [
    ':id'=>$_SESSION['logged']
];
$stmt = $db->query($sql, $params);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;;

    if (!empty($id)) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, active, user FROM conversation WHERE id = :id";
        $params = [
            ':id'=>$id
        ];
        $stmt = $db->query($sql, $params);
        $selected_conversation = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($selected_conversation && $selected_conversation[0]['user'] = $_SESSION['logged']) {
            $getMessages = true;
            if (!$selected_conversation[0]['active']) {
                $activeConversation = false;
            }
        } else {
            $error = "Impossible de trouver le ticket.";
            header('Location: ' . getenv('URL_PATH') . '/help/index.php?error='.urlencode($error), true, 301);
            exit;
        }
    }
}

writeLog('/help/index.php', "Visité la page de support", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Support</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="../../static/css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/static/css/btn-custom.css"> 
    </head> 

    <style>
        main.main {
            width: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .input-group-container {
            
        }
        .input-group {
            background: white;
            bottom: 0;
        }
        .input-group-append {
            display: flex;
        }

        .btn-notrounded {
            border-radius: 0;
        }

        .btn-rounded {
            border-radius: 0 10px 10px 0;
        }

        .scrollable-div {
            max-height: 85vh;
            overflow-y: auto;
            margin-bottom: 5px;
        }

        .scrollable-nav {
            max-height: 75vh;
            overflow-y: auto;
            overflow-x: none;
        }
        
    </style>

    <body>
    <?php displayMessage() ?>
        <div class="d-flex">
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="sidebar-logo">
                        <img src="../../static/img/logo-d.png" alt="Logo" width="160px">
                    </div>
                </div>
                <ul class="sidebar-nav list-unstyled scrollable-nav">

                    <li class="sidebar-item">
                        <a href="/help/backend/start-conversation" class="sidebar-link text-decoration-none">
                            <i class="bi bi-plus-circle"></i>
                            <span>Nouveau ticket</span>
                        </a>
                    </li>

                    <?php foreach ($conversations as $conversation): ?>
                    <li class="sidebar-item">
                        <a href="index?id=<?php echo $conversation['id'] ?>" class="sidebar-link text-decoration-none <?php echo $conversation['id'] == $id ? 'item-selected' : '';?>">
                            <i class="bi bi-chat-left-text"></i>
                            <span>Ticket n°<?php echo $conversation['id'] ?></span> <?php echo $conversation['active'] ? '<span class="badge badge-success bg-success">Ouvert</span>' : '<span class="badge badge-danger bg-danger">Fermé</span>'; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="sidebar-footer">
                    <a href="/auth/account/account" target="_blank" class="sidebar-link text-decoration-none">
                        <img src="https://api.dicebear.com/7.x/miniavs/svg?seed=<?php echo($_SESSION['logged']); ?>" alt="avatar" width="15px"/>
                        <span style="padding-left: 15px;">Mon compte</span>
                    </a>
                    <a href="/app/" class="sidebar-link text-decoration-none">
                        <i class="bi bi-box-arrow-in-left"></i>
                        <span>Retour à l'application</span>
                    </a>
                </div>
            </aside>

            <main class="main p-3">
            <?php if ($getMessages): ?>
                <div class="messages p-3 scrollable-div" id="chat-box">
                    <p>Chargement des messages...</p>
                </div>
                <?php if ($activeConversation): ?>
                    <div class="input-group-container">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Envoyer un message" aria-label="Envoyer un message" aria-describedby="button-addon2" id="message-input">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-notrounded" type="button" id="send-button">Envoyer</button>
                                <a class="btn btn-danger btn-rounded" href="/help/backend/close-conversation?id=<?php echo $id; ?>">Fermer</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <a href="/help/backend/start-conversation.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nouveau ticket</a>
            <?php endif; ?>
            </main>

        </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
        <?php if ($getMessages): ?>

        <script>
        const conversationId = <?php echo $id; ?>;
        const messagesContainer = document.getElementById('chat-box');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');

        function padTo2Digits(num) {
            return num.toString().padStart(2, '0');
        }

        function formatDate(dateString) {
            const date = new Date(dateString.replace(' ', 'T'));

            const day = padTo2Digits(date.getDate());
            const month = padTo2Digits(date.getMonth() + 1);
            const year = date.getFullYear().toString().slice(2);
            const hours = padTo2Digits(date.getHours());
            const minutes = padTo2Digits(date.getMinutes());

            return `${day}/${month}/${year} à ${hours}:${minutes}`;
        }

        async function fetchMessages() {
            try {
                const response = await fetch(`/help/backend/get-messages.php?id=${conversationId}`);
                const data = await response.json();
                if (data.status === 'success') {
                    messagesContainer.innerHTML = '';
                    data.messages.forEach(message => {
                        const messageElement = document.createElement('div');
                        if (message.user == data.user) {
                            messageElement.classList.add('d-flex', 'justify-content-end');
                            messageElement.innerHTML = `<p class="rounded w-75 p-3 text-end" style="background-color: #E6B3FF;"><span class="fw-bold">${message.first_name} ${message.last_name} le ${formatDate(message.created_at)}</span><br>${message.content}</p>`;
                        } else {
                            messageElement.innerHTML = `<p class="rounded w-75 p-3" style="background-color: #e6e6e6;"> <span class="fw-bold">${message.first_name} ${message.last_name} le ${formatDate(message.created_at)}</span><br>${message.content}</p>`;
                        }
                        messagesContainer.appendChild(messageElement);
                    });
                } else if (data.status == 'not-found'){
                    messagesContainer.innerHTML = '<p>Pas encore de messages, lancer la conversation en envoyant un premier message.</p>';
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des messages:', error);
            }

        }

        async function sendMessage() {
            const message = messageInput.value;
            if (message.trim() !== '') {
                try {
                    const response = await fetch('/help/backend/send-message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ conversation_id: conversationId, message: message })
                    });
                    const data = await response.json();
                    if (data.status === 'success') {
                        messageInput.value = '';
                        fetchMessages();
                    } else {
                        console.error(data.message);
                    }
                } catch (error) {
                    console.error('Erreur lors de l\'envoi du message:', error);
                }
            }
        }

        <?php if ($activeConversation): ?>
        sendButton.addEventListener('click', sendMessage);

        messageInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });
        <?php endif; ?>

        setInterval(fetchMessages, 10000);
        fetchMessages();
        </script>
        <?php endif; ?>
</body>
</html>