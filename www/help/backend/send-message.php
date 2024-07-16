<?php
session_start();

// Afficher toutes les erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['administrator'])) {
    $userIsAdmin = userIsAdmin();

    if ($userIsAdmin[0] == false) {
        header($userIsAdmin[1], true, 301);
        exit;
    }
} else {
    $allowedInApp = allowedInApp();

    if ($allowedInApp[0]) {
        $allowedInApp = $allowedInApp[1];
    } else {
        header($allowedInApp[1], true, 301);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $conversation_id = $data['conversation_id'] ?? null;
    $message_content = $data['message'] ?? null;

    if (empty($conversation_id) || empty($message_content)) {
        echo json_encode(['status' => 'error', 'message' => 'Conversation ID ou contenu du message manquant']);
        exit;
    }

    $db = new Database();
    $conn = $db->connect();
    
    // Vérifier si l'utilisateur est administrateur
    $sql = "SELECT administrator FROM user WHERE id = :id";
    $params = [
        ':id' => $_SESSION['logged']
    ];
    $stmt = $db->query($sql, $params);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['administrator']) {
        // Requête pour les administrateurs
        $sql = "SELECT id FROM conversation WHERE id = :id AND active = 1";
        $params = [
            ':id' => $conversation_id
        ];
    } else {
        // Requête pour les utilisateurs non administrateurs
        $sql = "SELECT id FROM conversation WHERE id = :id AND user = :user AND active = 1";
        $params = [
            ':id' => $conversation_id,
            ':user' => $_SESSION['logged']
        ];
    }

    $stmt = $db->query($sql, $params);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($conversation) {
        $sql = "INSERT INTO message (conversation, user, content) VALUES (:conversation, :user, :content)";
        $params = [
            ':conversation' => $conversation_id,
            ':user' => $_SESSION['logged'],
            ':content' => $message_content
        ];

        $stmt = $db->query($sql, $params);
        echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'La conversation est terminée ou n\'existe pas.']);
    }
    exit;
}
?>
