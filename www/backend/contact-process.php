<?php
session_start();
 
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/mail.php';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . getenv('URL_PATH') . '/contact?error='.urlencode("Veuillez remplir tous les champs."), true, 301);
        exit;
    } else {
        $mailer = new Mailer();

        $toName = $name;
        $to = "quickbee@rootage.fr";
        $subject = "Nouveau message de ".$name;
        $body = 'Nom : '.$name.'<br>E-mail : '.$email.'<br>Message : <br>'.$message;
        $altBody = 'Nom : '.$name.'\nE-mail : '.$email.'\nMessage : \n'.$message;

        $success = $mailer->send($to, $toName, $subject, $body, $altBody);

        $mailer1 = new Mailer();

        $toName1 = $name;
        $to1 = $email;
        $subject1 = "Quickbee - Nos équipes ont bien reçu votre message !";
        $body1 = 'Bonjour '.$name.' !<br><br>Nos équipes ont bien reçu votre message, vous serez contacté d\'ici 48h.<br><br>Merci de votre confiance,<br><br>Johan de Quickbee';
        $altBody1 = 'Nom : '.$name.'\nE-mail : '.$email.'\nMessage : \n'.$message;

        $success1 = $mailer1->send($to1, $toName1, $subject1, $body1, $altBody1);

        header('Location: ' . getenv('URL_PATH') . '/contact?success='.urlencode("Merci. Vous recevrez une réponse sous 48h."), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/', true, 301);
    exit;
}