<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

writeLog('/app/index.php', "Visité la page pricipale de l'app", getUserIP(), $_SESSION['logged']);

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    header('Location: '. getenv('URL_PATH') . '/app/invoice/invoices.php', true, 301);
} else {
    header($allowedInApp[1], true, 301);
    exit;
}