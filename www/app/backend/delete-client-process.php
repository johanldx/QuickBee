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
        $error = "Impossible de trouver le client.";
        header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT c.id, c.company_name, 
            CASE 
                WHEN i.id IS NOT NULL THEN 'Invoice'
                WHEN q.id IS NOT NULL THEN 'Quotation'
                ELSE 'None'
            END as record_type
        FROM client c
        LEFT JOIN invoice i ON c.id = i.client
        LEFT JOIN quotation q ON c.id = q.client
        WHERE c.id = :id";
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
            $error = "Impossible de supprimer le client car il est utilisé dans une facture ou un devis.";
            header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode($error), true, 301);
            exit;
        }

        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id FROM client WHERE id = :id AND organization = :organization";
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
            $sql = "DELETE FROM client WHERE id = :id";
            $params = [
                ':id'=>$id
            ];
            $stmt = $db->query($sql, $params);
            
            $success_message = "Votre client a bien été supprimé.";
            header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?success='.urlencode($success_message), true, 301);
            exit;
        }else{
            $error = "Impossible de trouver le client.";
            header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode($error), true, 301);
            exit;
        }

    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}