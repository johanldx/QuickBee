<?php
session_start();
 
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';
 
writeLog('/app/backend/search-quotation.php', "A rechercé un devis", getUserIP(), $_SESSION['logged']);
 
$allowedInApp = allowedInApp();
 
if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}
 
header("Content-Type: application/json");
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
 
    if (empty($search)) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT quotation.id, quotation.name, quotation.issue_date, client.company_name FROM quotation JOIN client ON client.id = quotation.client WHERE quotation.organization = :organization";
        $params = [
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'quotations' => $quotations]);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT quotation.id, quotation.name, quotation.issue_date, client.company_name FROM quotation JOIN client ON client.id = quotation.client WHERE (client.company_name LIKE :search OR quotation.name LIKE :search) AND quotation.organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $conn = $db->connect();
        $sql = "SELECT quotation.id, quotation.name, quotation.issue_date, client.company_name FROM quotation JOIN client ON client.id = quotation.client WHERE (client.company_name NOT LIKE :search AND quotation.name NOT LIKE :search) AND quotation.organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $others_quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $results = array_merge($quotations, $others_quotations);
 
        if ($results) {
            echo json_encode(['status' => 'success', 'quotations' => $results]);
        } else {
            echo json_encode(['status' => 'not-found', 'message' => 'Aucun devis trouvé']);
        }
        exit;
    }
}