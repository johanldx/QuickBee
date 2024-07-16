<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';

$userIsAdmin = userIsAdmin();

if ($userIsAdmin[0] == false) {
    header($userIsAdmin[1], true, 301);
    exit;
}

$db = new Database();
$conn = $db->connect();
$sql = "SELECT * FROM user";
$params = [];
$stmt = $db->query($sql, $params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->close();

$db = new Database();
$conn = $db->connect();
$sql = "SELECT * FROM organization";
$params = [];
$stmt = $db->query($sql, $params);
$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->close();

writeLog('/admin/dashboard/account.php', "Visité la page des comptes de l'app", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Gestion des comptes</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="../../static/css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/static/css/btn-custom.css">
    </head> 

    <body>
    <?php displayMessage() ?>
        <div class="d-flex">
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="sidebar-logo">
                        <img src="../../static/img/logo-d.png" alt="Logo" width="160px">
                    </div>
                </div>
                <ul class="sidebar-nav list-unstyled">

                    <li class="sidebar-item">
                        <a href="/admin/" class="sidebar-link text-decoration-none">
                            <i class="bi bi-hammer"></i>
                            <span>Panel administrateur</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="dashboard" class="sidebar-link text-decoration-none collapsed has-dropdown" data-bs-toggle="collapse"
                            data-bs-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
                            <i class="bi bi-speedometer"></i>
                            <span>Dashboard</span>
                        </a>
                        <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="/admin/dashboard/accounts" class="sidebar-link text-decoration-none">Comptes</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/admin/dashboard/visits" class="sidebar-link text-decoration-none">Visites</a>
                            </li>
                            <li class="sidebar-item item-selected">
                                <a href="/admin/dashboard/logs" class="sidebar-link text-decoration-none">Logs</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/admin/dashboard/income" class="sidebar-link text-decoration-none">Revenues</a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a href="manage" class="sidebar-link  text-decoration-none collapsed has-dropdown item-selected" data-bs-toggle="collapse"
                            data-bs-target="#manage" aria-expanded="false" aria-controls="manage">
                            <i class="bi bi-kanban-fill"></i>
                            <span>Gérer</span>
                        </a>
                        <ul id="manage" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="/admin/manage/accounts" class="sidebar-link text-decoration-none item-selected">Comptes</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/admin/manage/captchas" class="sidebar-link text-decoration-none">Captchas</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="/admin/manage/newsletters" class="sidebar-link text-decoration-none">Newsletters</a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a href="/help/admin" class="sidebar-link text-decoration-none">
                            <i class="bi bi-patch-question-fill"></i>
                            <span>Support</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="/auth/account/account" class="sidebar-link text-decoration-none">
                            <i class="bi bi-gear-fill"></i>
                            <span>Paramètres</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-footer">
                    <a href="/auth/account/account" target="_blank" class="sidebar-link text-decoration-none">
                        <img src="https://api.dicebear.com/7.x/miniavs/svg?seed=<?php echo($_SESSION['logged']); ?>" alt="avatar" width="15px"/>
                        <span style="padding-left: 15px;">Mon compte</span>
                    </a>
                    <a href="/auth/backend/logout-process" class="sidebar-link text-decoration-none">
                        <i class="bi bi-box-arrow-in-left"></i>
                        <span>Se déconnecter</span>
                    </a>
                </div>
            </aside>

            <main class="main p-3" style="overflow: auto; min-with: 1000px">
                <h1 class="mt-3 mb-3 fs-3 text-start">Compte<?php if ($result && count($result) > 1) { echo("s"); } ?> </h1>

                <div class="bg-body-tertiary rounded p-3">
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>Organisation</th>
                                <th></th>
                            </tr>
                        </thead>
                        <body>
                            <?php
                            if ($result && count($result) >= 1) {
                                foreach ($result as $row) {
                            ?>   
                            <tr>
                                <td class="fw-bold"><?php echo($row['id']); ?></th>
                                <td><?php echo(strlen($row['first_name']) > 50 ? substr($row['first_name'], 0, 50) . "..." : $row['first_name']); ?></th>
                                <td><?php echo(strlen($row['last_name']) > 50 ? substr($row['last_name'], 0, 50) . "..." : $row['last_name']); ?></th>
                                <td><?php echo(empty($row['organization']) ? "Pas d'organisation" : (strlen($row['organization']) > 50 ? substr($row['organization'], 0, 50) . "..." : $row['organization'])); ?></th>
                                <td><a href="/admin/manage/edit-account.php?id=<?php echo($row['id']); ?>" class="btn btn-primary">Modifier</a></th>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </body>
                    </table>

                </div>

                <h1 class="mt-3 mb-3 fs-3 text-start">Organisation<?php if ($result && count($result) > 1) { echo("s"); } ?> </h1>

                <div class="bg-body-tertiary rounded p-3">
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Gérant</th>
                                <th>Plan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <body>
                            <?php
                            if ($result1 && count($result1) >= 1) {
                                foreach ($result1 as $row) {
                            ?>   
                            <tr>
                                <td class="fw-bold"><?php echo($row['id']); ?></th>
                                <td><?php echo(strlen($row['name']) > 50 ? substr($row['name'], 0, 50) . "..." : $row['name']); ?></th>
                                <td><?php echo(strlen($row['owner']) > 50 ? substr($row['owner'], 0, 50) . "..." : $row['owner']); ?></th>
                                <td><?php echo(empty($row['plan']) ? "Pas de plan" : (strlen($row['plan']) > 50 ? substr($row['plan'], 0, 50) . "..." : $row['plan'])); ?></th>
                                <td><a href="/admin/manage/edit-organization.php?id=<?php echo($row['id']); ?>" class="btn btn-primary">Modifier</a></th>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </body>
                    </table>

                </div>
            </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>