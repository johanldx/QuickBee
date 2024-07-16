<?php
session_start();
 
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/document-pdf.php';
 
$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

$perms = has_permission();

if (!$perms['new_quotation']) {
    $error = "Vous ne pouvez plus créer de devis.";
    header('Location: ' . getenv('URL_PATH') . '/app/quotation/create-quotation.php?error='.urlencode($error), true, 301);
    exit;
}

function generateUuidV4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, // 0x4000 = version 4
        mt_rand(0, 0x3fff) | 0x8000, // 0x8000 = variant 2
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = trim($_POST['contact']);
    $client = trim($_POST['client']);
    $issue_date = trim($_POST['issue_date']);
    
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
 
    if (empty($contact) || empty($client) || empty($issue_date) || empty($products) || empty($quantities)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/app/quotation/create-quotation.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO quotation (name, contact, client, issue_date, organization, shared) VALUES (:name, :contact, :client, :issue_date, :organization, :shared)";
        $params = [
            ':name'=>generateUuidV4(),
            ':contact'=>$contact,
            ':client'=>$client,
            ':issue_date'=>$issue_date,
            ':shared' => bin2hex(random_bytes(25)),
            ':organization' => $allowedInApp
        ];
        $stmt = $db->query($sql, $params);

        $quotation_id = $conn->lastInsertId();

        for ($i = 0; $i < count($products); $i++) {
            $product = $products[$i];
            $quantity = $quantities[$i];
    
            $sql = "INSERT INTO quotationline (quotation, product, quantity) VALUES (:quotation, :product, :quantity)";
            $params = [
                ':quotation' => $quotation_id,
                ':product' => $product,
                ':quantity' => $quantity
            ];
            $stmt = $db->query($sql, $params);
        }

        $document = new DocumentGenerator();
        $document->generateQuotation($quotation_id);
 
        $success_message = "Votre devis a bien été créé.";
        header('Location: ' . getenv('URL_PATH') . '/app/quotation/quotations.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/app/quotation/create-quotation.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}