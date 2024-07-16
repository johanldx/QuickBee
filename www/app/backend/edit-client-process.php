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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $postal_code = trim($_POST['postal_code']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    
    if (empty($_SESSION['edit-client']) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($address) || empty($postal_code) || empty($city) || empty($country)) {
        header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?error='.urlencode("Tout les champs doivent être renseignés."), true, 301);
        exit;
    } else {
       
        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE client SET first_name = :first_name , last_name = :last_name , email = :email , address = :address, phone = :phone , postal_code = :postal_code , city = :city , country = :country WHERE id = :id";
        $params = [
                ':first_name'=> $first_name,
                ':last_name'=> $last_name,
                ':email'=> $email,
                ':address'=> $address,
                ':phone'=> $phone,
                ':postal_code'=> $postal_code,
                ':city'=> $city,
                ':country'=> $country,
                ':id'=>$_SESSION['edit-client']
            ];
        $stmt = $db->query($sql, $params);
            
        unset($_SESSION['edit-client']);

        $success_message = "Le client a bien été modifié.";
        header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?success='.urlencode($success_message), true, 301);
        exit;
    }

}else {
    header('Location: ' . getenv('URL_PATH') . '/app/client/edit-client.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}