<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

$error = '';

writeLog('/auth/backend/register-process.php', "Création de compte", getUserIP());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim(htmlspecialchars($_POST['first_name']));
    $last_name = trim(htmlspecialchars($_POST['last_name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password']; 
    $retype_password = $_POST['retype_password'];

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT email FROM user WHERE email = :email";
    $params = [':email' => $email];
    $stmt = $db->query($sql, $params);
    $users_with_this_email = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($retype_password)) {
        $error = 'Veuillez remplir tous les champs.';
        header('Location: ' . getenv('URL_PATH') . '/auth/register.php?error='.urlencode($error), true, 301);
        exit;
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email n'est pas au bon format.";
        header('Location: ' . getenv('URL_PATH') . '/auth/register.php?error='.urlencode($error), true, 301);
        exit;
    }
    else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,}$/', $password)) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères, dont une lettre minuscule, une lettre majuscule, et un caractère spécial.';
            header('Location: ' . getenv('URL_PATH') . '/auth/register.php?error='.urlencode($error), true, 301);
            exit;
    } else if ($users_with_this_email) {
        $error = 'Un compte existe déjà avec cette adresse e-mail.';
        header('Location: ' . getenv('URL_PATH') . '/auth/register.php?error='.urlencode($error), true, 301);
        exit;
    } else if ($password != $retype_password) {
        $error = 'Les mots de passe ne sont pas les mêmes.';
        header('Location: ' . getenv('URL_PATH') . '/auth/register.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $_SESSION['register'] = [
            'email'=>$email,
            'password'=>password_hash($password, PASSWORD_DEFAULT),
            'first_name'=>$first_name,
            'last_name'=>$last_name,
        ];

        header('Location: ' . getenv('URL_PATH') . '/auth/captcha.php', true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/register.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}
