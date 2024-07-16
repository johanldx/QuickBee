<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['capcha_resolved'])) {
    header('Location: ' . getenv('URL_PATH') . '/app/', true, 301);
    exit;
}

writeLog('/auth/backend/captcha-process.php', "Vérification du captcha", getUserIP());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_response = strtolower(trim(htmlspecialchars($_POST['response'])));

    if (empty($user_response) || !isset($_SESSION['captcha'])) {
        header('Location: ' . getenv('URL_PATH') . '/auth/captcha.php?error='.urlencode("Veuillez remplir tous les champs."), true, 301);
        exit;
    }
    else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT response FROM captcha WHERE id = :id";
        $params = [':id' => $_SESSION['captcha']];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $db->close();

        similar_text(strtolower(trim($result[0]['response'])), $user_response, $percent);
        if ($percent > 80) {

            if (isset($_SESSION['logged'])) {
                $_SESSION['capcha_resolved'] = true;
                
                if (isset($_SESSION['administrator'])) {
                    header('Location: ' . getenv('URL_PATH') . '/admin/', true, 301);
                    exit;
                }

                header('Location: ' . getenv('URL_PATH') . '/app/', true, 301);
                exit;
            } else {
                $action_key = bin2hex(random_bytes(25));

                $db = new Database();
                $conn = $db->connect();
                $sql = "INSERT INTO user (email, password, first_name, last_name, newsletter, administrator, rank, active, action_key) values (:email, :password, :first_name, :last_name, :newsletter, :administrator, :rank, :active, :action_key)";
                $params = [
                    ':email'=>$_SESSION['register']['email'],
                    ':password'=>$_SESSION['register']['password'],
                    ':first_name'=>$_SESSION['register']['first_name'],
                    ':last_name'=>$_SESSION['register']['last_name'],
                    ':newsletter'=>1,
                    ':administrator'=>0,
                    ':rank'=>'user',
                    ':active'=>0,
                    ':action_key'=>$action_key
                ];
                $stmt = $db->query($sql, $params);
    
                $db->close();
    
                $mailer = new Mailer();
    
                $toName = $_SESSION['register']['first_name'].' '.$_SESSION['register']['last_name'];
                $to = $_SESSION['register']['email'];
                $subject = "Confirmer votre email";
                $body = '<h2 style="color: #CEAAFF;">Confirmer votre email</h2><p>Bonjour '.$toName.', merci de vous être inscrit(e) sur Quickbee. Avant de commencer, nous devons confirmer que c\'est bien vous.</p><p>Cliquez sur le bouton ci-dessous pour confirmer votre email :</p><a href="'.getenv("URL_PATH").'/auth/backend/validate-email-process.php?email='.urlencode($_SESSION['register']['email']).'&token='.urlencode($action_key).'" style="display: block; width: 200px; background-color: #CEAAFF; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; text-decoration: none; margin: 20px auto;">Confirmer votre email</a>';
                $altBody = "Confirmer votre email\n\nMerci de vous être inscrit(e) sur Quickbee. Avant de commencer, nous devons confirmer que c\'est bien vous.\n\nCliquez sur le lien ci-dessous pour confirmer votre email :\n\n".getenv("URL_PATH").'/auth/backend/validate-email-process.php?email='.urlencode($email).'&token='.urlencode($action_key);
    
                $success = $mailer->send($to, $toName, $subject, $body, $altBody);
    
                unset($_SESSION['register']);
                unset($_SESSION['captcha']);
    
                $success_message = "Votre compe a bien été créé. Veuillez valider votre email.";
                header('Location: ' . getenv('URL_PATH') . '/auth/login.php?success='.urlencode($success_message), true, 301);
                exit;
            }
        }
        else {
            header('Location: ' . getenv('URL_PATH') . '/auth/captcha.php?error='.urlencode("La réponse est incorrect."), true, 301);
            exit;
        }
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/captcha.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}