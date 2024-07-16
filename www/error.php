<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('ROOT_PATH', dirname(__DIR__, 1));
require_once ROOT_PATH . '/php/utils.php';

$errorCode = 'Erreur';
$errorMessage = "Erreur inconnue";

if (isset($_GET['code'])) {
    $errorCode = $_GET['code'];

    switch($errorCode) {
        case '400':
            $errorMessage = "La syntaxe de la requête est erronée";
            break;
        case '401':
            $errorMessage = "Une authentification est nécessaire pour accéder à la ressource";
            break;
        case '403':
            $errorMessage = "Le serveur refuse d'exécuter la requête demandé";
            break;
        case '404':
            $errorMessage = "Ressource non trouvée";
            break;
        case '500':
            $errorMessage = "Erreur interne du serveur";
            break;
        case '502':
            $errorMessage = "Le serveur a reçu une réponse invalide depuis le serveur distant";
            break;
        case '503':
            $errorMessage = "Service temporairement indisponible ou en maintenance";
            break;
        case '504':
            $errorMessage = "Temps d’attente d’une réponse d’un serveur à un serveur intermédiaire écoulé";
            break;
        case '505':
            $errorMessage = "Version HTTP non gérée par le serveur";
            break;
    }
}

writeLog('/error.php', "Visité la page d'erreur", getUserIP());

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur <?php echo($errorCode); ?></title>
    <meta name="description" content="Panel d'administration de QuickBee">
    <link rel="stylesheet" href="/static/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="/static/css/btn-custom.css"> 
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="d-flex justify-content-center mt-1 mb-5">
        <img class="m-2" src="/static/img/logo-d.png" alt="Logo" width="160px">
    </div>

    <section class="py-3 py-md-5 d-flex justify-content-center align-items-center">
        <div class="container">
        <div class="row">
            <div class="col-12">
            <div class="text-center">
                <h2 class="display-1 d-flex justify-content-center align-items-center gap-2 mb-4 fw-bold" style="color: #DEE2E6;">
                    <?php echo($errorCode); ?>
                </h2>
                <h3 class="h2 mb-2" style="color: #4d4adf;">Oups...</h3>
                <p class="mb-5"><?php echo($errorMessage); ?></p>
                <a href="<?php echo isset($_SESSION['administrator']) ? '/admin/' : "/app/" ?>" class="btn btn-primary">Retour à la page d'accueil</a>
            </div>
            </div>
        </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../../static/js/script.js"></script>
    <script src="../../static/js/notify.js"></script>
</body>
</html>