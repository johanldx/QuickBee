<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

if (!isset($_SESSION['logged'])) {
    $error = "Vous n'êtes pas connecté.";
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
    exit;
}

writeLog('/auth/account/backend/account-process.php', "Modification du compte.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim(htmlspecialchars($_POST['first_name']));
    $last_name = trim(htmlspecialchars($_POST['last_name']));
    $email = trim(htmlspecialchars($_POST['email']));
    if (isset($_POST['newsletter'])) {
        $newsletter = 1;
    } else {
        $newsletter = 0;
    }
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'Veuillez remplir tous les champs.';
        header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.$error, true, 301);
        exit;
    } else if (mb_strlen($password) > 0 xor mb_strlen($retype_password) > 0) {
        $error = 'Vous devez entrer votre nouveau mot de passe 2 fois.';
        header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.$error, true, 301);
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email n'est pas au bon format.";
        header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.$error, true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id FROM user WHERE email = :email";
        $params = [':email' => $email];
        $stmt = $db->query($sql, $params);
        $users_with_this_email = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users_with_this_email && count($users_with_this_email) == 1) {
            if ($users_with_this_email[0]['id'] != $_SESSION['logged']) {
                $error = 'Un compte existe déjà avec cette adresse e-mail.';
                header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode($error), true, 301);
                exit;
            }
        }

        if (mb_strlen($password) != 0 && mb_strlen($retype_password) != 0) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,}$/', $password)) {
                $error = 'Le mot de passe doit contenir au moins 8 caractères, dont une lettre minuscule, une lettre majuscule, et un caractère spécial.';
                header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode($error), true, 301);
                exit;
            } else if ($password != $retype_password) {
                $error = 'Les mots de passe ne sont pas les mêmes.';
                header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode($error), true, 301);
                exit;
            } else {
                $db = new Database();
                $conn = $db->connect();
                $sql = "UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name, newsletter = :newsletter, password = :password WHERE id = :id";
                $params = [
                    ':email'=>$email,
                    ':password'=>password_hash($password, PASSWORD_DEFAULT),
                    ':first_name'=>$first_name,
                    ':last_name'=>$last_name,
                    ':newsletter'=>$newsletter,
                    ':id'=>$_SESSION['logged']
                ];
                $stmt = $db->query($sql, $params);

                header('Location: ' . getenv('URL_PATH') . '/auth/backend/logout-process.php', true, 301);
                exit;
            }
        } else {
            $db = new Database();
            $conn = $db->connect();
            $sql = "UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name, newsletter = :newsletter WHERE id = :id";
            $params = [
                ':email'=>$email,
                ':first_name'=>$first_name,
                ':last_name'=>$last_name,
                ':newsletter'=>$newsletter,
                ':id'=>$_SESSION['logged']
            ];
            $stmt = $db->query($sql, $params);

            $success_message = "Votre compte a été mis à jour.";
            header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?success='.urlencode($success_message), true, 301);
            exit;
        }
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}