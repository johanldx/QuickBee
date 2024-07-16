<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/www/api/v1/models/Quotation.php';
require_once ROOT_PATH . '/www/api/v1/helpers/response.php';

class QuotationController {

    public function getAllQuotations($organisation) {
        $quotation = new Quotation();
        $data = $quotation->getAllQuotations($organisation);
        return jsonResponse(200, "Invoices fetched successfully", $data);
    }

    public function getQuotation($organisation, $id) {
        $quotation = new Quotation();
        $data = $quotation->getQuotation($organisation, $id);
        if ($data) {
            return jsonResponse(200, "Invoice fetched successfully", $data);
        } else {
            return jsonResponse(404, "Invoice not found");
        }
    }
}