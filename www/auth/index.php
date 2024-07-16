<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/env.php';

if (isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/account/', true, 301);
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
}