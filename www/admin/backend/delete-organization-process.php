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

writeLog('/admin/backend/delete-organization-process.php', "Sppression d'une entreprise.", getUserIP(), $_SESSION['logged']);


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = trim($_GET['id']);

    if (empty($id)) {
        $error = "Impossible de trouver l'entreprise.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT organization.id, (SELECT user.email FROM user WHERE user.id = organization.owner) AS email, (SELECT user.first_name FROM user WHERE user.id = organization.owner) AS first_name, (SELECT user.last_name FROM user WHERE user.id = organization.owner) AS last_name FROM organization WHERE organization.id = :id";
        $params = [
            ':id' => $id
        ];
        $stmt = $db->query($sql, $params);
        $organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($organization && count($organization) == 1) {
            $db = new Database();

            $conn = $db->connect();
            $sql = "DELETE FROM organization WHERE id = :id";
            $params = [
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);
    
            $mailer = new Mailer();
                
            $toName = $organization[0]['first_name'].' '.$organization[0]['last_name'];
            $to = $organization[0]['email'];
            $subject = "Votre compte a été supprimé.";
            $body = 'Bonjour '.$toName.',<br><br>Votre entreprise a été supprimé.<br><br>Au revoir, votre entreprise vient d\'être supprimé.<br><br>Cordialement,<br>L\'équipe Quickbee.<br><br>Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter notre support à quickbee@rootage.fr.';
            $altBody = "Bonjour ".$toName.",\n\nVotre entreprise a été supprimé.\n\nAu revoir, votre entreprise vient d'être supprimé.\n\nCordialement,\nL'équipe Quickbee.\n\nSi vous pensez qu'il s'agit d'une erreur, veuillez contacter notre support à quickbee@rootage.fr.";
    
            $success = $mailer->send($to, $toName, $subject, $body, $altBody);

            $success = "L'entreprise vient d'être supprimé.";
            header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?success='.urlencode($success), true, 301);
            exit;
        } else {
            $error = "Entreprise introuvable.";
            header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode($error), true, 301);
            exit;
        }
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}