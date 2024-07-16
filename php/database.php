<?php

require_once ROOT_PATH . '/php/env.php';

class Database {
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = getenv("DB_HOST");
        $this->dbName = getenv("DB_NAME");
        $this->username = getenv("DB_USER");
        $this->password = getenv("DB_PASS");
    }

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbName;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            echo "Query error: " . $e->getMessage();
        }
    }

    public function close() {
        $this->conn = null;
    }
}