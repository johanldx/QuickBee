<?php
session_start();
 
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
 
$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

$perms = has_permission();

if (!$perms['new_product']) {
    $error = "Vous ne pouvez plus créer de produits.";
    header('Location: ' . getenv('URL_PATH') . '/app/product/create-product.php?error='.urlencode($error), true, 301);
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $tva = trim($_POST['tva']);
    $price = trim($_POST['price_ht']);
    
 
    if (empty($name) || empty($description) || empty($tva) || empty($price)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/app/product/create-product.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO product (name, description, tva, price_ht, organization) VALUES (:name, :description, :tva, :price, :organization)";
        $params = [
            ':name'=>$name,
            ':description'=>$description,
            ':tva'=>$tva,
            ':price'=>$price,
            ':organization' => $allowedInApp,
        ];
        $stmt = $db->query($sql, $params);
 
        $success_message = "Votre produit a bien été créé.";
        header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/app/product/create-products.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}