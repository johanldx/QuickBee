<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/database.php';

$userIsAdmin = userIsAdmin();

if ($userIsAdmin[0] == false) {
    header($userIsAdmin[1], true, 301);
    exit;
}

$db = new Database();

$conn = $db->connect();
$sql = "SELECT COUNT(id) as count FROM organization WHERE plan != 1";
$stmt = $db->query($sql);
$organizationPremium = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

$conn = $db->connect();
$sql = "SELECT COUNT(id) as count FROM organization";
$stmt = $db->query($sql);
$organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

$conn = $db->connect();
$sql = "SELECT SUM(plan.price * TIMESTAMPDIFF(MONTH, organization.created_at, NOW())) as sumYear, SUM(plan.price) as sum FROM organization JOIN plan ON organization.plan = plan.id";
$stmt = $db->query($sql);
$income = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

$conn = $db->connect();
$sql = "SELECT COUNT(id) as count FROM user WHERE administrator = 1";
$stmt = $db->query($sql);
$admin = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();

writeLog('/admin/dashboard/income.php', "Visité la page dashboard de l'app", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Statistiques des revenues</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="shortcut icon" href="../../static/img/favicon.ico" type="image/x-icon">
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
                        <a href="dashboard" class="sidebar-link text-decoration-none collapsed has-dropdown item-selected" data-bs-toggle="collapse"
                            data-bs-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
                            <i class="bi bi-speedometer"></i>
                            <span>Dashboard</span>
                        </a>
                        <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse " data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="accounts" class="sidebar-link text-decoration-none">Comptes</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="visits" class="sidebar-link text-decoration-none">Visites</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="logs" class="sidebar-link text-decoration-none">Logs</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="" class="sidebar-link text-decoration-none item-selected">Revenues</a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a href="manage" class="sidebar-link  text-decoration-none collapsed has-dropdown" data-bs-toggle="collapse"
                            data-bs-target="#manage" aria-expanded="false" aria-controls="manage">
                            <i class="bi bi-kanban-fill"></i>
                            <span>Gérer</span>
                        </a>
                        <ul id="manage" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="/admin/manage/accounts" class="sidebar-link text-decoration-none">Comptes</a>
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

            <main class="main p-3">
                <h1 class="mt-3 mb-3 fs-3 text-start">Statistiques des revenues</h1>
                <p>Les données peuvent ne pas être précise à 100%. Pour plus de données connectez vous à Stripe.</p>

                <div class="mb-3">
                    <a href="https://dashboard.stripe.com/" class="btn btn-primary" target="_blank">Ouvrir Stripe</a>
                </div>
                
                <div class="row row-cols-md-1 row-cols-md-2 g-2">
                    <div class="col">
                      <div class="card bg-light-subtle">
                        <div class="card-body">
                          <h5 class="fs-6">Entreprise avec forfait payant</h5>
                          <p class="fs-4 fw-semibold"><?php echo $organizationPremium[0]['count']; ?>/<?php echo $organization[0]['count']; ?></p>
                        </div>
                      </div>
                    </div>

                    <div class="col">
                        <div class="card bg-light-subtle">
                          <div class="card-body">
                            <h5 class="fs-6">Revenues sur la dernière année</h5>
                            <p class="fs-4 fw-semibold"><?php echo $income[0]['sumYear']; ?> €</p>
                          </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card bg-light-subtle">
                          <div class="card-body">
                            <h5 class="fs-6">Revenues sur le mois en cours</h5>
                            <p class="fs-4 fw-semibold"><?php echo $income[0]['sum']; ?> €</p>
                          </div>
                        </div>
                    </div>
                </div>
            </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>