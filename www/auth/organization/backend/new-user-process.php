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

if (!$perms['new_user']) {
    $error = 'Vous ne pouvez pas ajouter de nouveaux utilisateurs.';
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.$error, true, 301);
    exit;
}

writeLog('/auth/organization/backend/new-user-process.php', "Ajout d'un utilisateur à l'entreprise.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim(htmlspecialchars($_POST['user']));
    $rank = trim(htmlspecialchars($_POST['rank']));

    if (empty($user) || empty($rank)) {
        $error = 'Veuillez remplir tous les champs.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/new-user.php?error='.$error, true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT organization FROM user WHERE id = :id";
        $params = ['id' => $user];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if ($result[0]['organization'] == null) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "UPDATE user SET organization = :organization, rank = :rank WHERE id = :id";
            $params = [
                ':organization' => $adminOfOrganization,
                'rank' => $rank,
                ':id'=> $user
            ];
            $stmt = $db->query($sql, $params);
        } else {
            $error = "L'utilisateur est incorrect.";
            header('Location: ' . getenv('URL_PATH') . '/auth/organization/new-user.php?error='.$error, true, 301);
            exit;
        }

        $success_message = "L'utilisateur a bien été ajouté à votre entreprise.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?success='.urlencode($success_message), true, 301);
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/new-user.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}