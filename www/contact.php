<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 1));
require_once ROOT_PATH . '/php/utils.php';

writeLog('/contact', "Visité la page de contact", getUserIP());

?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
    <title>QuickBee - Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap-reboot.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap-grid.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="/static/css/btn-custom.css">
</head>
<body>
    <?php displayMessage() ?>
    <header class="text-center mt-5">
        <img class="" src="static/img/logo-d.png" alt="Logo du site" width="160px">
    </header>
    <main class="text-center">
        <h1 class="fs-3 mt-5">Contactez nous</h1>
        <form class="mx-5" action="/backend/contact-process.php" method="post">
            <div class="form-floating my-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="Votre nom" required>
                <label for="name">Votre nom</label>
            </div>

            <div class="form-floating my-4">
                <input type="email" class="form-control" id="email" name="email" placeholder="Votre e-mail" required>
                <label for="email">Votre e-mail</label>
            </div>

            <div class="form-floating my-4">
                <textarea style="height: 100px" class="form-control" id="message" name="message" placeholder="Votre message" required></textarea>
                <label for="message">Votre message</label>
            </div>

            <div>
                <a class="btn btn-secondary" href="/">Retour</a>
                <input type="submit" class="btn btn-primary" value="Envoyer">
            </div>
        </form>
    </main>
    <script src="../static/js/theme.js"></script>
    <script src="../static/js/notify.js"></script>
</body>
</html>