<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/mail.php';
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/vendor/autoload.php';

$stripe = \Stripe\Stripe::setApiKey(getenv('STRIPE_API_KEY'));

$event = null;
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$webhook_secret = getenv('STRIPE_WEBHOOK_SECRET');

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $webhook_secret
  );
} catch(\UnexpectedValueException $e) {
  http_response_code(400);
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  http_response_code(400);
  exit();
}

// Handle the event
switch ($event->type) {
  case 'checkout.session.completed':
    $session = $event->data->object;
    writeLog('/auth/organization/backend/webhook-stripe.php', "checkout.session.completed", getUserIP(), $session->customer_email);
    break;
  case 'invoice.paid':
    $session = $event->data->object;

    $first_item = $session->lines->data[0];
    $price_id = $first_item->price->id;

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT id, first_name, last_name, organization FROM user WHERE email = :email";
    $params = [':email' => $session->customer_email];
    $stmt = $db->query($sql, $params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT id, name FROM plan WHERE stripe_product_id = :stripe_product_id";
    $params = [':stripe_product_id' => $price_id];
    $stmt = $db->query($sql, $params);
    $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    if ($result[0]['organization'] == null) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "INSERT INTO organization (name, email, owner, plan, active, stripe_customer_id) VALUES (:name, :email, :owner, :plan, :active, :stripe_customer_id)";
        $params = [
            ':name' => $result[0]['first_name'].' '.$result[0]['last_name'],
            ':email' => $session->customer_email,
            ':owner' => $result[0]['id'],
            ':plan' => $result1[0]['id'],
            ':active' => 1,
            ':stripe_customer_id' => $session->customer
        ];
        $stmt = $db->query($sql, $params);
        $db->close();

        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id FROM organization WHERE owner = :owner";
        $params = [':owner' => $result[0]['id']];
        $stmt = $db->query($sql, $params);
        $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        writeLog('/auth/organization/backend/webhook-stripe.php', "logs1", getUserIP(), $session->customer_email);

        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE user SET organization = :organization, rank = :rank WHERE id = :id";
        $params = [
            ':organization'=>$result2[0]['id'],
            ':rank' => 'administrator',
            ':id'=>$result[0]['id']
        ];
        $stmt = $db->query($sql, $params);

        $mailer = new Mailer();
    
        $toName = $result[0]['first_name'].' '.$result[0]['last_name'];
        $to = $session->customer_email;
        $subject = "Merci pour votre confiance !";
        $body = '<h2 style="color: #CEAAFF;">Merci pour votre confiance !</h2><p>Bonjour '.$toName.', merci pour votre abonnement '.$result1[0]['name'].' chez Quickbee.</p><p>Vous pouvez acceder à la configuration de votre entreprise ici :</p><a href="'.getenv("URL_PATH").'/auth/organization/organization.php" style="display: block; width: 200px; background-color: #CEAAFF; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; text-decoration: none; margin: 20px auto;">Votre entreprise</a>';
        $altBody = "Merci pour votre confiance !\n\nBonjour ".$toName.", merci pour votre abonnement ".$result1[0]['name']." chez Quickbee. Vous pouvez acceder à la configuration de votre entreprise ici :\n\n".getenv("URL_PATH")."/auth/organization/organization.php";
        
        $success = $mailer->send($to, $toName, $subject, $body, $altBody);

    }

    writeLog('/auth/organization/backend/webhook-stripe.php', "invoice.paid", getUserIP(), $session->customer_email);
    break;
  case 'invoice.payment_failed':
    $session = $event->data->object;

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT id, first_name, last_name, organization FROM user WHERE email = :email";
    $params = [':email' => $session->customer_email];
    $stmt = $db->query($sql, $params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    if ($result[0]['organization'] != null) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "UPDATE organization SET active = :active WHERE owner = :owner";
        $params = [
            ':active' => 0, 
            ':owner' => $result[0]['id'],
        ];
        $stmt = $db->query($sql, $params);
    }

    writeLog('/auth/organization/backend/webhook-stripe.php', "invoice.payment_failed", getUserIP(), $session->customer_email);
    break;

  case 'customer.subscription.updated':
    $session = $event->data->object;

    $customer_id = $session->customer;
    $items = $session->items->data;
    $price_id = 0;

    foreach ($items as $item) {
        $price_id = $item->price->id;
    }

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT id, name FROM plan WHERE stripe_product_id = :stripe_product_id";
    $params = [':stripe_product_id' => $price_id];
    $stmt = $db->query($sql, $params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    $db = new Database();
    $conn = $db->connect();
    $sql = "UPDATE organization SET active = :active, plan = :plan WHERE stripe_customer_id = :stripe_customer_id";
    $params = [
        ':active' => 1,
        ':plan' => $result[0]['id'],
        ':stripe_customer_id' => $customer_id,
    ];
    $stmt = $db->query($sql, $params);

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT email, first_name, last_name FROM user WHERE id = (SELECT owner FROM organization WHERE stripe_customer_id = :stripe_customer_id)";
    $params = [':stripe_customer_id' => $customer_id];
    $stmt = $db->query($sql, $params);
    $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    $mailer = new Mailer();

    $toName = $result1[0]['first_name'].' '.$result1[0]['last_name'];
    $to = $result1[0]['email'];
    $subject = "Merci pour votre nouvel abonnement ".$result[0]['name']." !";
    $body = '<h2 style="color: #CEAAFF;">Merci pour votre confiance !</h2><p>Bonjour '.$result1[0]['first_name'].', merci pour votre nouvel abonnement '.$result[0]['name'].' chez Quickbee.</p><p>Vous pouvez acceder à la configuration de votre entreprise ici :</p><a href="'.getenv("URL_PATH").'/auth/organization/organization.php" style="display: block; width: 200px; background-color: #CEAAFF; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; text-decoration: none; margin: 20px auto;">Votre entreprise</a>';
    $altBody = "Merci pour votre confiance !\n\nBonjour '.$result1[0]['first_name'].', merci pour votre nouvel abonnement ".$result[0]['name']." chez Quickbee. Vous pouvez acceder à la configuration de votre entreprise ici :\n\n".getenv("URL_PATH")."/auth/organization/organization.php";
    
    $success = $mailer->send($to, $toName, $subject, $body, $altBody);

    writeLog('/auth/organization/backend/webhook-stripe.php', "customer.subscription.updated", getUserIP(), $customer_id);
    break;
  case 'customer.subscription.deleted':
    $session = $event->data->object;

    $customer_id = $session->customer;

    $db = new Database();
    $conn = $db->connect();
    $sql = "UPDATE organization SET active = :active, plan = :plan WHERE stripe_customer_id = :stripe_customer_id";
    $params = [
        ':active' => 0,
        ':stripe_customer_id' => $customer_id,
    ];
    $stmt = $db->query($sql, $params);
    writeLog('/auth/organization/backend/webhook-stripe.php', "customer.subscription.deleted", getUserIP(), $customer_id);
    break;
  default:
    // Unhandled event type
    //writeLog('/auth/account/backend/webhook-stripe.php', "Unhandled event type", getUserIP());
}

http_response_code(200);