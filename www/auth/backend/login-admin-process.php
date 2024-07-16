<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

$error = '';

writeLog('/auth/backend/login-admin-process.php', "Connexion au panel administrateur", getUserIP());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password']; 

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/auth/login-admin.php?error='.urlencode($error), true, 301);
        exit;
    }
    else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, email, password, administrator, active FROM user WHERE email = :email";
        $params = [':email' => $email];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result && count($result) == 1) {
            $hashed_password = $result[0]['password'];

            if (password_verify($password, $hashed_password)) {

                if ($result[0]['administrator']) {
                    if ((bool)$result[0]['active']) {
                        $_SESSION['logged'] = $result[0]['id'];
                        $_SESSION['administrator'] = true;

                        $conn = $db->connect();
                        $sql = "UPDATE user SET last_login = NOW() WHERE id = :id";
                        $params = [':id' => $_SESSION['logged']];
                        $stmt = $db->query($sql, $params);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $db->close();

                        header('Location: ' . getenv('URL_PATH') . '/auth/captcha/', true, 301);
                        exit;
                    } else {
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
                        $to = $result[0]['email'];
                        $subject = "Confirmer votre email";
                        $body = '<h2 style="color: #CEAAFF;">Confirmer votre email</h2><p>Bonjour '.$toName.', merci de vous être inscrit(e) sur Quickbee. Avant de commencer, nous devons confirmer que c\'est bien vous.</p><p>Cliquez sur le bouton ci-dessous pour confirmer votre email :</p><a href="'.getenv("URL_PATH").'/auth/backend/validate-email-process.php?email='.urlencode($email).'&token='.urlencode($action_key).'" style="display: block; width: 200px; background-color: #CEAAFF; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; text-decoration: none; margin: 20px auto;">Confirmer votre email</a>';
                        $altBody = "Confirmer votre email\n\nMerci de vous être inscrit(e) sur Quickbee. Avant de commencer, nous devons confirmer que c\'est bien vous.\n\nCliquez sur le lien ci-dessous pour confirmer votre email :\n\n".getenv("URL_PATH").'/auth/backend/validate-email-process.php?email='.urlencode($email).'&token='.urlencode($action_key);
    
                        $success = $mailer->send($to, $toName, $subject, $body, $altBody);
    
                        $error = "Votre email n'est pas validé. Veuillez valider votre email.";
                        header('Location: ' . getenv('URL_PATH') . '/auth/login-admin.php?error='.urlencode($error), true, 301);
                        exit;
                    }
                } else {
                    $error = "Vous n'êtes pas administrateur.";
                    header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
                    exit;
                }
            } else {
                $error = "Le mot de passe n'est pas correct.";
                header('Location: ' . getenv('URL_PATH') . '/auth/login-admin.php?error='.urlencode($error), true, 301);
                exit;
            }
        } else {
            $error = "L'email n'est pas correct.";
            header('Location: ' . getenv('URL_PATH') . '/auth/login-admin.php?error='.urlencode($error), true, 301);
            exit;
        }
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/login-admin.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}