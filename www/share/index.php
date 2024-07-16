<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/php/document-pdf.php';
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $token = isset($_GET['token']) ? $_GET['token'] : null;

    if ($type == null || $id == null) {
        header('Location: ' . getenv('URL_PATH'), true, 301);
        exit;
    } else {
        if (isset($_SESSION['logged']) && $token == null) {

            writeLog('/share', "Visualisation d'un document", getUserIP(), $_SESSION['logged']);
            
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT organization FROM user WHERE id = :id";
            $params = [':id' => $_SESSION['logged']];
            $stmt = $db->query($sql, $params);
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->close();

            if ($type === 'invoice') {
                    
                $db = new Database();
                $conn = $db->connect();
                $sql = "SELECT id, shared, organization FROM invoice WHERE id = :id";
                $params = [':id' => $id];
                $stmt = $db->query($sql, $params);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $db->close();

                if ($result && $result[0]['id'] == $id && $result[0]['organization'] == $user[0]['organization']) {
                    $document = new DocumentGenerator();
                    $document->getInvoice($id);
                    exit;
                } else {
                    header('Location: ' . getenv('URL_PATH'), true, 301);
                    exit;
                }

            } else if ($type === 'quotation') {
                $db = new Database();
                $conn = $db->connect();
                $sql = "SELECT id, shared, organization FROM quotation WHERE id = :id";
                $params = [':id' => $id];
                $stmt = $db->query($sql, $params);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $db->close();

                if ($result && $result[0]['id'] == $id && $result[0]['organization'] == $user[0]['organization']) {
                    $document = new DocumentGenerator();
                    $document->getQuotation($id);
                    exit;
                } else {
                    header('Location: ' . getenv('URL_PATH'), true, 301);
                    exit;
                }
            } else {
                header('Location: ' . getenv('URL_PATH'), true, 301);
                exit;
            }

        } else {
            writeLog('/share', "Visualisation d'un document", getUserIP());

            if ($token == null) {
                header('Location: ' . getenv('URL_PATH'), true, 301);
                exit; 
            } else {
                if ($type === 'invoice') {
                    
                    $db = new Database();
                    $conn = $db->connect();
                    $sql = "SELECT id, shared FROM invoice WHERE id = :id";
                    $params = [':id' => $id];
                    $stmt = $db->query($sql, $params);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $db->close();

                    if ($result && $result[0]['shared'] != null && $result[0]['id'] == $id && $result[0]['shared'] == $token) {
                        $document = new DocumentGenerator();
                        $document->getInvoice($id);
                        exit;
                    } else {
                        header('Location: ' . getenv('URL_PATH'), true, 301);
                        exit;
                    }

                } else if ($type === 'quotation') {
                    $db = new Database();
                    $conn = $db->connect();
                    $sql = "SELECT id, shared FROM quotation WHERE id = :id";
                    $params = [':id' => $id];
                    $stmt = $db->query($sql, $params);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $db->close();

                    if ($result && $result[0]['shared'] != null && $result[0]['id'] == $id && $result[0]['shared'] == $token) {
                        $document = new DocumentGenerator();
                        $document->getQuotation($id);
                        exit;
                    } else {
                        header('Location: ' . getenv('URL_PATH'), true, 301);
                        exit;
                    }
                } else {
                    header('Location: ' . getenv('URL_PATH'), true, 301);
                    exit;
                }
            }
        }
    }
}