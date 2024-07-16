<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/vendor/autoload.php';

if (!isset($_SESSION['logged'])) {
    $error = "Vous n'êtes pas connecté.";
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode($error), true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT email, organization, active FROM user WHERE id = :id";
    $params = [':id' => $_SESSION['logged']];
    $stmt = $db->query($sql, $params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    if ($result[0]['organization'] != null && $result[0]['active']) {
        header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous avez déjà une entreprise."), true, 301);
        exit;
    }

    $stripe = \Stripe\Stripe::setApiKey(getenv('STRIPE_API_KEY'));

    $priceId = $_POST['priceId'];

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT name FROM plan WHERE stripe_product_id = :stripe_product_id";
    $params = [':stripe_product_id' => $priceId];
    $stmt = $db->query($sql, $params);
    $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    if (!$result || count($result) != 1) {
        header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
        exit;
    }

    $session = \Stripe\Checkout\Session::create([
    'success_url' => getenv('URL_PATH'). '/auth/account/account.php' ."?success=". urlencode("Votre entreprise est en cours de création."),
    'cancel_url' => getenv('URL_PATH'). '/auth/account/account.php?error='. urlencode("Le payement a été annulé."),
    'mode' => 'subscription',
    'line_items' => [[
        'price' => $priceId,
        'quantity' => 1,
    ]],
    'customer_email' => $result[0]['email'],
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $session->url);
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Impossible de récupérer les informations."), true, 301);
    exit;
}