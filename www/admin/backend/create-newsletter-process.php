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
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content) || empty($description)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-newsletter.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO newsletter (name, description, content, created_by) VALUES (:name, :description, :content, :created_by)";
        $params = [
            ':name'=>$title,
            ':description'=>$description,
            ':content'=>$content,
            ':created_by'=>$_SESSION['logged']
        ];
        $stmt = $db->query($sql, $params);

        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT first_name, last_name, email FROM user WHERE newsletter = :newsletter";
        $params = [':newsletter' => 1];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        foreach ($result as $row) {
            $mailer = new Mailer();
            $toName = $row['first_name'].' '.$row['last_name'];
            $to = $row['email'];
            $success = $mailer->send($to, $toName, $title, $content, $content);
        }

        $success_message = "Votre newsletter a bien été créé.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/newsletters.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/new-newsletter.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}