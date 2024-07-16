<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

writeLog('/admin/backend/delete-account-process.php', "Sppression d'un compte.", getUserIP(), $_SESSION['logged']);


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = trim($_GET['id']);

    if (empty($id)) {
        $error = "Impossible de trouver le compte.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, first_name, last_name, email FROM user WHERE id = :id";
        $params = [
            ':id' => $id
        ];
        $stmt = $db->query($sql, $params);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($user && count($user) == 1) {
            $conn = $db->connect();
            $sql = "UPDATE user SET first_name = 'Anonymous', last_name = 'User', email = CONCAT('anonymous-', id, '@rootage.fr'), newsletter = 0, organization = NULL, administrator = 0, rank = 'user', active = 0, action_key = NULL, password = :password WHERE id = :id";
            $params = [
                ':id' => $id,
                ':password' => bin2hex(random_bytes(25))
            ];
            $stmt = $db->query($sql, $params);
    
            $mailer = new Mailer();
                
            $toName = $user[0]['first_name'].' '.$user[0]['last_name'];
            $to = $user[0]['email'];
            $subject = "Votre compte a été supprimé.";
            $body = 'Bonjour '.$toName.',<br><br>Votre compte a été supprimé.<br><br>Au revoir, votre compte vient d\'être supprimé.<br><br>Cordialement,<br>L\'équipe Quickbee.<br><br>Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter notre support à quickbee@rootage.fr.';
            $altBody = "Bonjour ".$toName.",\n\nVotre compte a été supprimé.\n\nAu revoir, votre compte vient d'être supprimé.\n\nCordialement,\nL'équipe Quickbee.\n\nSi vous pensez qu'il s'agit d'une erreur, veuillez contacter notre support à quickbee@rootage.fr.";
    
            $success = $mailer->send($to, $toName, $subject, $body, $altBody);

            $success = "Le compte vient d'être supprimé.";
            header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?success='.urlencode($success), true, 301);
            exit;
        } else {
            $error = "Utilisateur introuvable.";
            header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode($error), true, 301);
            exit;
        }
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}