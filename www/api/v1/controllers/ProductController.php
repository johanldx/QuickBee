<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/www/api/v1/models/Product.php';
require_once ROOT_PATH . '/www/api/v1/helpers/response.php';

class ProductController {

    public function getAllProducts($organisation) {
        $product = new Product();
        $data = $product->getAllProducts($organisation);
        return jsonResponse(200, "Products fetched successfully", $data);
    }

    public function getProduct($organisation, $id) {
        $product = new Product();
        $data = $product->getProduct($organisation, $id);
        if ($data) {
            return jsonResponse(200, "Product fetched successfully", $data);
        } else {
            return jsonResponse(404, "Product not found");
        }
    }
}
