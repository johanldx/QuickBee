<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/www/api/v1/models/Client.php';
require_once ROOT_PATH . '/www/api/v1/helpers/response.php';

class ClientController {

    public function getAllClients($organisation) {
        $client = new Client();
        $data = $client->getAllClients($organisation);
        return jsonResponse(200, "Clients fetched successfully", $data);
    }

    public function getClient($organisation, $id) {
        $client = new Client();
        $data = $client->getClient($organisation, $id);
        if ($data) {
            return jsonResponse(200, "Client fetched successfully", $data);
        } else {
            return jsonResponse(404, "Client not found");
        }
    }
}
