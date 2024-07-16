<?php
session_start();
 
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

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Conversation ID manquant']);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT administrator FROM user WHERE id = :id";
        $params = [
            ':id'=>$_SESSION['logged']
        ];
        $stmt = $db->query($sql, $params);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($user[0]['administrator']) {
            $conn = $db->connect();
            $sql = "SELECT message.id, message.user, user.first_name, user.last_name, message.content, message.created_at FROM message JOIN user ON message.user = user.id WHERE message.conversation = :id ORDER BY message.created_at ASC";
            $params = [
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($messages) {
                echo json_encode(['status' => 'success', 'messages' => $messages, 'user' => $_SESSION['logged']]);
            } else {
                echo json_encode(['status' => 'not-found', 'message' => 'Pas encore de messages ou conversation introuvable.']);
            }
            exit;
        } else {
            $conn = $db->connect();
            $sql = "SELECT message.id, message.user, user.first_name, user.last_name, message.content, message.created_at FROM message JOIN user ON message.user = user.id JOIN conversation ON message.conversation = conversation.id WHERE message.conversation = :id AND conversation.user = :user ORDER BY message.created_at ASC";
            $params = [
                ':user'=>$_SESSION['logged'],
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($messages) {
                echo json_encode(['status' => 'success', 'messages' => $messages, 'user' => $_SESSION['logged']]);
            } else {
                echo json_encode(['status' => 'not-found', 'message' => 'Pas encore de messages ou conversation introuvable.']);
            }
            exit;
        }
    }
}