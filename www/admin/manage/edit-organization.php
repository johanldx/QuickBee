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

writeLog('/admin/dashboard/edit-entreprise.php', "Visité la page de modification d'entreprise de l'app", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['id'])) {
        $error_message = "L'utilisateur est introuvable.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode($error_message), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, name, email, owner, plan, created_at, active FROM organization WHERE id = :id";
        $params = [':id' => $_GET['id']];
        $stmt = $db->query($sql, $params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if (!$data || count($data) < 1) {
            $error_message = "L'entreprise est introuvable.";
            header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode($error_message), true, 301);
            exit;
        }

        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, first_name, last_name FROM user WHERE organization = :organization";
        $params = [':organization' => $_GET['id']];
        $stmt = $db->query($sql, $params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, name FROM plan";
        $stmt = $db->query($sql);
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        $date = new DateTime($data[0]['created_at']);
        $formattedDate = $date->format('d/m/Y à H:i');

        $_SESSION['edit-organization'] = $_GET['id'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Gestion des entreprises</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="stylesheet" href="../../static/css/style.css">
        <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
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
            
            <main class="main p-3">
                <h1 class="mt-3 mb-3 fs-3 text-start">Modifier l'entreprise <?php echo($data[0]['name']); ?> <?php echo($result[0]['last_name']); ?> (<?php echo($data[0]['id']); ?>)</h1>

                <div class="bg-body-tertiary rounded p-3">
                    <form method="post" action="/admin/backend/edit-organization-process.php">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="first_name" name="name" placeholder="Nom" value="<?php echo($data[0]['name']); ?>" required>
                            <label for="first_name">Nom</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="<?php echo($data[0]['email']); ?>" required>
                            <label for="email">E-mail</label>
                        </div>


                        <div class="form-floating mb-3">
                            <select class="form-select" name="owner" id="owner">
                                <?php
                                if ($data[0]['owner'] == null) {
                                    echo('<option selected value="null">Pas de propriétaire (actuel)</option>');
                                }

                                foreach ($users as $row) {
                                    if ($row['id'] == $data[0]['owner']) {
                                        echo('<option selected value="'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].' (actuel)</option>');
                                    } else {
                                        echo('<option value="'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>');
                                    }
                                }
                                ?>
                            </select>
                            <label for="organization">Propriétaire</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" name="plan" id="plan">
                                <?php
                                if ($data[0]['plan'] == null) {
                                    echo('<option selected value="null">Pas de plan (actuel)</option>');
                                }

                                foreach ($plans as $row) {
                                    if ($row['id'] == $data[0]['plan']) {
                                        echo('<option selected value="'.$row['id'].'">'.$row['name'].'  (actuel)</option>');
                                    } else {
                                        echo('<option value="'.$row['id'].'">'.$row['name'].'</option>');
                                    }
                                }
                                ?>
                            </select>
                            <label for="organization">Plan</label>
                        </div>

                        <div class="form-check form-switch mb-3 text-start my-4">
                            <input type="checkbox" class="form-check-input" id="active" name="active" <?php if ((bool)$data[0]['active']) {echo('checked');} ?>>
                            <label class="form-check-label" for="active">Active</label>
                        </div>

                        <p class="text-start my-4">Créé le <?php echo($formattedDate); ?></p>
                        <p class="fs-5 fw-bold text-danger">Zone de danger</p>
                        <a href="/admin/backend/delete-organization-process.php?id=<?php echo($data[0]['id']); ?>" class="btn btn-danger">Supprimer l'entreprise</a>
                        <div class="d-flex justify-content-start gap-2 mt-4">
                            <a href="/admin/manage/accounts.php" class="btn btn-secondary">Retour</a>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </div>
                    </form>
                </div>
            </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>