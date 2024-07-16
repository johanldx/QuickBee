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

writeLog('/app/product/edit-product.php', "Visité la page de modification de produit de l'app", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['id'])) {
        $error_message = "Le produit est introuvable.";
        header('Location: ' . getenv('URL_PATH') . '/app/product/product.php?error='.urlencode($error_message), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, name, tva, description, price_ht FROM product WHERE id = :id AND :organization = organization";
        $params = [':id' => $_GET['id'], ':organization' => $allowedInApp];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if (!$result || count($result) < 1) {
            $error_message = "Le produit est introuvable.";
            header('Location: ' . getenv('URL_PATH') . '/app/product/product.php?error='.urlencode($error_message), true, 301);
            exit;
        }

        $date = new DateTime($result[0]['created_at']);
        $formattedDate = $date->format('d/m/Y à H:i');

        $_SESSION['edit-product'] = $_GET['id'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Modification de produits</title>
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
                        <a href="../client/clients.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-people-fill"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="products.php" class="sidebar-link text-decoration-none item-selected">
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
                <h1 class="mt-3 mb-3 fs-3 text-start">Modifier le produit <?php echo($result[0]['name']); ?> (<?php echo($result[0]['id']); ?>)</h1>

                <div class="bg-body-tertiary rounded p-3">
                    <form method="post" action="/app/backend/edit-product-process.php">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo($result[0]['name']); ?>" required>
                            <label for="name">Nom</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" class="form-control" id="price_ht" name="price_ht" placeholder="Prix" value="<?php echo($result[0]['price_ht']); ?>" required>
                            <label for="price_ht">Prix</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo($result[0]['description']); ?>" required>
                            <label for="description">Description</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" step="0.01" min="0" max="1" class="form-control" id="tva" name="tva" placeholder="TVA" value="<?php echo($result[0]['tva']); ?>" required>
                            <label for="tva">TVA</label>
                        </div>
                     
                        <a href="/app/backend/delete-product-process.php" class="btn btn-danger">Supprimer le produit</a>
                        <div class="d-flex justify-content-start gap-2 mt-4">
                            <a href="/app/product/products.php" class="btn btn-secondary">Retour</a>
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