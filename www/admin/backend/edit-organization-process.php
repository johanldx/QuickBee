<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

$userIsAdmin = userIsAdmin();

if ($userIsAdmin[0] == false) {
    header($userIsAdmin[1], true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $owner = trim($_POST['owner']);
    $plan = trim($_POST['plan']);
    $active = isset($_POST['active']) ? 1 : 0;

    if (empty($_SESSION['edit-organization']) || empty($name) || empty($email) || empty($owner) || empty($plan)) {
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode("Tout les champs doivent être renseignés."), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE organization SET name = :name, email = :email, owner = :owner, plan = :plan WHERE id = :id";
        $params = [
            'name'=> $name,
            'email'=> $email,
            'owner'=> $owner,
            'plan'=> $plan,
            ':id'=>$_SESSION['edit-organization']
        ];
        $stmt = $db->query($sql, $params);

        $success_message = "L'entreprise a bien été modifié.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?success='.urlencode($success_message), true, 301);
        exit;
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}