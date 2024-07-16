<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/utils.php';

$adminOfOrganization = adminOfOrganization();

if ($adminOfOrganization[0]) {
    $adminOfOrganization = $adminOfOrganization[1];
} else {
    header($adminOfOrganization[1], true, 301);
    exit;
}

writeLog('auth/organization/backend/organization-process.php', "Modification de l'entreprise.", getUserIP(), $_SESSION['logged']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $phone = trim(htmlspecialchars($_POST['phone']));
    $address = trim(htmlspecialchars($_POST['address']));
    $postal_code = trim(htmlspecialchars($_POST['postal_code']));
    $city = trim(htmlspecialchars($_POST['city']));
    $country = trim(htmlspecialchars($_POST['country']));
    $iban = trim(htmlspecialchars($_POST['iban']));
    $bic = trim(htmlspecialchars($_POST['bic']));
    $siren = trim(htmlspecialchars($_POST['siren']));

    if (empty($name) || empty($email)) {
        $error = 'Veuillez remplir un nom et un e-mail.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/organization.php?error='.urlencode($error), true, 301);
        exit;
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez remplir un e-mail valide.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/organization.php?error='.urlencode($error), true, 301);
        exit;
    } 
    else if (!empty($siren) && !preg_match('/^\d{9}$/', $siren)) {
        $error = 'Veuillez remplir un SIREN valide.';
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/organization.php?error='.urlencode($error), true, 301);
        exit;
    }
    else if (!empty($postal_code) && !ctype_digit($postal_code)) {
        $error = "Le code postal doit contenir uniquement des chiffres.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/organization.php?error='.urlencode($error), true, 301);
        exit;
    } 
    else {
        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE organization SET name = :name, email = :email, phone = :phone, address = :address, postal_code = :postal_code, city = :city, country = :country, iban = :iban, bic = :bic, siren = :siren WHERE id = :id";
        $params = [
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':postal_code' => $postal_code,
            ':city' => $city,
            ':country' => $country,
            ':iban' => $iban,
            ':bic' => $bic,
            ':siren' => $siren,
            ':id' => $adminOfOrganization
        ];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        $success_message = "Votre entreprise a été mis à jour.";
        header('Location: ' . getenv('URL_PATH') . '/auth/organization/organization.php?success='.urlencode($success_message), true, 301);
        exit;
    }

} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/organization/organization.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}