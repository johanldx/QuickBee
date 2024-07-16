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

writeLog('/app/backend/search-invoices.php', "A recherché une facture", getUserIP(), $_SESSION['logged']);
 
header("Content-Type: application/json");
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
 
    if (empty($search)) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT invoice.id, invoice.name, invoice.issue_date, client.company_name FROM invoice JOIN client ON client.id = invoice.client WHERE invoice.organization = :organization";
        $params = [
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'invoices' => $invoices]);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT invoice.id, invoice.name, invoice.issue_date, client.company_name FROM invoice JOIN client ON client.id = invoice.client WHERE (client.company_name LIKE :search OR invoice.name LIKE :search) AND invoice.organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $conn = $db->connect();
        $sql = "SELECT invoice.id, invoice.name, invoice.issue_date, client.company_name FROM invoice JOIN client ON client.id = invoice.client WHERE (client.company_name NOT LIKE :search AND invoice.name NOT LIKE :search) AND invoice.organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $others_invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $results = array_merge($invoices, $others_invoices);
 
        if ($results) {
            echo json_encode(['status' => 'success', 'invoices' => $results]);
        } else {
            echo json_encode(['status' => 'not-found', 'message' => 'Aucune facture trouvée']);
        }
        exit;
    }
}