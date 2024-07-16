<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

class Invoice {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllInvoices($organization) {
        $sql = "SELECT invoice.id, invoice.name, invoice.client AS client_id, CONCAT(client.first_name, ' ', client.last_name) AS client, invoice.issue_date, invoice.due_date, invoice.footer AS infos, CONCAT('".getenv('URL_PATH')."/share/?type=invoice&id=', invoice.id, '&token=', invoice.shared) AS link FROM invoice JOIN client ON invoice.client = client.id WHERE invoice.organization = :organization";
        $params = [':organization' => $organization];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoice($organization, $id) {
        $sql = "SELECT invoice.id, invoice.name, invoice.client AS client_id, CONCAT(client.first_name, ' ', client.last_name) AS client, invoice.issue_date, invoice.due_date, invoice.footer AS infos, CONCAT('".getenv('URL_PATH')."/share/?type=invoice&id=', invoice.id, '&token=', invoice.shared) AS link FROM client WHERE organization = :organization AND id = :id";
        $params = [
            ':organization' => $organization,
            ':id' => $id
        ];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}