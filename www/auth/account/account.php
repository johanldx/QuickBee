<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (!isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
    exit;
}

$db = new Database();
$conn = $db->connect();
$sql = "SELECT email, newsletter, first_name, last_name, newsletter, rank, created_at, organization FROM user WHERE id = :id";
$params = [':id' => $_SESSION['logged']];
$stmt = $db->query($sql, $params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

$date = new DateTime($result[0]['created_at']);
$formattedDate = $date->format('d/m/Y à H:i');

if ($result[0]['organization']) {
    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT name, active FROM organization WHERE id = :id";
    $params = [':id' => $result[0]['organization']];
    $stmt = $db->query($sql, $params);
    $result_organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();
} else {
    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT name, stripe_product_id, active, price FROM plan";
    $params = [];
    $stmt = $db->query($sql, $params);
    $result_plan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();
}

writeLog('/auth/account/account.php', "Consultation du compte.", getUserIP(), $_SESSION['logged']);

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
    <title>Mon compte - QuickBee</title>
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
    <div class="container card text-center border-1 rounded-4 p-5" style="display: flex; margin: auto;">
        <div class="card-body p-5">
            <a href=""><img class="mb-5" id="logo-img" src="../../static/img/logo-d.png" alt="Logo QuickBee" width="150px"></a>
            <p class="card-title fs-2 mb-5 fw-bold">Mon compte</p>
            
            <div class="row justify-content-center">
                <div class="col-lg-7 mx-5 mb-3">
                    <?php
                    if (!isset($_SESSION['administrator'])) {
                    ?>
                        <?php
                            if ($result[0]['organization'] && $result[0]['rank'] == 'administrator') {
                        ?>
                            <a class="btn btn-primary w-100 text-center px-3" href="/auth/organization/organization.php">Gérer l'entreprise : <?php echo($result_organization[0]['name']); ?></a>
                        <?php
                            } else if ($result[0]['organization']) {
                        ?>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="organization" name="organization" placeholder="Mon entreprise" disabled>
                                <label for="organization"><?php echo($result_organization[0]['name']); ?></label>
                            </div>
                        <?php
                            } else {
        
                        ?>
                            <div class="bg-body-tertiary p-2 rounded">
                                <form action="/auth/organization/backend/create-checkout-session.php" method="post">
                                    <select class="form-select mb-2" name="priceId" id="priceId">
                                        <?php 
                                        foreach ($result_plan as $row) {
                                        ?>
                                        <option value="<?php echo($row['stripe_product_id']); ?>"><?php echo($row['name']); ?> (<?php echo($row['price']); ?> €)</option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary w-100 text-center px-3">Créer une entreprise</button>
                                </form>
                            </div>
                        <?php
                            }
                        ?>
                    <?php
                    }
                    ?>
                    </div>
            </div>

            <form action="/auth/account/backend/account-process.php" method="post" class="text-center mb-3 container">
                <div class="row justify-content-center">
                    <div class="col-lg-3 mx-5">
                        <?php
                            if ($result[0]['organization']) {
                        ?>
                        <?php
                        if (!isset($_SESSION['administrator'])) {
                        ?>
                        <div class="form-check form-switch text-start my-4">
                            <input type="checkbox" class="form-check-input" id="admin" name="admin" disabled <?php if ($result[0]['rank'] == 'administrator') { echo('checked');} ?>>
                            <label class="form-check-label" for="admin">Administrateur</label>
                        </div>
                        <?php } ?>
                        <?php
                            }
                        ?>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Prénom" value="<?php echo($result[0]['first_name']); ?>" required>
                            <label for="first_name">Prénom</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Nom" value="<?php echo($result[0]['last_name']); ?>" required>
                            <label for="last_name">Nom</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="<?php echo($result[0]['email']); ?>" required>
                            <label for="email">Adresse e-mail</label>
                        </div>
                        <div class="form-check form-switch mb-3 text-start my-4">
                            <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" <?php if ((bool)$result[0]['newsletter']) {echo('checked');} ?>>
                            <label class="form-check-label" for="newsletter">Abonné à la newsletter</label>
                        </div>
                        <p class="text-start my-4">Créé le <?php echo($formattedDate); ?></p>
                    </div>
                    <div class="col-lg-3 mx-5">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe">
                            <label for="password">Mot de passe</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="retype_password" name="retype_password" placeholder="retype_password">
                            <label for="floatingConfirmPassword">Nouveau m.d.p.</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg mt-3 mb-3 text-center">Modifier</button>
                <?php
                if (isset($_SESSION['administrator'])) {
                ?>
                <a href="/admin/" onclick="window.close();" class="btn btn-secondary btn-lg mt-3 mb-3 text-center">Retour</a>
                <?php
                } else {
                ?>
                <a href="/app/" onclick="window.close();" class="btn btn-secondary btn-lg mt-3 mb-3 text-center">Retour</a>
                <?php
                }
                ?>
                <a class="btn btn-danger btn-lg mt-3 mb-3 text-center" href="/auth/backend/logout-process.php">Me déconnecter</a>
                <p class="card-link">Vous pouvez ne modifier que les champs pertinents.</p>
            </form>
            <a class="btn btn-info btn-lg mt-3 mb-1 text-center text-white" href="/auth/account/personal-informations.php" target="_blank">Mes informations personnelles</a><br>
            <a class="btn btn-danger btn-lg mt-1 mb-3 text-center" href="/auth/account/backend/delete-account-process.php">Supprimer mon compte</a>

        </div>
    </div>
    <script src="../../static/js/theme.js"></script>
    <script src="../../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>