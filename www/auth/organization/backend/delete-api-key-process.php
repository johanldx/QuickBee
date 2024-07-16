<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

$adminOfOrganization = adminOfOrganization();

if ($adminOfOrganization[0]) {
    $adminOfOrganization = $adminOfOrganization[1];
} else {
    header($adminOfOrganization[1], true, 301);
    exit;
}

writeLog('/auth/organization/backend/delete-api-key-process.php', "Supression d'une clé API.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = trim(htmlspecialchars($_GET['id']));

    if (empty($id)) {
        $error = 'Impossible de trouver la clé API.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-api-keys.php?error='.$error, true, 301);
        exit;
    } else {        
        $db = new Database();
        $conn = $db->connect();
        $sql = "DELETE FROM apikey WHERE id = :id AND organization = :organization";
        $params = [
            ':id' => $id,
            ':organization' => $adminOfOrganization
        ];
        $stmt = $db->query($sql, $params);
        $db->close();

        $success_message = "La clé d'API a bien été supprimé. Attention à vérifier vos environnements de production !";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-api-keys.php?success='.urlencode($success_message), true, 301);
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-api-keys.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}