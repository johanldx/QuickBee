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

writeLog('/app/backend/search-product.php', "A recherché des produits", getUserIP(), $_SESSION['logged']);
 
header("Content-Type: application/json");
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
 
    if (empty($search)) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, name, description, price_ht FROM product WHERE organization = :organization";
        $params = [
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'products' => $products]);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, name, description, price_ht FROM product WHERE (name LIKE :search OR description LIKE :search) AND organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $conn = $db->connect();
        $sql = "SELECT id, name, description, price_ht FROM product WHERE (name NOT LIKE :search AND NOT description LIKE :search) AND organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $others_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $results = array_merge($products, $others_products);
 
        if ($results) {
            echo json_encode(['status' => 'success', 'products' => $results]);
        } else {
            echo json_encode(['status' => 'not-found', 'message' => 'Aucun produit trouvé']);
        }
        exit;
    }
}