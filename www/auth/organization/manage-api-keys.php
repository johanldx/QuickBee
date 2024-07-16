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
$sql = "SELECT id, token, name, created_at FROM apikey WHERE organization = :organization";
$params = [
    ':organization' => $adminOfOrganization
];
$stmt = $db->query($sql, $params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

writeLog('/auth/organization/manage-api-keys.php', "Consultation des clés API.", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Mes clés API - QuickBee</title>
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
            <p class="card-title fs-2 mb-5 fw-bold">Mes clés API</p>

            <div class="text-end mb-3">
                <?php
                $perms = has_permission();
                if ($perms['new_api_key']) { 
                ?>
                <a href="/auth/organization/new-api-key.php" class="btn btn-primary">Nouvelle clé API</a>
                <?php 
                } else {
                ?>
                <a href="/auth/organization/new-api-key.php" class="btn btn-primary disabled">Vous ne pouvez pas créer de nouvelle clé API</a>
                <?php
                }
                ?>
            </div>

            <div class="bg-body-tertiary rounded p-3">
                    <table class="table mt-3 rounded">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Token</th>
                                <th>Création</th>
                                <th></th>
                            </tr>
                        </thead>
                        <body>
                            <?php
                            if ($result && count($result) >= 1) {
                                foreach ($result as $row) {
                            ?>   
                            <tr>
                                <td><?php echo(strlen($row['name']) > 50 ? substr($row['name'], 0, 50) . "..." : $row['name']); ?></th>
                                <td><span class="masked-text"><?php echo(substr($row['token'], 0, 5) . str_repeat('*', strlen($row['token']) - 6) . substr($row['token'], -5)); ?></span><span class="actual-text" style="display: none;"><?php echo($row['token']); ?></span><button class="btn btn-secondary btn-sm mx-2 toggle-visibility" data-index="0"><i class="bi bi-eye-fill"></i></i></button></td>
                                <td><?php $date = new DateTime($row['created_at']); echo($date->format('d/m/Y à H:i')); ?></td>
                                <td><a href="/auth/organization/backend/delete-api-key-process.php?id=<?php echo($row['id']); ?>" class="btn btn-danger">Supprimer</a></th>
                            </tr>

                            <?php
                                }
                            } else {
                                ?> 
                                <p>Vous n'avez pas de clé API.</p>
                                <?php
                                }
                                ?>
                        </body>
                    </table>
            </div>
            <a href="/auth/organization/" class="btn btn-secondary btn-lg mt-3 mb-3 text-center">Retour</a>
        </div>
    </div>
    <script src="../../static/js/theme.js"></script>
    <script src="../../static/js/notify.js"></script>
    <script src="../../static/js/mask-text.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>