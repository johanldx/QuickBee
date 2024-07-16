<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

$userIsAdmin = userIsAdmin();

if ($userIsAdmin[0] == false) {
    header($userIsAdmin[1], true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = trim($_GET['id']);

    if (empty($id)) {
        $error = "Impossible de trouver le captcha.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/captchas.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id FROM captcha WHERE id = :id";
        $params = [
            ':id'=>$id
        ];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if ($result && count($result) == 1) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "DELETE FROM captcha WHERE id = :id";
            $params = [
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);
        }

        $success_message = "Votre captcha a bien été supprimé.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/captchas.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-captcha.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}