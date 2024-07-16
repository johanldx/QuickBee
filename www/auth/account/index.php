<?php
session_start();

if (isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/account/account.php', true, 301);
} else {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
}