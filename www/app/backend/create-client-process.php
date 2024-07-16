<?php
session_start();
 
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
 
$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

$perms = has_permission();

if (!$perms['new_client']) {
    $error = "Vous ne pouvez plus créer de clients.";
    header('Location: ' . getenv('URL_PATH') . '/app/client/create-client.php?error='.urlencode($error), true, 301);
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $siren = trim($_POST['siren']);
    $iban = trim($_POST['iban']);
    $address = trim($_POST['address']);
    $postal_code = trim($_POST['postal_code']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    
 
    if (empty($company_name) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($siren) || empty($iban) || empty($address) || empty($postal_code) || empty($city) || empty($country)) {
        $error = "Veuillez remplir tous les champs.";
        header('Location: ' . getenv('URL_PATH') . '/app/client/create-client.php?error='.urlencode($error), true, 301);
        exit;
    } else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO client (company_name, first_name, last_name, email, phone, siren, iban, address, postal_code, city, country, organization) VALUES (:company_name, :first_name, :last_name, :email, :phone, :siren, :iban, :address, :postal_code, :city, :country, :organization)";
        $params = [
            ':company_name'=>$company_name,
            ':first_name'=>$first_name,
            ':last_name'=>$last_name,
            ':email'=>$email,
            ':phone'=>$phone,
            ':siren'=>$siren,
            ':iban'=>$iban,
            ':address'=>$address,
            ':postal_code'=>$postal_code,
            ':city'=>$city,
            ':country'=>$country,
            ':organization' => $allowedInApp,
        ];
        $stmt = $db->query($sql, $params);
 
        $success_message = "Votre client a bien été créé.";
        header('Location: ' . getenv('URL_PATH') . '/app/client/clients.php?success='.urlencode($success_message), true, 301);
        exit;
    }
} else {
    header('Location: ' . getenv('URL_PATH') . '/app/client/create-client.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}