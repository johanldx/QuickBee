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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $response = trim($_POST['response']);

    if (empty($question) || empty($response) || empty($_SESSION['edit-captcha'])) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-captcha.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE captcha SET question = :question, response = :response WHERE id = :id";
        $params = [
            ':question'=>$question,
            ':response'=>$response,
            ':id'=>$_SESSION['edit-captcha']
        ];
        $stmt = $db->query($sql, $params);

        unset($_SESSION['edit-captcha']);

        $success_message = "Votre captcha a bien été modifié.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/captchas.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-captcha.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}