<?php
session_start();
 
define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (isset($_SESSION['administrator'])) {
    $userIsAdmin = userIsAdmin();

    if ($userIsAdmin[0] == false) {
        header($userIsAdmin[1], true, 301);
        exit;
    } else {
        $error = 'Vous ne pouvez pas créer de conversation.';
        header('Location: ' . getenv('URL_PATH') . '/help/admin.php?error='.urlencode($error), true, 301);
        exit;
    }
} else {
    $allowedInApp = allowedInApp();

    if ($allowedInApp[0]) {
        $allowedInApp = $allowedInApp[1];
    } else {
        header($allowedInApp[1], true, 301);
        exit;
    }
}

$db = new Database();
$conn = $db->connect();
$sql = "INSERT INTO conversation (user) VALUES (:user)";
$params = [
    ':user'=>$_SESSION['logged']
];
$stmt = $db->query($sql, $params);

$conversation_id = $conn->lastInsertId();

$success = "Un nouveau ticket est ouvert.";
header('Location: ' . getenv('URL_PATH') . '/help/index.php?id='.$conversation_id.'&success='.urlencode($success), true, 301);
exit;
