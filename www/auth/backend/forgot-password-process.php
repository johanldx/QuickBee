<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

$error = '';

writeLog('/auth/backend/forgot-password-process.php', "Processus de mot de passe oublié", getUserIP());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['forgot-password'])) {
        $email = $_POST['email'];

        if (empty($email)) {
            $error = 'Veuillez remplir tous les champs.';
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode($error), true, 301);
            exit;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "L'email n'est pas au bon format.";
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode($error), true, 301);
            exit;
        } else {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT email, password, first_name, last_name FROM user WHERE email = :email";
            $params = [':email' => $email];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->close();
    
            if ($result && count($result) == 1) {
                $action_key = bin2hex(random_bytes(25));

                $db = new Database();
                $conn = $db->connect();
                $sql = "UPDATE user SET action_key = :action_key WHERE email = :email";
                $params = [
                    'action_key'=>$action_key,
                    ':email'=>$email
                ];
                $stmt = $db->query($sql, $params);
    
                $mailer = new Mailer();
                
                $toName = $result[0]['first_name'].' '.$result[0]['last_name'];
                $to = $email;
                $subject = "Réinitialiser votre mot de passe";
                $body = '<h2 style="color: #CEAAFF;">Réinitialisation de votre mot de passe</h2><p>Bonjour '.$toName.', vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p><p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p><a href="'.getenv("URL_PATH").'/auth/forgot-password.php?email='.urlencode($email).'&token='.urlencode($action_key).'" style="display: block; width: 200px; background-color: #CEAAFF; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; text-decoration: none; margin: 20px auto;">Réinitialiser le mot de passe</a>';
                $altBody = "Réinitialisation de votre mot de passe\n\nVous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.\n\nCliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :\n\n".getenv("URL_PATH").'/auth/forgot-password.php?email='.urlencode($email).'&token='.urlencode($action_key);

                $success = $mailer->send($to, $toName, $subject, $body, $altBody);
                
                $success_message = "Un e-mail vous a été envoyé.";
                header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?success='.urlencode($success_message), true, 301);
            } else {
                $error = "Le compte n'existe pas.";
                header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode($error), true, 301);
                exit;
            }
        }

    } else if (isset($_POST['change-password'])) {
        $password = $_POST['password']; 
        $retype_password = $_POST['retype_password'];

        if (!isset($_SESSION['change_password'])) {
            $error = 'Vous ne pouvez pas changer de mot de passe.';
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.$error, true, 301);
            exit;
        }
        else if (empty($password) || empty($retype_password)) {
            $error = 'Veuillez remplir tous les champs.';
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.$error.'&email='.urlencode($_SESSION['change_password']['email']).'&token='.urlencode($_SESSION['change_password']['token']), true, 301);
            exit;
        } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,}$/', $password)) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères, dont une lettre minuscule, une lettre majuscule, et un caractère spécial.';
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.$error.'&email='.urlencode($_SESSION['change_password']['email']).'&token='.urlencode($_SESSION['change_password']['token']), true, 301);
            exit;
        } else if ($password != $retype_password) {
            $error = 'Les mots de passe ne sont pas les mêmes.';
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.$error.'&email='.urlencode($_SESSION['change_password']['email']).'&token='.urlencode($_SESSION['change_password']['token']), true, 301);
            exit;
        } else {
            $db = new Database();
            $conn = $db->connect();
            $sql = "UPDATE user SET password = :password, action_key = :action_key WHERE email = :email";
            $params = [
                'email'=>$_SESSION['change_password']['email'],
                'action_key'=>null,
                ':password'=>password_hash($password, PASSWORD_DEFAULT)
            ];
            $stmt = $db->query($sql, $params);
                
            unset($_SESSION['change_password']);

            $success_message = "Votre mot de passe a été changé avec succès.";
            header('Location: ' . getenv('URL_PATH') . '/auth/login.php?success='.urlencode($success_message), true, 301);
            exit;
        }
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}