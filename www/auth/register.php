<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/app/', true, 301);
    exit;
}
else if (isset($_SESSION['register'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/captcha.php', true, 301);
    exit;
}

writeLog('/auth/register.php', "Visite de la page de création de compte", getUserIP());
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
    <link rel="stylesheet" href="/static/css/btn-custom.css">
    <title>Inscription - QuickBee</title>
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
            <p class="card-title fs-2 mb-5 fw-bold">M'inscrire</p>
            
            <form action="backend/register-process.php" method="post" class="text-center">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="first_name" placeholder="Prénom">
                    <label for="floatingInput">Prénom</label>
                </div>
    
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="last_name" placeholder="Nom">
                    <label for="floatingInput">Nom</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com">
                    <label for="floatingInput">Adresse e-mail</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
                    <label for="floatingPassword">Mot de passe</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="floatingConfirmPassword" name="retype_password" placeholder="ConfirmPassword">
                    <label for="floatingConfirmPassword">Confirmation de mot de passe</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg mb-3">Continuer</button>
            </form>

            <a href="login.php" class="card-link">Déjà inscrit ? Connectez-vous</a>
            <br>
            <a href="forgot-password.php" class="card-link">Mot de passe oublié ?</a>
        </div>
    </div>
    <script src="../static/js/theme.js"></script>
    <script src="../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>