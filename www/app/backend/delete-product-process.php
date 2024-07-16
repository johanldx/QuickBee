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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = trim($_GET['id']);

    if (empty($id)) {
        $error = "Impossible de trouver le produit.";
        header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT p.id, 
            CASE 
                WHEN i.invoice IS NOT NULL THEN 'InvoiceLine'
                WHEN q.quotation IS NOT NULL THEN 'QuotationLine'
                ELSE 'None'
            END as record_type
        FROM product p
        LEFT JOIN invoiceline i ON p.id = i.product
        LEFT JOIN quotationline q ON p.id = q.product
        WHERE p.id = :id";
        $params = [':id' => $id];
        $stmt = $db->query($sql, $params);
        $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();
        
        $allNone = true;
        foreach ($count as $c) {
            if ($c['record_type'] !== 'None') {
                $allNone = false;
                break;
            }
        }

        if (!$allNone) {
            $error = "Impossible de supprimer le produit car il est utilisé dans une facture ou un devis.";
            header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?error='.urlencode($error), true, 301);
            exit;
        }

        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id FROM product WHERE id = :id AND organization = :organization";
        $params = [
            ':id'=>$id,
            ':organization'=> $allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if ($result && count($result) == 1) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "DELETE FROM product WHERE id = :id";
            $params = [
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);
            
            $success_message = "Votre produit a bien été supprimé.";
            header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?success='.urlencode($success_message), true, 301);
            exit;
        }else{
            $error = "Impossible de trouver le produit.";
            header('Location: ' . getenv('URL_PATH') . '/app/product/product.php?error='.urlencode($error), true, 301);
            exit;
        }

    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/app/product/products.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}