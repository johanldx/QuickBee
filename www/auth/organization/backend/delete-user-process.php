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

writeLog('/auth/organization/backend/delete-user-process.php', "Suppression d'un utilisateur à l'entreprise.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = trim(htmlspecialchars($_GET['user']));

    if (empty($user)) {
        $error = 'Impossible de supprimer cet utilisateur.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/edit-user.php?id='.$user.'&error='.$error, true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id FROM user WHERE id = :id AND id <> (SELECT owner FROM organization WHERE id = :organization)";
        $params = [
            ':id' => $user,
            ':organization' => $adminOfOrganization
        ];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if (($result && count($result) == 1)) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "UPDATE user SET rank = :rank, organization = :organization WHERE id = :id";
            $params = [
                'rank' => 'user',
                ':organization' => NULL,
                ':id'=> $user
            ];
            $stmt = $db->query($sql, $params);
        } else {
            $error = "L'utilisateur est incorrect.";
            header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.$error, true, 301);
            exit;
        }

        $success_message = "L'utilisateur a bien été supprimé à votre entreprise.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?success='.urlencode($success_message), true, 301);
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}