<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/vendor/autoload.php';

$adminOfOrganization = adminOfOrganization();

if ($adminOfOrganization[0]) {
    $adminOfOrganization = $adminOfOrganization[1];
} else {
    header($adminOfOrganization[1], true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT stripe_customer_id FROM organization WHERE id = :id";
    $params = [':id' => $adminOfOrganization];
    $stmt = $db->query($sql, $params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    $stripe = \Stripe\Stripe::setApiKey(getenv('STRIPE_API_KEY'));

    $session = \Stripe\BillingPortal\Session::create([
    'customer' => $result[0]['stripe_customer_id'],
    'return_url' => getenv('URL_PATH'). '/auth/organization/organization.php?success='. urlencode("Vos informations ont été enregistées."),
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $session->url);
}