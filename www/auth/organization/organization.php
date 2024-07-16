<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

$adminOfOrganization = adminOfOrganization();

if ($adminOfOrganization[0]) {
    $adminOfOrganization = $adminOfOrganization[1];
} else {
    header($adminOfOrganization[1], true, 301);
    exit;
}

$db = new Database();
$conn = $db->connect();
$sql = "SELECT name, email, phone, address, postal_code, city, country, iban, bic, siren, active, created_at FROM organization WHERE id = :id";
$params = [':id' => $adminOfOrganization];
$stmt = $db->query($sql, $params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

if (!$result[0]['active']) {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/backend/create-checkout-session.php');
    exit;
}

$date = new DateTime($result[0]['created_at']);
$formattedDate = $date->format('d/m/Y à H:i');

writeLog('/auth/organization/organization.php', "Consultation de l'entreprise.", getUserIP(), $_SESSION['logged']);

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
    <title>Mon entreprise - QuickBee</title>
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
            <p class="card-title fs-2 mb-5 fw-bold">Mon entreprise</p>
            
            <form action="/auth/organization/backend/organization-process.php" method="post" class="text-center mb-3 container">
                <div class="row justify-content-center">
                    <div class="col-lg-3 mx-5">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo($result[0]['name']); ?>" required>
                            <label for="name">Nom</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="<?php echo($result[0]['email']); ?>" required>
                            <label for="email">Adresse e-mail</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Numéro de téléphone" value="<?php echo($result[0]['phone']); ?>">
                            <label for="phone">Numéro de téléphone</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="siren" name="siren" placeholder="SIREN" value="<?php echo($result[0]['siren']); ?>">
                            <label for="siren">SIREN</label>
                        </div>
                        <p class="text-start my-4">Créé le <?php echo($formattedDate); ?></p>
                    </div>
                    <div class="col-lg-2 mx-5">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="iban" name="iban" placeholder="IBAN" value="<?php echo($result[0]['iban']); ?>">
                            <label for="iban">IBAN</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="bic" name="bic" placeholder="BIC" value="<?php echo($result[0]['bic']); ?>">
                            <label for="bic">BIC</label>
                        </div>
                    </div>
                    <div class="col-lg-3 mx-5">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Adresse" value="<?php echo($result[0]['address']); ?>" >
                            <label for="address">Adresse</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="postal_code" name="postal_code" placeholder="Code postal" value="<?php echo($result[0]['postal_code']); ?>">
                            <label for="postal_code">Code postal</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="city" name="city" placeholder="Ville" value="<?php echo($result[0]['city']); ?>">
                            <label for="city">Ville</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="country" name="country" placeholder="Pays" value="<?php echo($result[0]['country']); ?>">
                            <label for="country">Pays</label>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary btn-lg mt-3 mb-3 text-center">Modifier</button>
                    <a href="/auth/account/" class="btn btn-secondary btn-lg mt-3 mb-3 text-center">Retour</a>
                    <p class="card-link">Vous pouvez ne modifier que les champs pertinents.</p>
                </div>
            </form>
            <div>
                <a href="/auth/organization/manage-users.php" class="btn btn-warning btn-lg mt-3 mb-3 text-center">Gestion des membres</a>
                <a href="/auth/organization/manage-api-keys.php" class="btn btn-warning btn-lg mt-3 mb-3 text-center">Gestion des clés API</a>
                <?php 
                if ($result[0]['active']) {
                ?>
                <form action="/auth/organization/backend/customer-portal.php" method="post">
                    <button type="submit" class="btn btn-success btn-lg mt-3 mb-3 text-center">Gérer mon abonnement</button>
                </form>
                <?php
                } else {
                ?>
                <form action="/auth/organization/backend/create-checkout-session.php" method="post">
                    <button type="submit" class="btn btn-success btn-lg mt-3 mb-3 text-center">Gérer mon abonnement</button>
                </form>
                <?php
                }
                ?>
            </div>

        </div>
    </div>
    <script src="../../static/js/theme.js"></script>
    <script src="../../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>