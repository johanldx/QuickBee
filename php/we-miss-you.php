<?php
require_once 'utils.php';
require_once 'database.php';
require_once 'mail.php';
require_once 'env.php';
$config = require_once 'config.php';

$db = new Database();
$conn = $db->connect();
$sql = "SELECT id, first_name, last_name, last_login, email FROM user WHERE last_login < DATE_SUB(NOW(), INTERVAL :day DAY) AND active = 1";
$params = [
    ':day' => $config['days']
];
$stmt = $db->query($sql, $params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->close();

foreach ($users as $user) {
    $mailer = new Mailer();
    
    $toName = $user['first_name'].' '.$user['last_name'];
    $to = $user['email'];
    $subject = "Vous nous manquez !";
    $body = 'Bonjour '.$toName.',<br><br>Vous nous manquez !<br><br>Vous n\'avez pas utilisé votre compte Quickbee depuis un moment. Connectez-vous dès maintenant pour profiter de nos services.<br><br><a href="'.getenv("URL_PATH").'/auth/login.php">Se connecter</a><br><br>A bientôt,<br>L\'équipe Quickbee';
    $altBody = 'Bonjour '.$toName.','."\r\n\r\n".'Vous nous manquez !'."\r\n\r\n".'Vous n\'avez pas utilisé votre compte Quickbee depuis un moment. Connectez-vous dès maintenant pour profiter de nos services.'."\r\n\r\n".'Se connecter: '.getenv("URL_PATH").'/auth/login.php'."\r\n\r\n".'A bientôt,'."\r\n".'L\'équipe Quickbee';

    $success = $mailer->send($to, $toName, $subject, $body, $altBody);
}

writeLog('server', "Envoie des mails de relance.", 'SERVER');