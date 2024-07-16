<?php
session_start();
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (!isset($_SESSION['logged'])) {
    $error = "Vous n'êtes pas connecté.";
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
    exit;
}

writeLog('/auth/backend/logout-process.php', "Déconnexion", getUserIP(), $_SESSION['logged']);

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

$success_message = "Vous avez bien été déconnecté.";
header('Location: ' . getenv('URL_PATH') . '/auth/login.php?success='.urlencode($success_message), true, 301);
exit;