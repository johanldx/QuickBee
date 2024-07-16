<?php
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require ROOT_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;

class DocumentGenerator {
    private $dompdf;
    private $db;

    public function __construct() {
        $this->dompdf = new Dompdf();
        $this->db = new Database();
    }

    public function generateQuotation($id) {
        $conn = $this->db->connect();
        $sql = "SELECT quotation.name, quotation.organization, user.first_name, user.last_name, quotation.client, quotation.issue_date, quotation.footer FROM quotation JOIN user ON quotation.contact = user.id WHERE quotation.id = :id";
        $params = [':id' => $id];
        $stmt = $this->db->query($sql, $params);
        $quotation_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $issue_date = new DateTime($quotation_infos[0]['issue_date']);
        $formatted_issue_date = $issue_date->format('d/m/Y');

        $conn = $this->db->connect();
        $sql = "SELECT company_name, first_name, last_name, email, phone, siren, address, postal_code, city, country FROM client WHERE id = :id";
        $params = [':id' => $quotation_infos[0]['client']];
        $stmt = $this->db->query($sql, $params);
        $client_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $conn = $this->db->connect();
        $sql = "SELECT name, email, siren, phone, address, postal_code, city, country, iban, bic FROM organization WHERE id = :id";
        $params = [':id' => $quotation_infos[0]['organization']];
        $stmt = $this->db->query($sql, $params);
        $organization_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $conn = $this->db->connect();
        $sql = "SELECT product.name, product.tva, product.description, product.price_ht, product.price_ht*quotationline.quantity AS total_ht, quotationline.quantity FROM quotationline JOIN product ON quotationline.product = product.id WHERE quotationline.quotation = :id";
        $params = [':id' => $id];
        $stmt = $this->db->query($sql, $params);
        $products_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $details_rows = '';
        $total_ttc = 0;
        foreach ($products_infos as $product) {
            $details_rows .= '
            <tr>
                <td>'.$product['name'].'</td>
                <td>'.$product['description'].'</td>
                <td>'.$product['price_ht'].' €</td>
                <td>'.$product['quantity'].'</td>
                <td>'.$product['total_ht'].' €</td>
            </tr>
            ';

            $total_ttc += $product['total_ht'] + ($product['total_ht']*$product['tva']);
        }

        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devis '.$quotation_infos[0]['name'].'</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: "Noto Sans", Arial, Helvetica, sans-serif;
                    margin: 0;
                }

                body * {
                    margin: 0;
                    padding: 0;
                }

                .bold {
                    font-weight: bold;
                }

                h2 {
                    font-weight: bold;
                    padding-bottom: 25px;
                }

                header {
                    margin: 0 0 5% 0;
                }

                header p {
                    font-size: 1.5em;
                    margin: 10px 0 0;
                }

                .informations td:first-child {
                    color: #858585;
                }

                .detail {
                    margin: 0 0 2% 0;
                }

                .detail table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                .detail th, .detail td {
                    border-bottom: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                }

