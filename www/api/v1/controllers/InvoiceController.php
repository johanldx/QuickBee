<?php

require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/www/api/v1/models/Invoice.php';
require_once ROOT_PATH . '/www/api/v1/helpers/response.php';

class InvoiceController {

    public function getAllInvoices($organisation) {
        $invoice = new Invoice();
        $data = $invoice->getAllInvoices($organisation);
        return jsonResponse(200, "Invoices fetched successfully", $data);
    }

    public function getInvoice($organisation, $id) {
        $invoice = new Invoice();
        $data = $invoice->getInvoice($organisation, $id);
        if ($data) {
            return jsonResponse(200, "Invoice fetched successfully", $data);
        } else {
            return jsonResponse(404, "Invoice not found");
        }
    }
}
