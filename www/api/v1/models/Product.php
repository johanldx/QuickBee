<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';

class Product {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function getAllProducts($organization) {
        $sql = "SELECT id, name, description, tva, price_ht FROM product WHERE organization = :organization";
        $params = [':organization' => $organization];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProduct($organization, $id) {
        $sql = "SELECT id, name, description, tva, price_ht FROM product WHERE organization = :organization AND id = :id";
        $params = [
            ':organization' => $organization,
            ':id' => $id
        ];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}