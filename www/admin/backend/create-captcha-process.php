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

    if (empty($question) || empty($response)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-captcha.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO captcha (question, response, created_by) VALUES (:question, :response, :created_by)";
        $params = [
            ':question'=>$question,
            ':response'=>$response,
            ':created_by'=>$_SESSION['logged']
        ];
        $stmt = $db->query($sql, $params);

        $success_message = "Votre captcha a bien été créé.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/captchas.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-captcha.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}