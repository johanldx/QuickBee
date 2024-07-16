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

$permissions = has_permission();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['id'])) {
        $error_message = "L'utilisateur est introuvable.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.urlencode($error_message), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, first_name, last_name, email FROM user WHERE id = :id AND organization = :organization AND id <> (SELECT owner FROM organization WHERE id = :organization)";
        $params = [
            ':id' => $_GET['id'],
            ':organization' => $adminOfOrganization
        ];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if (!$result || count($result) != 1) {
            $error_message = "L'utilisateur est introuvable";
            header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.urlencode($error_message), true, 301);
            exit; 
        } else {
            $_SESSION['edit-user'] = $_GET['id'];
        }
    }
}

writeLog('/auth/organization/manage-api-keys.php', "Consultation des clés API.", getUserIP(), $_SESSION['logged']);

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
    <title>Inviter un utilisateur - QuickBee</title>
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
            <p class="card-title fs-2 mb-5 fw-bold">Inviter un utilisateur</p>

            <form action="/auth/organization/backend/edit-user-process.php" method="post" class="text-center mb-3 container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 mx-5">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="user" name="user" placeholder="Membre" value="<?php echo($result[0]['first_name'].' '.$result[0]['last_name'].' ('.$result[0]['email'].')'); ?>" disabled>
                            <label for="user">Membre</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select mb-3" name="rank" id="rank">
                                <option value="user">Utilisateur</option>
                                <option value="administrator" class="text-danger">Administrateur</option>
                            </select>
                            <label for="rank">Choisir son rôle</label>
                        </div>
                    </div>
                </div>
                <a href="/auth/organization/manage-users.php" class="btn btn-secondary btn-lg mt-3 mb-3 text-center">Retour</a>
                <input type="submit" class="btn btn-primary btn-lg mt-3 mb-3 text-center" value="Valider"></input>
                <a href="/auth/organization/backend/delete-user-process.php?user=<?php echo($result[0]['id']); ?>" class="btn btn-danger btn-lg mt-3 mb-3 text-center">Retirer de l'entreprise</a>
            </form>
        </div>

    </div>
    <script src="../../static/js/theme.js"></script>
    <script src="../../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>