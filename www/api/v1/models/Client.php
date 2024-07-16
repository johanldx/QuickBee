<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';

class Client {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllClients($organization) {
        $sql = "SELECT id, company_name, first_name, last_name, email, phone, siren, iban, address, postal_code, city, country FROM client WHERE organization = :organization";
        $params = [':organization' => $organization];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClient($organization, $id) {
        $sql = "SELECT id, company_name, first_name, last_name, email, phone, siren, iban, address, postal_code, city, country FROM client WHERE organization = :organization AND id = :id";
        $params = [
            ':organization' => $organization,
            ':id' => $id
        ];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}