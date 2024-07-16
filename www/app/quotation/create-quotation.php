<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

writeLog('/app/quotation/create_devis.php', "Visité la page de création de devis de l'app", getUserIP(), $_SESSION['logged']);

$db = new Database();
$conn = $db->connect();
$sql = "SELECT id, first_name, last_name, email FROM user WHERE organization = :organization";
$params = [":organization" => $allowedInApp];
$stmt = $db->query($sql, $params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();


$db = new Database();
$conn = $db->connect();
$sql = "SELECT id, company_name FROM client WHERE organization = :organization";
$params = [":organization" => $allowedInApp];
$stmt = $db->query($sql, $params);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->close();


?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Création de devis</title>
        <meta name="description" content="Panel d'administration de QuickBee">
        <link rel="stylesheet" href="../../static/css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/static/css/btn-custom.css"> 
        <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
    </head> 

    <script>
            function getSelectedProducts() {
                const selectedProducts = [];
                const productElements = document.getElementsByName('product[]');
                
                for (let i = 0; i < productElements.length; i++) {
                    if (productElements[i].value !== "") {
                        selectedProducts.push(productElements[i].value);
                    }
                }
                return selectedProducts;
            }

            function addProductRow() {
                const table = document.getElementById('productTable');
                const lastRow = table.rows[table.rows.length - 1];
                const lastProductSelect = lastRow ? lastRow.querySelector('select[name="product[]"]') : null;
                
                if (lastProductSelect && lastProductSelect.value === "") {
                    alert("Veuillez sélectionner un produit avant d'ajouter une nouvelle ligne.");
                    return;
                }

                const selectedProducts = getSelectedProducts();

                const rowCount = table.rows.length;
                const row = table.insertRow(rowCount);

                const cell1 = row.insertCell(0);
                const element1 = document.createElement('select');
                element1.name = 'product[]';
                element1.classList.add("form-select", "mb-3");
                
                const templateOptions = document.getElementById('productTemplate').children;
                for (let i = 0; i < templateOptions.length; i++) {
                    const option = templateOptions[i].cloneNode(true);
                    if (!selectedProducts.includes(option.value)) {
                        element1.appendChild(option);
                    }
                }

                cell1.appendChild(element1);

                const cell2 = row.insertCell(1);
                const element2 = document.createElement('input');
                element2.type = 'number';
                element2.name = 'quantity[]';
                element2.classList.add("form-control", "mb-3");
                element2.setAttribute("required", "required");
                cell2.appendChild(element2);
            }
    </script>

    <body>
    <?php displayMessage() ?>
        <div class="d-flex">
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="sidebar-logo">
                        <img src="../../static/img/logo-d.png" alt="Logo" width="160px">
                    </div>
                </div>
                <ul class="sidebar-nav list-unstyled">
                    <li class="sidebar-item">
                        <a href="../invoice/invoices.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-clipboard2-check-fill"></i>
                            <span>Factures</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="quotations.php" class="sidebar-link text-decoration-none item-selected">
                            <i class="bi bi-clipboard-plus-fill"></i>
                            <span>Devis</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../client/clients.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-people-fill"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../product/products.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-cart-check-fill"></i>
                            <span>Produits</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/api/documentation/" class="sidebar-link text-decoration-none">
                            <i class="bi bi-server"></i>
                            <span>API</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../about.php" class="sidebar-link text-decoration-none">
                            <i class="bi bi-bookmark-fill"></i>
                            <span>A propos</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="/help/" class="sidebar-link text-decoration-none">
                        <i class="bi bi-chat-left-text-fill"></i>
                            <span>Support</span>
                        </a>
                    </li>
                    
                </ul>
                <div class="sidebar-footer">
                    <a href="/auth/account/account" target="_blank" class="sidebar-link text-decoration-none">
                        <img src="https://api.dicebear.com/7.x/miniavs/svg?seed=<?php echo($_SESSION['logged']); ?>" alt="avatar" width="15px"/>
                        <span style="padding-left: 15px;">Mon compte</span>
                    </a>
                    <a href="/auth/backend/logout-process" class="sidebar-link text-decoration-none">
                        <i class="bi bi-box-arrow-in-left"></i>
                        <span>Se déconnecter</span>
                    </a>
                </div>
            </aside>
            <div class="main p-3">
                <div class="d-flex justify-content-between">
                    <h1 class="fs-4 text-start">Nouveau devis</h1>
                    <div class="d-grid d-md-flex justify-content-md-end">
                        <i class="bi bi-sun"></i>
                    </div>
                </div>

                <div class="bg-light-subtle rounded p-3 mt-2">        
            
                        <div class="d-flex justify-content-start">
                            <p class="fs-5">Créer un nouveau devis</p>
                        </div>
                        <form method="post" action="../backend/create-quotation-process.php">
                        <div class="row">
                                <div class="col-md-6">
                                    <h3>Client</h3>
                                    <select class="form-select mb-3" name="client" id="client">
                                        <option value="">Sélectionner un client</option>
                                        <?php 
                                            foreach ($clients as $row) {
                                        ?>
                                        <option value="<?php echo($row['id']); ?>"><?php echo($row['company_name']); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <h3>Contact</h3>
                                    <select class="form-select mb-3" name="contact" id="contact">
                                        <option value="">Sélectionnner un contact</option>
                                        <?php 
                                            foreach ($users as $row) {
                                        ?>
                                        <option value="<?php echo($row['id']); ?>"><?php echo($row['first_name'].' '.$row['last_name']); ?> (<?php echo($row['email']); ?>)</option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <table id="productTable" class="mt-4">
                                <tr>
                                    <th class="fs-3 fw-medium">Produit</th>
                                    <th class="fs-3 fw-medium">Quantité</th>
                                </tr>
                                <tr>
                                    <td>
                                        <select class="form-select mb-3" name="product[]">
                                        <option value="">Sélectionner un produit</option>
                                            <?php
                                            $conn = $db->connect();
                                            $sql = "SELECT id, name FROM product WHERE organization = :organization";
                                            $params = [
                                                ':organization'=>$allowedInApp
                                            ];
                                            $stmt = $db->query($sql, $params);
                                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if ($result){
                                                foreach ($result as $product) {
                                                    echo "<option value='" . $product['id'] . "'>" . $product['name'] . "</option>";
                                                }
                                            }else {
                                                echo "<option value=''>Aucun produit disponible</option>";
                                            }

                                            ?>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control mb-3" name="quantity[]" required></td>
                                </tr>
                            </table>
                            <button type="button" class="btn-light btn" onclick="addProductRow()">Ajouter un produit</button>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h3>Date d'émission du devis</h3>
                                    <input type="date" class="form-control mt-2" placeholder="Date d'émission" aria-label="Date d'émission" name="issue_date">                                
                                </div>
                            </div>
                        
                       
                            <div class="d-flex justify-content-start gap-4 mt-4">
                                <a href="/app/quotation/quotations.php" class="btn btn-secondary" >Annuler</a>
                                <button type="submit" class="btn btn-primary" >Créer le devis</button>
                            </div>
                        </form>
                </div>
            </div>

            <!-- Template caché pour les produits -->
            <select class="form-select mb-3" id="productTemplate" style="display:none;">
            <option value="">Sélectionner un produit</option>
            <?php
                $conn = $db->connect();
                $sql = "SELECT id, name FROM product WHERE organization = :organization";
                $params = [
                    ':organization'=>$allowedInApp
                ];
                $stmt = $db->query($sql, $params);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result){
                    foreach ($result as $product) {
                        echo "<option value='" . $product['id'] . "'>" . $product['name'] . "</option>";
                    }
                }else {
                    echo "<option value=''>Aucun produit disponible</option>";
                }

            ?>
            </select>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>