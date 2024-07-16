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

writeLog('/app/backend/search-client.php', "A recherché des clients", getUserIP(), $_SESSION['logged']);

header("Content-Type: application/json");
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
 
    if (empty($search)) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, company_name, first_name, last_name, phone, address FROM client WHERE organization = :organization";
        $params = [
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'clients' => $clients]);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, company_name, first_name, last_name, phone, address FROM client WHERE (company_name LIKE :search OR first_name LIKE :search OR last_name LIKE :search) AND organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $conn = $db->connect();
        $sql = "SELECT id, company_name, first_name, last_name, phone, address FROM client WHERE (company_name NOT LIKE :search AND first_name NOT LIKE :search AND last_name NOT LIKE :search) AND organization = :organization";
        $params = [
            ':search'=>'%'.$search.'%',
            ':organization'=>$allowedInApp
        ];
        $stmt = $db->query($sql, $params);
        $others_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $results = array_merge($clients, $others_clients);
 
        if ($results) {
            echo json_encode(['status' => 'success', 'clients' => $results]);
        } else {
            echo json_encode(['status' => 'not-found', 'message' => 'Aucun client trouvé']);
        }
        exit;
    }
}
