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

    $page = 'admin';
} else {
    $allowedInApp = allowedInApp();

    if ($allowedInApp[0]) {
        $allowedInApp = $allowedInApp[1];
    } else {
        header($allowedInApp[1], true, 301);
        exit;
    }

    $page = 'index';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];

    if (empty($id)) {
        $error = "Impossible de fermer le ticket.";
        header('Location: ' . getenv('URL_PATH') . '/help/'.$page.'.php?error='.urlencode($error), true, 301);
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
            $sql = "UPDATE conversation SET active = 0 WHERE id = :id";
            $params = [
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);

            $conversation_id = $conn->lastInsertId();

            $success = "Le ticket a été fermé.";
            header('Location: ' . getenv('URL_PATH') . '/help/'.$page.'.php?success='.urlencode($success), true, 301);
            exit;
        } else {
            $conn = $db->connect();
            $sql = "UPDATE conversation SET active = 0 WHERE id = :id AND user = :user";
            $params = [
                ':user'=>$_SESSION['logged'],
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);

            $success = "Le ticket a été fermé.";
            header('Location: ' . getenv('URL_PATH') . '/help/'.$page.'.php?success='.urlencode($success), true, 301);
            exit;
        }
    }
}