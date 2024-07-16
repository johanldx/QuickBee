<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price_ht = trim($_POST['price_ht']);
    $description = trim($_POST['description']);
    $tva = trim($_POST['tva']);
    
    if (empty($_SESSION['edit-product']) || empty($name) || empty($price_ht) || empty($description) || empty($tva)) {
        header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?error='.urlencode("Tous les champs doivent être renseignés."), true, 301);
        exit;
    } else {

        $product_id = $_SESSION['edit-product'];
       
        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE product SET name = :name , price_ht = :price_ht , description = :description , tva = :tva WHERE id = :id";
        $params = [
                ':name'=> $name,
                ':price_ht'=> $price_ht,
                ':description'=> $description,
                ':tva'=> $tva,
                ':id' => $product_id
            ];
        $stmt = $db->query($sql, $params);
            
        unset($_SESSION['edit-product']);

        $success_message = "Le produit a bien été modifié.";
        header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?success='.urlencode($success_message), true, 301);
        exit;
    }

}else {
    header('Location: ' . getenv('URL_PATH') . '/app/product/edit-product.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}