<?php

require_once ROOT_PATH . '/php/database.php';

function validateApiKey($token) {
    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT organization FROM apikey WHERE token = :token";
    $params = [':token' => $token];    
    $stmt = $db->query($sql, $params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    if ($result && count($result) == 1) {
        return $result[0]['organization'];
    }

    return false;
}

function jsonResponse($status, $message, $data = null) {
    header("HTTP/1.1 " . $status);
    $response['status'] = $status;
    $response['message'] = $message;
    if ($data) {
        $response['data'] = $data;
    }
    return json_encode($response);
}