                .detail th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }

                .total {
                    margin: 0 0 5% 0;
                    text-align: right;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>Devis '.$quotation_infos[0]['name'].'</h1>
                <p style="color: #858585;";>'.$formatted_issue_date.'</p>
            </header>
            <br><br>

            <table width="100%">
                <tr>
                    <td width="50%" valign="top">
                        <h2>Émetteur</h2>
                        <br>
                        <table class="informations" style="margin-bottom: 20px; border-collapse: separate;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="color: #858585;">Société</td>
                                    <td style="display: block; margin-left: 20px;"><span class="bold">'.$organization_infos[0]['name'].'</span></td>
                                </tr>
                                <tr>
                                    <td>Votre contact</td>
                                    <td style="display: block; margin-left: 20px;">'.$quotation_infos[0]['first_name'].' '.$quotation_infos[0]['last_name'].'</td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['address'].', '.$organization_infos[0]['postal_code'].' '.$organization_infos[0]['city'].'</td>
                                </tr>
                                <tr>
                                    <td>Pays</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['country'].'</td>
                                </tr>
                                <tr>
                                    <td>Numéro d\'entreprise</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['siren'].'</td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['email'].'</td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['phone'].'</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="50%" valign="top">
                        <h2>Destinataire</h2>
                        <br>
                        <table class="informations" style="margin-bottom: 20px; border-collapse: separate;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Société</td>
                                    <td style="display: block; margin-left: 20px;"><span class="bold">'.$client_infos[0]['company_name'].'</span></td>
                                </tr>
                                <tr>
                                    <td>Contact</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['first_name'].' '.$client_infos[0]['last_name'].'</td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['address'].', '.$client_infos[0]['postal_code'].' '.$client_infos[0]['city'].'</td>
                                </tr>
                                <tr>
                                    <td>Pays</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['country'].'</td>
                                </tr>
                                <tr>
                                    <td>Numéro d\'entreprise</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['siren'].'</td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['email'].'</td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['phone'].'</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            
            <br><br><br>

            <section class="detail">
                <h2>Détail</h2>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix unitaire HT</th>
                            <th>Quantité</th>
                            <th>Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$details_rows.'
                    </tbody>
                </table>
            </section>

            <section class="total">
                <br>
                Total TTC : <span class="bold">'.$total_ttc.' €</span>
            </section>

            <table width="100%">
                <tr>
                    <td width="60%" valign="top">
                        <div>
                            <h2>Informations</h2>
                            <br>
                            <p><span class="bold">Valable jusqu\'au : </span>'.$formatted_issue_date.'</p>
                            <p><span class="bold">Mode de règlement : </span>Virement bancaire</p>
                            <p><span class="bold">Intérêts de retard : </span>Taux d’intérêt légal en vigueur</p>
                            <p><span class="bold">Informations complémentaires : </span> '.$quotation_infos[0]['footer'].'</p>
                        </div>
                    </td>
                    <td width="40%" valign="top">
                        <div>
                            <h2>RIB</h2>
                            <br>
                            <p><span class="bold">IBAN :</span> '.$organization_infos[0]['iban'].'</p>
                            <p><span class="bold">BIC :</span> '.$organization_infos[0]['bic'].'</p>
                            <p><span class="bold">Titulaire :</span> '.$organization_infos[0]['name'].'</p>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';
     
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        $pdfContent = $this->dompdf->output();
        
        $encryptionKey = getenv("ENCRYPTION_KEY");
        $encryptedPdfContent = openssl_encrypt($pdfContent, 'aes-256-cbc', $encryptionKey, 0, substr($encryptionKey, 0, 16));

        $outputDir = ROOT_PATH . '/pdfs/quotations/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        $outputFile = $outputDir . $quotation_infos[0]['name'] . '.pdf';

        file_put_contents($outputFile, $encryptedPdfContent);
    }

    public function generateInvoice($id) {
        $conn = $this->db->connect();
        $sql = "SELECT invoice.name, invoice.organization, user.first_name, user.last_name, invoice.client, invoice.issue_date, invoice.due_date, invoice.footer FROM invoice JOIN user ON invoice.contact = user.id WHERE invoice.id = :id";
        $params = [':id' => $id];
        $stmt = $this->db->query($sql, $params);
        $invoice_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $due_date = new DateTime($invoice_infos[0]['due_date']);
        $formatted_due_date = $due_date->format('d/m/Y');

        $issue_date = new DateTime($invoice_infos[0]['issue_date']);
        $formatted_issue_date = $issue_date->format('d/m/Y');

        $conn = $this->db->connect();
        $sql = "SELECT company_name, first_name, last_name, email, phone, siren, address, postal_code, city, country FROM client WHERE id = :id";
        $params = [':id' => $invoice_infos[0]['client']];
        $stmt = $this->db->query($sql, $params);
        $client_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $conn = $this->db->connect();
        $sql = "SELECT name, email, siren, phone, address, postal_code, city, country, iban, bic FROM organization WHERE id = :id";
        $params = [':id' => $invoice_infos[0]['organization']];
        $stmt = $this->db->query($sql, $params);
        $organization_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $conn = $this->db->connect();
        $sql = "SELECT product.name, product.tva, product.description, product.price_ht, product.price_ht*invoiceline.quantity AS total_ht, invoiceline.quantity FROM invoiceline JOIN product ON invoiceline.product = product.id WHERE invoiceline.invoice = :id";
        $params = [':id' => $id];
        $stmt = $this->db->query($sql, $params);
        $products_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $details_rows = '';
        $total_ttc = 0;
        foreach ($products_infos as $product) {
            $details_rows .= '
            <tr>
                <td>'.$product['name'].'</td>
                <td>'.$product['description'].'</td>
                <td>'.$product['price_ht'].' €</td>
                <td>'.$product['quantity'].'</td>
                <td>'.$product['total_ht'].' €</td>
            </tr>
            ';

            $total_ttc += $product['total_ht'] + ($product['total_ht']*$product['tva']);
        }

        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Facture '.$invoice_infos[0]['name'].'</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: "Noto Sans", Arial, Helvetica, sans-serif;
                    margin: 0;
                }

                body * {
                    margin: 0;
                    padding: 0;
                }

                .bold {
                    font-weight: bold;
                }

                h2 {
                    font-weight: bold;
                    padding-bottom: 25px;
                }

                header {
                    margin: 0 0 5% 0;
                }

                header p {
                    font-size: 1.5em;
                    margin: 10px 0 0;
                }

                .informations td:first-child {
                    color: #858585;
                }

                .detail {
                    margin: 0 0 2% 0;
                }

                .detail table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                .detail th, .detail td {
                    border-bottom: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                }

                .detail th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }

                .total {
                    margin: 0 0 5% 0;
                    text-align: right;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>Facture '.$invoice_infos[0]['name'].'</h1>
                <p style="color: #858585;";>'.$formatted_issue_date.'</p>
            </header>
            <br><br>

            <table width="100%">
                <tr>
                    <td width="50%" valign="top">
                        <h2>Émetteur</h2>
                        <br>
                        <table class="informations" style="margin-bottom: 20px; border-collapse: separate;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="color: #858585;">Société</td>
                                    <td style="display: block; margin-left: 20px;"><span class="bold">'.$organization_infos[0]['name'].'</span></td>
                                </tr>
                                <tr>
                                    <td>Votre contact</td>
                                    <td style="display: block; margin-left: 20px;">'.$invoice_infos[0]['first_name'].' '.$invoice_infos[0]['last_name'].'</td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['address'].', '.$organization_infos[0]['postal_code'].' '.$organization_infos[0]['city'].'</td>
                                </tr>
                                <tr>
                                    <td>Pays</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['country'].'</td>
                                </tr>
                                <tr>
                                    <td>Numéro d\'entreprise</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['siren'].'</td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['email'].'</td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td style="display: block; margin-left: 20px;">'.$organization_infos[0]['phone'].'</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="50%" valign="top">
                        <h2>Destinataire</h2>
                        <br>
                        <table class="informations" style="margin-bottom: 20px; border-collapse: separate;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Société</td>
                                    <td style="display: block; margin-left: 20px;"><span class="bold">'.$client_infos[0]['company_name'].'</span></td>
                                </tr>
                                <tr>
                                    <td>Contact</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['first_name'].' '.$client_infos[0]['last_name'].'</td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['address'].', '.$client_infos[0]['postal_code'].' '.$client_infos[0]['city'].'</td>
                                </tr>
                                <tr>
                                    <td>Pays</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['country'].'</td>
                                </tr>
                                <tr>
                                    <td>Numéro d\'entreprise</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['siren'].'</td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['email'].'</td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td style="display: block; margin-left: 20px;">'.$client_infos[0]['phone'].'</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            
            <br><br><br>

            <section class="detail">
                <h2>Détail</h2>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix unitaire HT</th>
                            <th>Quantité</th>
                            <th>Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$details_rows.'
                    </tbody>
                </table>
            </section>

            <section class="total">
                <br>
                Total TTC : <span class="bold">'.$total_ttc.' €</span>
            </section>

            <table width="100%">
                <tr>
                    <td width="60%" valign="top">
                        <div>
                            <h2>Informations</h2>
                            <br>
                            <p><span class="bold">Conditions de règlement : </span>avant le '.$formatted_due_date.'</p>
                            <p><span class="bold">Mode de règlement : </span>Virement bancaire</p>
                            <p><span class="bold">Intérêts de retard : </span>Taux d’intérêt légal en vigueur</p>
                            <p><span class="bold">Informations complémentaires : </span> '.$invoice_infos[0]['footer'].'</p>
                        </div>
                    </td>
                    <td width="40%" valign="top">
                        <div>
                            <h2>RIB</h2>
                            <br>
                            <p><span class="bold">IBAN :</span> '.$organization_infos[0]['iban'].'</p>
                            <p><span class="bold">BIC :</span> '.$organization_infos[0]['bic'].'</p>
                            <p><span class="bold">Titulaire :</span> '.$organization_infos[0]['name'].'</p>
                        </div>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';
     
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        $pdfContent = $this->dompdf->output();
        
        $encryptionKey = getenv("ENCRYPTION_KEY");
        $encryptedPdfContent = openssl_encrypt($pdfContent, 'aes-256-cbc', $encryptionKey, 0, substr($encryptionKey, 0, 16));

        $outputDir = ROOT_PATH . '/pdfs/invoices/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        $outputFile = $outputDir . $invoice_infos[0]['name'] . '.pdf';

        file_put_contents($outputFile, $encryptedPdfContent);
    }

    public function getQuotation($id) {
        $conn = $this->db->connect();
        $sql = "SELECT quotation.name FROM quotation WHERE quotation.id = :id";
        $params = [':id' => $id];
        $stmt = $this->db->query($sql, $params);
        $quotation_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $encryptedFile = ROOT_PATH . '/pdfs/quotations/' . $quotation_infos[0]['name'] . '.pdf';
        $encryptedPdfContent = file_get_contents($encryptedFile);

        $encryptionKey = getenv("ENCRYPTION_KEY");
        $decryptedPdfContent = openssl_decrypt($encryptedPdfContent, 'aes-256-cbc', $encryptionKey, 0, substr($encryptionKey, 0, 16));

        if ($decryptedPdfContent === false) {
            die('Erreur de déchiffrement du PDF.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="decrypted_document.pdf"');
        echo $decryptedPdfContent;
    }

    public function getInvoice($id) {
        $conn = $this->db->connect();
        $sql = "SELECT invoice.name FROM invoice WHERE invoice.id = :id";
        $params = [':id' => $id];
        $stmt = $this->db->query($sql, $params);
        $invoice_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->close();

        $encryptedFile = ROOT_PATH . '/pdfs/invoices/' . $invoice_infos[0]['name'] . '.pdf';
        $encryptedPdfContent = file_get_contents($encryptedFile);

        $encryptionKey = getenv("ENCRYPTION_KEY");
        $decryptedPdfContent = openssl_decrypt($encryptedPdfContent, 'aes-256-cbc', $encryptionKey, 0, substr($encryptionKey, 0, 16));

        if ($decryptedPdfContent === false) {
            die('Erreur de déchiffrement du PDF.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="decrypted_document.pdf"');
        echo $decryptedPdfContent;
    }
}