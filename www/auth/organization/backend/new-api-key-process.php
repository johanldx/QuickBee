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

$perms = has_permission();

if (!$perms['new_api_key']) {
    $error = 'Vous ne pouvez pas créer de nouvelle clé API.';
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-api-key.php?error='.$error, true, 301);
    exit;
}


writeLog('/auth/organization/backend/new-api-key-process.php', "Création d'une clé API.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(htmlspecialchars($_POST['name']));

    if (empty($name)) {
        $error = 'Veuillez remplir tous les champs.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/new-api-key.php?error='.$error, true, 301);
        exit;
    } else {
        $api_key = bin2hex(random_bytes(25));
        
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO apikey (name, token, organization) VALUES (:name, :token, :organization)";
        $params = [
            ':name' => $name,
            ':token' => $api_key,
            ':organization' => $adminOfOrganization
        ];
        $stmt = $db->query($sql, $params);
        $db->close();

        $success_message = "La clé d'API a bien été créé.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-api-keys.php?success='.urlencode($success_message), true, 301);
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/new-api-key.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}