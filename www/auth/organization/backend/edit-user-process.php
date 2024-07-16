<!-- a modif -->
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

writeLog('/auth/organization/backend/edit-user-process.php', "Modification d'un utilisateur de l'entreprise.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_SESSION['edit-user'];
    $rank = trim(htmlspecialchars($_POST['rank']));

    unset($_SESSION['edit-captcha']);

    if (empty($user) || empty($rank)) {
        $error = 'Veuillez remplir tous les champs.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.$error, true, 301);
        exit;
    } else if ($rank != "user" && $rank != "administrator") { 
        $error = 'Impossible de trouver le rôle.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.$error, true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT organization FROM user WHERE id = :id";
        $params = ['id' => $user];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if ($result[0]['organization'] == $adminOfOrganization) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "UPDATE user SET rank = :rank WHERE id = :id";
            $params = [
                'rank' => $rank,
                ':id'=> $user
            ];
            $stmt = $db->query($sql, $params);
        } else {
            $error = "L'utilisateur est incorrect.";
            header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.$error, true, 301);
            exit;
        }

        $success_message = "L'utilisateur a bien été modifié.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?success='.urlencode($success_message), true, 301);
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/manage-users.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}