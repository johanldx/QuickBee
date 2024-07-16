<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/utils.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

writeLog('/app/about.php', "Visité la page à propos de l'app", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - À propos</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="stylesheet" href="../static/css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/static/css/btn-custom.css"> 
        <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
    </head> 

    <body>
        <div class="d-flex">
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="sidebar-logo">
                        <img src="../static/img/logo-d.png" alt="Logo" width="160px">
                    </div>
                </div>
                <ul class="sidebar-nav list-unstyled">
                    <li class="sidebar-item">
                        <a href="invoice/invoices.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-clipboard2-check-fill"></i>
                            <span>Factures</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="quotation/quotations.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-clipboard-plus-fill"></i>
                            <span>Devis</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="client/clients.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-people-fill"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="product/products.php" class="sidebar-link text-decoration-none">
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
                        <a href="about.php" class="sidebar-link text-decoration-none item-selected">
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
                    <h1 class="fs-4 text-start">A propos de QuickBee</h1>
                    <div class="d-grid d-md-flex justify-content-md-end">
                        <i class="bi bi-sun"></i>
                    </div>
                </div>

                <div class="d-flex justify-content-start">
                    <div class="col">
                        <span style="color: black;"><p class="text-center fw-light fs-4 mt-3">Bienvenue sur QuickBee, votre solution complète pour la gestion de facturation et de devis sur mesure. QuickBee vous permet de créer facilement et rapidement des factures et des devis personnalisés, tout en offrant une gestion efficace de vos clients et de vos produits. Que vous soyez un entrepreneur indépendant, une petite entreprise ou une grande société, notre plateforme intuitive et conviviale est conçue pour simplifier vos processus administratifs et améliorer votre productivité. Avec QuickBee, gardez le contrôle de votre activité grâce à des outils performants et accessibles, et concentrez-vous sur ce qui compte vraiment : faire croître votre entreprise.</p></span>
                    </div>
                </div>

                
                    <div class="row row-cols-3 g-3 mt-3">
                        <div class="col mt-6">
                            <i class="bi bi-clipboard-check display-1 me-5"></i>
                            <p class="fs-5">Facturation simplifiée</p>
                            <p>Chez QuickBee, nous savons que la gestion des factures peut être complexe et chronophage. C'est pourquoi nous avons simplifié le processus de création de factures pour vous. Grâce à notre interface intuitive, vous pouvez générer des factures professionnelles en quelques clics seulement. Personnalisez vos factures avec vos logos, vos conditions de paiement et vos informations spécifiques, et envoyez-les directement à vos clients. Notre système automatisé vous permet de suivre les paiements et d'envoyer des rappels, vous assurant ainsi de ne jamais manquer une échéance. Avec QuickBee, la facturation devient un jeu d'enfant, vous permettant de vous concentrer sur l'essentiel : le développement de votre activité.</p>
                        </div>

                        <div class="col mt-6">
                            <i class="bi bi-clipboard-data display-1 me-5"></i>
                            <p class="fs-5">Devis sur mesure</p>
                            <p>Avec QuickBee, la création de devis sur mesure n'a jamais été aussi simple et efficace. Notre plateforme vous permet de générer des devis personnalisés en un clin d'œil, adaptés spécifiquement aux besoins de vos clients. Vous pouvez facilement ajouter vos produits et services, ajuster les prix, et inclure des conditions particulières ou des remises. Grâce à notre interface conviviale, chaque devis reflète votre professionnalisme et votre attention aux détails. Envoyez vos devis directement à vos clients par email et suivez leur statut en temps réel. Avec QuickBee, offrez à vos clients une expérience professionnelle et transparente dès le premier contact.</p>
                        </div>

                        <div class="col mt-6">
                            <i class="bi bi-credit-card-2-back display-1 me-5"></i>
                            <p class="fs-5">Paiement sécurisé</p>
                            <p>Chez QuickBee, nous comprenons l'importance de la sécurité et de la fiabilité dans la gestion des paiements. C'est pourquoi nous avons intégré Stripe, une solution de paiement en ligne reconnue et sécurisée. Grâce à Stripe, vos transactions sont protégées par les normes de sécurité les plus élevées, garantissant la confidentialité et l'intégrité de vos données financières. Offrez à vos clients la possibilité de payer leurs factures en ligne de manière simple et sécurisée, tout en bénéficiant d'un suivi précis des paiements reçus. Avec QuickBee et Stripe, gérez vos paiements en toute tranquillité et concentrez-vous sur le développement de votre entreprise.</p>
                        </div>
                    </div>
                

            </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../static/js/script.js"></script>
</body>
</html>