<?php
session_start();
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

writeLog('/app/quotation/quotation.php', "Visité la page de visualisation de devis de l'app", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['id'])) {
        $error_message = "Le devis est introuvable.";
        header('Location: ' . getenv('URL_PATH') . '/app/quotation/quotations.php?error='.urlencode($error_message), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT client.company_name AS name, quotation.issue_date, quotation.name AS id, CONCAT(user.first_name, ' ', user.last_name) AS contact, quotation.id AS ID_2, quotation.shared FROM quotation JOIN client ON client.id = quotation.client JOIN user ON user.id = quotation.contact WHERE quotation.id = :id";
        $params = [':id' => $_GET['id']];
        $stmt = $db->query($sql, $params);
        $quotation = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();
        
        $conn = $db->connect();
        $sql = "SELECT product.name AS product, product.price_ht, quotationline.quantity FROM quotationline JOIN product ON quotationline.product = product.id WHERE quotationline.quotation = :id";
        $params = [':id' => $_GET['id']];
        $stmt = $db->query($sql, $params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if (!$quotation || count($quotation) < 1) {
            $error_message = "Le devis est introuvable.";
            header('Location: ' . getenv('URL_PATH') . '/app/quotation/quotations.php?error='.urlencode($error_message), true, 301);
            exit;
        }

        $date = new DateTime($result[0]['send_at']);
        $formattedDate = $date->format('d/m/Y H:i');
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Modification de devis</title>
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
                        <a href="quotations.php" class="sidebar-link text-decoration-none item-selected">
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
            <div class="main p-3">
                <div class="d-flex ">
                    <h1 class="fs-4 text-start">Devis n° <?php  echo($quotation[0]['id']); ?></h1>
                </div>

                <div class="bg-light-subtle rounded p-3 mt-2">        
                        <form method="" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>Client</h3>
                                    <input type="text" class="form-control mt-2" aria-label="Client" name="client" value="<?php echo($quotation[0]['name']) ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <h3>Contact</h3>
                                    <input type="text" class="form-control mt-2" aria-label="Contact" name="contact" value="<?php echo($quotation[0]['contact']) ?>" disabled>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                        <h3>Date d'émission du devis</h3>
                                        <input type="text" class="form-control mt-2" aria-label="Date d'émission de la facture" name="issue_date" value="<?php $date = new DateTime($quotation[0]['issue_date']); echo($date->format('d/m/Y')); ?>" disabled>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                            <?php
                            if ($products && count($products) >= 1) {
                                foreach ($products as $row) {
                            ?> 
                                <div class="col-md-4">
                                    <h3>Produit</h3>
                                    <input type="text" class="form-control mt-2" aria-label="Produit" name="product" value="<?php echo($row['product']) ?>" disabled>
                                </div>
                                                                    
                                <div class="col-md-4">
                                    <h3>Quantité</h3>
                                    <input type="text" class="form-control mt-2" aria-label="Quantité" name="quantity" value="<?php echo($row['quantity']) ?>" disabled>
                                </div>
                                    
                                <div class="col-md-4">
                                    <h3>Prix</h3>
                                    <input type="text" class="form-control mt-2" aria-label="Prix" name="price" value="<?php echo($row['price_ht']) ?> €" disabled>
                                </div>
                            <?php
                                }
                            }
                            ?>
                            </div>
                                    
                            <div class="d-flex justify-content-start gap-4 mt-4">
                                <a href="/app/quotation/quotations.php" class="btn btn-secondary">Retour</a>
                                <a href="/share?type=quotation&id=<?php echo($quotation[0]['ID_2']); ?>&token=<?php echo($quotation[0]['shared']); ?>" class="btn btn-primary">Exporter</a>
                            </div>
                        </form>
                    
                </div>
            </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>