<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/app/', true, 301);
    exit;
}

$change_password = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['email']) && !empty($_GET['token'])) {
        $email = $_GET['email'];
        $token = $_GET['token'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($token) != 50) {
            $error = "Le liens de changement de mot de passe est incorrect.";
            header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode($error), true, 301);
            exit;
        } else {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT email, action_key FROM user WHERE email = :email";
            $params = [':email' => $email];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($result && count($result) == 1) {
                if ($result[0]['action_key'] == $token) {
                    $change_password = true;
                    $_SESSION['change_password'] = [
                        'email'=>$email,
                        'token'=>$token
                    ];
                }
                else {
                    $error = "Le liens de changement de mot de passe est incorrect.";
                    header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode($error), true, 301);
                    exit;
                }
            }
            else {
                $error = "Le liens de changement de mot de passe est incorrect.";
                header('Location: ' . getenv('URL_PATH') . '/auth/forgot-password.php?error='.urlencode($error), true, 301);
                exit;
            }
        }
    }
}

writeLog('/auth/forgot-password.php', "Visite de la page de mot de passe oublié", getUserIP());
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <?php
        if ($change_password) {
    ?>
    <title>Changer de mot de passe - QuickBee</title>
    <?php     
        } else {
    ?>
    <title>Mot de passe oublié - QuickBee</title>
    <?php
        }
    ?>
    <link rel="stylesheet" href="/static/css/btn-custom.css">
</head>
<style>
    body {
        width: 100%;
        height: 100vh;
        font-family: "Noto Sans", sans-serif;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #E6B3FF;
    }

    .card-link {
        font-size: 12px;
    }

</style>
<body>
    <?php displayMessage() ?>
    <div class="card text-center border-1 rounded-4" style="width: 400px; display: flex; margin: auto;">
        <div class="card-body p-5">
            <a href=""><img class="mb-5" id="logo-img" src="../static/img/logo-d.png" alt="Logo QuickBee" width="150px"></a>
            <?php
                if ($change_password) {
            ?>
            <p class="card-title fs-2 mb-5 fw-bold">Changer de mot de passe</p>

            <form action="backend/forgot-password-process.php" method="post" class="text-center mb-3">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nouveau mot de passe" required>
                    <label for="password">Nouveau mot de passe</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="retype_password" name="retype_password" placeholder="Retaper le nouveau mot de passe" required>
                    <label for="retype_password">Retaper le nouveau mot de passe</label>
                </div>

                <button type="submit" name="change-password" class="btn btn-primary btn-lg mb-3 text-center">Changer</button>
            </form>
            <?php     
                } else {
            ?>
            <p class="card-title fs-2 mb-5 fw-bold">Mot de passe oublié</p>

            <form action="backend/forgot-password-process.php" method="post" class="text-center mb-3">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required>
                    <label for="email">Adresse e-mail</label>
                </div>
    
                <button type="submit" name="forgot-password" class="btn btn-primary btn-lg mb-3 text-center">Envoyer</button>
            </form>
            <?php
                }
            ?>
            
            <a href="register.php" class="card-link">Toujours pas inscrit ? Inscrivez-vous</a>
            <br>
            <a href="login.php" class="card-link">Déjà inscrit ? Connectez-vous</a>
        </div>
    </div>
    <script src="../static/js/theme.js"></script>
    <script src="../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>