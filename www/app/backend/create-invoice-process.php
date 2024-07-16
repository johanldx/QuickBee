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

if (!$perms['new_invoice']) {
    $error = "Vous ne pouvez plus créer de factures.";
    header('Location: ' . getenv('URL_PATH') . '/app/invoice/create-invoice.php?error='.urlencode($error), true, 301);
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
    $due_date = trim($_POST['due_date']);
    $footer = trim($_POST['footer']) ?? '';

    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
 
    if (empty($contact) || empty($client) || empty($issue_date) || empty($due_date) || empty($products) || empty($quantities)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/app/invoice/create-invoice.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO invoice (name, contact, client, issue_date, due_date, footer, organization, shared) VALUES (:name, :contact, :client, :issue_date, :due_date, :footer, :organization, :shared)";
        $params = [
            ':name'=>generateUuidV4(),
            ':contact'=>$contact,
            ':client'=>$client,
            ':issue_date'=>$issue_date,
            ':due_date'=>$due_date,
            ':footer'=>$footer,
            ':organization' => $allowedInApp,
            ':shared' => bin2hex(random_bytes(25))
        ];
        $stmt = $db->query($sql, $params);

        $invoice_id = $conn->lastInsertId();

        for ($i = 0; $i < count($products); $i++) {
            $product = $products[$i];
            $quantity = $quantities[$i];
    
            $sql = "INSERT INTO invoiceline (invoice, product, quantity) VALUES (:invoice, :product, :quantity)";
            $params = [
                ':invoice' => $invoice_id,
                ':product' => $product,
                ':quantity' => $quantity
            ];
            $stmt = $db->query($sql, $params);
        }

        $document = new DocumentGenerator();
        $document->generateInvoice($invoice_id);

        $success_message = "Votre facture a bien été créée.";
        header('Location: ' . getenv('URL_PATH') . '/app/invoice/invoices.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/app/invoice/create-invoice.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}