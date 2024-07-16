<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['logged'])) {
    if (isset($_SESSION['capcha_resolved'])) {
        header('Location: ' . getenv('URL_PATH') . '/app/', true, 301);
        exit;
    }
} else if (empty($_SESSION['register'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
    exit;
}

$db = new Database();
$conn = $db->connect();
$sql = "SELECT * FROM captcha ORDER BY RAND() LIMIT 1";
$stmt = $db->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$_SESSION['captcha'] = $result[0]['id'];

writeLog('/auth/captcha.php', "Visite de la page de preuve anti-bot", getUserIP());

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
    <title>Captcha - QuickBee</title>
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
            <a href=""><img class="mb-5" id="logo-img" src="../../static/img/logo-d.png" alt="Logo QuickBee" width="150px"></a>
            <p class="card-title fs-2 mb-5 fw-bold">Je ne suis pas un robot</p>
            
            <form action="/auth/backend/captcha-process.php" method="post" class="text-center mb-3">
                <p class="card-text"><?php echo($result[0]['question']); ?></p>
                <div class="form-floating mb-4">
                    <input type="text" class="form-control" id="response" name="response" placeholder="Réponse" required>
                    <label for="password">Réponse</label>
                </div>
    
                <button type="submit" class="btn btn-primary btn-lg mb-3 text-center">Suivant</button>
            </form>

            <a href="register.php" class="card-link">Toujours pas inscrit ? Inscrivez-vous</a>
            <br>
            <a href="forgot-password.php" class="card-link">Mot de passe oublié ?</a>
        </div>
    </div>
    <script src="../static/js/theme.js"></script>
    <script src="../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>