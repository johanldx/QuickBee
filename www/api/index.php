<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

if (!isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
    exit;
}

header('Location: ' . getenv('URL_PATH') . '/api/documentation', true, 301);

?>