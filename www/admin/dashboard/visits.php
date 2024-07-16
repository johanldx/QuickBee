<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';

$userIsAdmin = userIsAdmin();

if ($userIsAdmin[0] == false) {
    header($userIsAdmin[1], true, 301);
    exit;
}

$logDir = ROOT_PATH . '/logs';

$selectedFile = '';
$fileContent = '';

$logFiles = array_diff(scandir($logDir, SCANDIR_SORT_DESCENDING), array('..', '.'));
$latestLogFile = reset($logFiles);

if (isset($_GET['logFile'])) {
    $selectedFile = $_GET['logFile'];
} else {
    $selectedFile = $latestLogFile;
}

$content = false;
$endpointVisits = [];

$filePath = $logDir . '/' . $selectedFile;
if (file_exists($filePath)) {
    $fileContent = file_get_contents($filePath);
    $logLines = explode(PHP_EOL, $fileContent);

    foreach ($logLines as $line) {
        if (preg_match('/^(.+?) : (.+?) : (.+?) : (.+)$/', $line, $matches)) {
            $endpoint = $matches[3];

            if (isset($endpointVisits[$endpoint])) {
                $endpointVisits[$endpoint]++;
            } else {
                $endpointVisits[$endpoint] = 1;
            }
        }
    }

    arsort($endpointVisits);

    $content = true;
}

writeLog('/admin/dashboard/visits.php', "Visité la page des logs", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Visites</title>
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
                        <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="accounts" class="sidebar-link text-decoration-none">Comptes</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="" class="sidebar-link text-decoration-none item-selected">Visites</a>
                            </li>
                            <li class="sidebar-item item-selected">
                                <a href="logs" class="sidebar-link text-decoration-none">Logs</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="income" class="sidebar-link text-decoration-none">Revenues</a>
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
                <h1 class="mt-3 mb-3 fs-3 text-start">Visites du site</h1>

                <form action="" method="get">
                    <div class="form-floating">
                        <select class="form-select" name="logFile" id="logFile">
                            <?php
                                foreach ($logFiles as $file) {
                                    echo '<option value="' . htmlspecialchars($file) . '">' . htmlspecialchars($file) . '</option>';
                                }
                            ?>
                        </select>
                        <label for="logFile">Choisir un fichier de logs</label>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
                </form>

                <?php if ($content): ?>

                <h5 class="mt-4 fs-6">Logs : <?php echo $selectedFile; ?></h5>

                <div class="mt-4">
                    <div class="card bg-light-subtle">
                        <div class="card-body bg-dark border border-light-subtle rounded">
                            <p class="text-light">
                                <?php 
                                    foreach ($endpointVisits as $endpoint => $count) {
                                        echo "<span class=\"text-warning\">{$endpoint}</span> : {$count} <br>";
                                    }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php else: ?>
                
                <div class="mt-4">
                    <div class="card bg-light-subtle">
                        <div class="card-body bg-dark border border-light-subtle rounded">
                            <p class="text-light">
                                Le fichier sélectionné n'existe pas.
                            </p>
                        </div>
                    </div>
                </div>

                <?php endif; ?>
            </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/theme.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>