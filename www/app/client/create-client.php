<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}



writeLog('/app/client/new-client.php', "Visité la page de création de client de l'app", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Création de clients</title>
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
            <div class="main p-3">
                <div class="d-flex justify-content-between">
                    <h1 class="fs-4 text-start">Nouveau client</h1>
                    <div class="d-grid d-md-flex justify-content-md-end">
                        <i class="bi bi-sun"></i>
                    </div>
                </div>

                <div class="bg-light-subtle rounded p-3 mt-2">        
            
                        <div class="d-flex justify-content-start">
                            <p class="fs-5">Créer un nouveau client</p>
                        </div>
                        <form action="../backend/create-client-process.php" method="post">
                            <div class="row">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Nom de l'entreprise" required>
                                    <label for="company_name">Nom de l'entreprise</label>
                                </div>
                            </div>
                            <div class="row mt-3">

                                <div class="form-floating col">
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Prénom" required>
                                    <label for="first_name">Prénom</label>
                                </div>
                                <div class="form-floating col">
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Nom" required>
                                    <label for="last_name">Nom</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="E-mail" aria-label="E-mail" name="email">
                                <label for="email">E-mail</label>
                                </div>
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="Téléphone" aria-label="Téléphone" name="phone">
                                <label for="phone">Téléphone</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="SIREN" aria-label="SIREN" name="siren">
                                <label for="siren">SIREN</label>
                                </div>
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="IBAN" aria-label="IBAN" name="iban">
                                <label for="iban">IBAN</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="Adresse" aria-label="Adresse" name="address">
                                <label for="address">Adresse</label>
                                </div>
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="Code postal" aria-label="Code postal" name="postal_code">
                                <label for="postal_code">Code postal</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="Ville" aria-label="Ville" name="city">
                                <label for="city">Ville</label>
                                </div>
                                <div class="form-floating col">
                                <input type="text" class="form-control" placeholder="Pays" aria-label="Pays" name="country">
                                <label for="country">Pays</label>
                                </div>
                            </div>
                        
                            <div class="d-flex justify-content-start gap-4 mt-4">
                                <a href="/app/client/clients.php" class="btn btn-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">Valider</button>
                            </div>
                        </form>
                </div>
            </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>