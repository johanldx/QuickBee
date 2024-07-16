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
$sql = "SELECT id, first_name, last_name, email, rank, last_login 
        FROM user 
        WHERE organization = :organization
        AND id <> (SELECT owner FROM organization WHERE id = :organization)
        ";
$params = [
    ':organization' => $adminOfOrganization
];
$stmt = $db->query($sql, $params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

writeLog('/auth/organization/manage-users.php', "Consultation des administrateurs.", getUserIP(), $_SESSION['logged']);

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
    <title>Mes membres - QuickBee</title>
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
            <p class="card-title fs-2 mb-5 fw-bold">Mes membres</p>
            
            <div class="text-end mb-3">
                <?php
                $perms = has_permission();
                if ($perms['new_user']) {
                ?>
                <a href="/auth/organization/new-user.php" class="btn btn-primary">Nouveau membre</a>
                <?php 
                } else {
                ?>
                <a href="/auth/organization/new-user.php" class="btn btn-primary disabled">Vous ne pouvez pas ajouter de nouveau membre</a>
                <?php
                }
                ?>
            </div>
            
            <div class="bg-body-tertiary rounded p-3">
                    <table class="table mt-3 rounded">
                        <thead>
                            <tr>
                                <th>Membre</th>
                                <th>E-mail</th>
                                <th>Rôle</th>
                                <th>Dernière connexion</th>
                                <th></th>
                            </tr>
                        </thead>
                        <body>
                            <?php
                            if ($result && count($result) >= 1) {
                                foreach ($result as $row) {
                            ?>   
                            <tr>
                                <td><?php echo(strlen($row['first_name'].' '.$row['last_name']) > 50 ? substr($row['first_name'].' '.$row['last_name'], 0, 50) . "..." : $row['first_name'].' '.$row['last_name']); ?></th>
                                <td><?php echo(strlen($row['email']) > 50 ? substr($row['email'], 0, 50) . "..." : $row['email']); ?></th>
                                <td><?php echo($row['rank'] == 'user') ? 'Utilisateur' : (($row['rank'] == 'administrator') ? 'Administrateur' : 'Erreur'); ?></th>
                                <td><?php $date = new DateTime($row['last_login']); echo($date->format('d/m/Y à H:i')); ?></td>
                                <td><a href="/auth/organization/edit-user.php?id=<?php echo($row['id']); ?>" class="btn btn-primary">Modifier</a></th>
                            </tr>
                            <?php
                                }
                            } else {
                            ?> 
                            <p>Vous n'avez pas de membres.</p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>