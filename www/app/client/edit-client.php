<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/database.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

writeLog('/client/edit-client.php', "Visité la page de modification de client de l'app", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['id'])) {
        $error_message = "Le client est introuvable.";
        header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode($error_message), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, first_name, last_name, email, phone, address, postal_code, city, country FROM client WHERE id = :id AND organization = :organization";
        $params = [
            ':id' => $_GET['id'],
            ':organization' => $allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if (!$result || count($result) < 1) {
            $error_message = "Le client est introuvable.";
            header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode($error_message), true, 301);
            exit;
        }

        $date = new DateTime($result[0]['created_at']);
        $formattedDate = $date->format('d/m/Y à H:i');

        $_SESSION['edit-client'] = $_GET['id'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Modification de clients</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="stylesheet" href="../../static/css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/static/css/btn-custom.css"> 
        <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
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
                        <a href="../invoice/invoices.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-clipboard2-check-fill"></i>
                            <span>Factures</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../quotation/quotations.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-clipboard-plus-fill"></i>
                            <span>Devis</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="clients.php" class="sidebar-link text-decoration-none item-selected">
                            <i class="bi bi-people-fill"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../product/products.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-cart-check-fill"></i>
                            <span>Produits</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/api/documentation/" class="sidebar-link text-decoration-none">
                        <i class="bi bi-server"></i>
                            <span>API</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../about.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-bookmark-fill"></i>
                            <span>A propos</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/help/" class="sidebar-link text-decoration-none">
                        <i class="bi bi-chat-left-text-fill"></i>
                            <span>Support</span>
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
                <h1 class="mt-3 mb-3 fs-3 text-start">Modifier le compte client de <?php echo($result[0]['first_name']); ?> <?php echo($result[0]['last_name']); ?> (<?php echo($result[0]['id']); ?>)</h1>

                <div class="bg-body-tertiary rounded p-3">
                    <form method="post" action="/app/backend/edit-client-process.php">

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
                            <label for="email">E-mail</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Téléphone" value="<?php echo($result[0]['phone']); ?>" required>
                            <label for="phone">Téléphone</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Adresse" value="<?php echo($result[0]['address']); ?>" required>
                            <label for="address">Adresse</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="postal_code" name="postal_code" placeholder="Code postal" value="<?php echo($result[0]['postal_code']); ?>" required>
                            <label for="postal_code">Code postal</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="city" name="city" placeholder="Ville" value="<?php echo($result[0]['city']); ?>" required>
                            <label for="city">Ville</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="country" name="country" placeholder="Pays" value="<?php echo($result[0]['country']); ?>" required>
                            <label for="country">Pays</label>
                        </div>
                        
                        
                        <div class="d-flex justify-content-start gap-2 mt-4">
                            <a href="/app/client/clients.php" class="btn btn-secondary">Retour</a>
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