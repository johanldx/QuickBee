<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 1));
require_once ROOT_PATH . '/php/utils.php';

writeLog('/legal-notice', "Visité la page des mentions légales", getUserIP());

?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
    <title>QuickBee - Mentions légales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap-reboot.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap-grid.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="/static/css/btn-custom.css">
</head>
<body class="m-5">
    <a class="btn btn-secondary" href="/">Retour sur le site</a>

    <h1 class="mt-3">Mentions légales</h1>
    <h2 class="mt-3">Identidé</h2>
    <p>
        Dénomination sociale : <span class="fw-bold">Quickbee</span> <br>
        Forme juridique : <span class="fw-bold">SARL</span> <br>
        Montant du capital social : <span class="fw-bold">-30 €</span> <br>
    </p>

    <h2 class="mt-3">Coordonnées</h2>
    <p>
        Adresse : <span class="fw-bold"> 242 Rue du Faubourg Saint-Antoine, 75012 Paris</span> <br>
        Téléphone : <span class="fw-bold">+33 7 68 04 41 12</span> <br>
        Email : <span class="fw-bold">quickbee@rootage.fr</span>
    </p>

    <h2 class="mt-3">Mentions relatives à la propriété intellectuelle</h2>
    <p>
        <span class="fw-bold">Images, illustrations, photographies :</span> <br>
        Les images, illustrations et photographies utilisées sur ce site sont protégées par des droits de propriété intellectuelle. Toute utilisation de ces éléments doit être autorisée par leur propriétaire.
        <br>
        <span class="fw-bold">Textes :</span> <br>
        Les textes présents sur ce site, sauf mention contraire, sont la propriété de Quickbee. Toute reproduction totale ou partielle de ces textes sans autorisation est interdite.  
    </p>

    <h2 class="mt-3">Hébergement du site</h2>
    <p>
        L'hébergement du site est assuré par la société <span class="fw-bold">OVH</span> <br>
        Adresse : <span class="fw-bold">2 rue Kellermann - 59100 Roubaix - France</span> <br>
        Téléphone : <span class="fw-bold">+33 9 72 10 10 07</span>
</body>