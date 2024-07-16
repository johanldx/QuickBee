<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once 'helpers/response.php';

header("Content-Type: application/json");

spl_autoload_register(function ($class_name) {

    if (file_exists('controllers/' . $class_name . '.php')) {
        require_once 'controllers/' . $class_name . '.php';
    }
    if (file_exists('models/' . $class_name . '.php')) {
        require_once 'models/' . $class_name . '.php';
    }
});

// Récupération de la route et de la méthode HTTP
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$method = $_SERVER['REQUEST_METHOD'];

// Récupération de la clé API
$apiKey = $_SERVER['HTTP_API_KEY'] ?? '';

// Vérification de la clé API
$organization = validateApiKey($apiKey);

if ($organization == false) {
    echo jsonResponse(403, "Invalid API Key");

    if (isset($uri[4]) && $uri[4] != '') {
        writeLog('/api/v1/'.$uri[3].'/'.$uri[4], "Tentative d'utilisation de l'API", getUserIP());
    } else {
        writeLog('/api/v1/'.$uri[3], "Tentative d'utilisation de l'API", getUserIP());
    }

    exit();
}

if (isset($uri[4]) && $uri[4] != '') {
    writeLog('/api/v1/'.$uri[3].'/'.$uri[4], "Utilisation de l'API", getUserIP(), $organization.' (organization)');
} else {
    writeLog('/api/v1/'.$uri[3], "Utilisation de l'API", getUserIP(), $organization.' (organization)');
}

// Routage basique
try {
    if ($uri[3] == 'product' && $method == 'GET') {
        $controller = new ProductController();
        if (isset($uri[4]) && $uri[4] != '') {
            echo $controller->getProduct($organization, $uri[4]);
        } else {
            echo $controller->getAllProducts($organization);
        }
    } else if ($uri[3] == 'client' && $method == 'GET') {
        $controller = new ClientController();
        if (isset($uri[4]) && $uri[4] != '') {
            echo $controller->getClient($organization, $uri[4]);
        } else {
            echo $controller->getAllClients($organization);
        }
    } else if ($uri[3] == 'invoice' && $method == 'GET') {
        $controller = new InvoiceController();
        if (isset($uri[4]) && $uri[4] != '') {
            echo $controller->getInvoice($organization, $uri[4]);
        } else {
            echo $controller->getAllInvoices($organization);
        }
    } else if ($uri[3] == 'quotation' && $method == 'GET') {
        $controller = new QuotationController();
        if (isset($uri[4]) && $uri[4] != '') {
            echo $controller->getQuotation($organization, $uri[4]);
        } else {
            echo $controller->getAllQuotations($organization);
        }
    } else {
        echo jsonResponse(404, "Route not found");
    }
} catch (Exception $e) {
    echo jsonResponse(500, "Internal Server Error: " . $e->getMessage());
}