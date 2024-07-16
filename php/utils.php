<?php

require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/database.php';

function writeLog($path, $action, $ip, $user=null) {
    $logFile = ROOT_PATH . '/logs/logfile_' . date('Y-m-d') . '.log';
    $newLine = PHP_OS_FAMILY == 'Windows' ? "\r\n" : "\n";

    if ($user != null) {
        $message = date('Y-m-d H:i:s') . ' : ' . $user . ' (' . $ip . ')' . ' : ' . $path .' : ' . $action;
    } else {
        $message = date('Y-m-d H:i:s') . ' : anonymous (' . $ip . ')' . ' : ' . $path .' : ' . $action;
    }

    if (file_put_contents($logFile, $message .$newLine, FILE_APPEND | LOCK_EX) === true) {
        return true;
    } else {
        return false;
    }
}

function getUserIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'IP inconnue';

    return $ipaddress;
}

function allowedInApp() {
    if (isset($_SESSION['logged'])) {
        if (isset($_SESSION['capcha_resolved'])) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT organization FROM user WHERE id = :id";
            $params = [':id' => $_SESSION['logged']];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result && count($result) == 1) {
                if ($result[0]['organization'] != null && !isset($_SESSION['administrator'])) {
                    $_SESSION['organization'] = $result[0]['organization'];
                    return [true, $result[0]['organization']];
                } else {
                    return [false, 'Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous n'avez pas d'organisation.")];
                }
            } else {
                return [false, 'Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous n'avez pas d'organisation.")];
            }
        } else {
            return [false, 'Location: ' . getenv('URL_PATH') . '/auth/captcha.php?error='.urlencode("Veuillez prouver que vous n'êtes pas un robot.")];
        }
    } else {
        return [false, 'Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode("Veuillez vous connecter.")];
    }
}

function adminOfOrganization() {
    if (isset($_SESSION['logged'])) {
        if (isset($_SESSION['capcha_resolved'])) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT organization, rank FROM user WHERE id = :id";
            $params = [':id' => $_SESSION['logged']];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result && count($result) == 1 && !isset($_SESSION['administrator'])) {
                if ($result[0]['organization'] != null && $result[0]['rank'] == "administrator") {
                    $_SESSION['organization'] = $result[0]['organization'];
                    return [true, $result[0]['organization']];
                } else {
                    return [false, 'Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous n'êtes pas administrateur.")];
                }
            } else {
                return [false, 'Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous n'avez pas d'organisation.")];
            }
        } else {
            return [false, 'Location: ' . getenv('URL_PATH') . '/auth/captcha.php?error='.urlencode("Veuillez prouver que vous n'êtes pas un robot.")];
        }
    } else {
        return [false, 'Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode("Veuillez vous connecter.")];
    }
}

function userIsAdmin() {
    if (isset($_SESSION['logged'])) {
        if (isset($_SESSION['capcha_resolved'])) {
            $db = new Database();
            $conn = $db->connect();
            $sql = "SELECT administrator FROM user WHERE id = :id";
            $params = [':id' => $_SESSION['logged']];
            $stmt = $db->query($sql, $params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result && count($result) == 1) {
                if ($result[0]['administrator'] && isset($_SESSION['administrator'])) {
                    return [true, true];
                } else {
                    return [false, 'Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous n'êtes pas administateur.")];
                }
            } else {
                return [false, 'Location: ' . getenv('URL_PATH') . '/auth/account/account.php?error='.urlencode("Vous n'êtes pas administateur.")];
            }
        } else {
            return [false, 'Location: ' . getenv('URL_PATH') . '/auth/captcha.php?error='.urlencode("Veuillez prouver que vous n'êtes pas un robot.")];
        }
    } else {
        return [false, 'Location: ' . getenv('URL_PATH') . '/auth/login.php?error='.urlencode("Veuillez vous connecter.")];
    }
}

function has_permission() {
    if (isset($_SESSION['organization'])) {
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT plan FROM organization WHERE id = :id";
        $params = [':id' => $_SESSION['organization']];
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        if ($result[0]['plan'] == 3) {
            return [
                'new_quotation' => true,
                'new_invoice' => true,
                'new_client' => true,
                'new_product' => true,
                'new_api_key' => true,
                'new_user' => true,
            ];
        } else if ($result[0]['plan'] == 2) {
            $conn = $db->connect();
            $sql = "SELECT
                        (SELECT COUNT(*) FROM quotation WHERE organization = o.id) AS num_quotations,
                        (SELECT COUNT(*) FROM invoice WHERE organization = o.id) AS num_invoices,
                        (SELECT COUNT(*) FROM client WHERE organization = o.id) AS num_clients,
                        (SELECT COUNT(*) FROM product WHERE organization = o.id) AS num_products,
                        (SELECT COUNT(*) FROM user WHERE organization = o.id) AS num_users
                    FROM
                        organization o
                    WHERE
                        o.id = :organization;
            ";
            $params = [':organization' => $_SESSION['organization']];
            $stmt = $db->query($sql, $params);
            $count_organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->close();


            return [
                'new_quotation' => $count_organization[0]['num_quotations'] < 200 ? true : false,
                'new_invoice' => $count_organization[0]['num_invoices'] < 200 ? true : false,
                'new_client' => $count_organization[0]['num_clients'] < 20 ? true : false,
                'new_product' => $count_organization[0]['num_products'] < 25 ? true : false,
                'new_api_key' => true,
                'new_user' => $count_organization[0]['num_quotations'] < 5 ? true : false,
            ];

        } else if ($result[0]['plan'] == 1) {
            $conn = $db->connect();
            $sql = "SELECT
                        (SELECT COUNT(*) FROM quotation WHERE organization = o.id) AS num_quotations,
                        (SELECT COUNT(*) FROM invoice WHERE organization = o.id) AS num_invoices,
                        (SELECT COUNT(*) FROM client WHERE organization = o.id) AS num_clients,
                        (SELECT COUNT(*) FROM product WHERE organization = o.id) AS num_products,
                        (SELECT COUNT(*) FROM user WHERE organization = o.id) AS num_users
                    FROM
                        organization o
                    WHERE
                        o.id = :organization;
            ";
            $params = [':organization' => $_SESSION['organization']];
            $stmt = $db->query($sql, $params);
            $count_organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->close();


            return [
                'new_quotation' => $count_organization[0]['num_quotations'] < 20 ? true : false,
                'new_invoice' => $count_organization[0]['num_invoices'] < 20 ? true : false,
                'new_client' => $count_organization[0]['num_clients'] < 10 ? true : false,
                'new_product' => $count_organization[0]['num_products'] < 5 ? true : false,
                'new_api_key' => false,
                'new_user' => false,
            ];

        } else {
            return [
                'new_quotation' => false,
                'new_invoice' => false,
                'new_client' => false,
                'new_product' => false,
                'new_api_key' => false,
                'new_user' => false,
            ];
        }

    } else {
        return [
            'new_quotation' => false,
            'new_invoice' => false,
            'new_client' => false,
            'new_product' => false,
            'new_api_key' => false,
            'new_user' => false,
        ];
    }
}

function displayMessage() {
    if (isset($_GET['error'])) {
        $errorMessage = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
        echo('<div class="notif alert alert-danger fixed-top m-2" style="max-width:400px;" role="alert">' . $errorMessage . '</div>');
    }
    if (isset($_GET['success'])) {
        $errorMessage = htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8');
        echo('<div class="notif alert alert-success fixed-top m-2" style="max-width:400px;" role="alert">' . $errorMessage . '</div>');
    }
}