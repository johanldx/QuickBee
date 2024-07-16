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
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $organization = trim($_POST['organization']);
    $administrator = isset($_POST['administrator']) ? 'administrator' : 'user';
    $active = isset($_POST['active']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $superadmin = isset($_POST['superadmin']) ? 1 : 0;

    if (empty($_SESSION['edit-account']) || empty($first_name) || empty($last_name) || empty($email) || empty($organization) || empty($administrator)) {
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode("Tout les champs doivent être renseignés."), true, 301);
        exit;
    } else {
        if ($organization != 'null') {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT id FROM organization WHERE id = :id";
            $params = [':id' => $organization];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result && count($result) == 1) {
                $db = new Database();
                $conn = $db->connect();
                $sql = "UPDATE user SET first_name = :first_name, last_name = :last_name, email = :email , organization = :organization , rank = :rank , active = :active , newsletter = :newsletter , administrator = :administrator WHERE id = :id";
                $params = [
                    'first_name'=> $first_name,
                    'last_name'=> $last_name,
                    'email'=> $email,
                    'organization'=> $organization,
                    'rank'=> $administrator,
                    'active'=> $active,
                    'newsletter'=> $newsletter,
                    'administrator'=> $superadmin,
                    ':id'=>$_SESSION['edit-account']
                ];
                $stmt = $db->query($sql, $params);
        
                unset($_SESSION['edit-account']);

            } else {
                header('Location: ' . getenv('URL_PATH') . '/admin/manage/account.php?id='.$_SESSION['edit-account'].'&error='.urlencode("Impossible de récupérer l'entreprise."), true, 301);
                exit;
            }
        } else {
            $db = new Database();
            $conn = $db->connect();
            $sql = "UPDATE user SET first_name = :first_name , last_name = :last_name , email = :email , rank = :rank , active = :active , newsletter = :newsletter , administrator = :administrator WHERE id = :id";
            $params = [
                'first_name'=> $first_name,
                'last_name'=> $last_name,
                'email'=> $email,
                'rank'=> $administrator,
                'active'=> $active,
                'newsletter'=> $newsletter,
                'administrator'=> $superadmin,
                ':id'=>$_SESSION['edit-account']
            ];
            $stmt = $db->query($sql, $params);
        }

        $success_message = "L'utilisateur a bien été modifié.";
        header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?success='.urlencode($success_message), true, 301);
        exit;
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/admin/manage/accounts.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}