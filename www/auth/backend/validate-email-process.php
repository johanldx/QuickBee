<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/app/', true, 301);
    exit;
}

writeLog('/auth/backend/register-process.php', "Vérification du compte", getUserIP());

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['email']) && !empty($_GET['token'])) {
        $email = $_GET['email'];
        $token = $_GET['token'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($token) != 50) {
            $error = "Le liens de validation d'email est incorrect.";
            header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
            exit;
        } else {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT email, action_key FROM user WHERE email = :email";
            $params = [':email' => $email];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db->close();
    
            if ($result && count($result) == 1) {
                if ($result[0]['action_key'] == $token) {
                    $db = new Database();
                    $conn = $db->connect();
                    $sql = "UPDATE user SET active = :active, action_key = :action_key WHERE email = :email";
                    $params = [
                        ':active'=>1,
                        ':action_key'=>null,
                        ':email' => $email
                    ];
                    $stmt = $db->query($sql, $params);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $success_message = "Votre email a bien été validé.";
                    header('Location: ' . getenv('URL_PATH') . '/auth/login.php?success='.urlencode($success_message), true, 301);
                    exit;
                }
                else {
                    $error = "Le liens de validation d'email est incorrect.";
                    header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
                    exit;
                }
            }
            else {
                $error = "Le liens de changement de mot de passe est incorrect.";
                header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
                exit;
            }
        }
    } else {
        $error = "Le liens de validation d'email est incorrect.";
        header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
        exit;
    }
}