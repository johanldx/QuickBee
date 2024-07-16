<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

class Quotation {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllQuotations($organization) {
        $sql = "SELECT quotation.id, quotation.name, quotation.client AS client_id, CONCAT(client.first_name, ' ', client.last_name) AS client, quotation.issue_date, quotation.footer AS infos, CONCAT('".getenv('URL_PATH')."/share/?type=quotation&id=', quotation.id, '&token=', quotation.shared) AS link FROM quotation JOIN client ON quotation.client = client.id WHERE quotation.organization = :organization";
        $params = [':organization' => $organization];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuotation($organization, $id) {
        $sql = "SELECT quotation.id, quotation.name, quotation.client AS client_id, CONCAT(client.first_name, ' ', client.last_name) AS client, quotation.issue_date, quotation.footer AS infos, CONCAT('".getenv('URL_PATH')."/share/?type=quotation&id=', quotation.id, '&token=', quotation.shared) AS link FROM quotation JOIN client ON quotation.client = client.id WHERE quotation.organization = :organization AND id = :id";
        $params = [
            ':organization' => $organization,
            ':id' => $id
        ];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}