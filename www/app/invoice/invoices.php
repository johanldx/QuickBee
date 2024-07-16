<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';

$allowedInApp = allowedInApp();

if ($allowedInApp[0]) {
    $allowedInApp = $allowedInApp[1];
} else {
    header($allowedInApp[1], true, 301);
    exit;
}

writeLog('/app/invoice/invoices.php', "Visité la page de facture de l'app", getUserIP(), $_SESSION['logged']);


$db = new Database();
$conn = $db->connect();
$sql = "SELECT invoice.shared, invoice.id, client.company_name AS client, invoice.name, invoice.issue_date FROM invoice JOIN client ON client.id = invoice.client WHERE invoice.organization = :organization ORDER BY invoice.id DESC";
$params = [':organization' => $allowedInApp];
$stmt = $db->query($sql, $params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$db->close();

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QuickBee - Gestion de factures</title>
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
                        <a href="invoices.php" class="sidebar-link text-decoration-none item-selected">
                            <i class="bi bi-clipboard2-check-fill"></i>
                            <span>Factures</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="../quotation/quotations.php" class="sidebar-link text-decoration-none">
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
                <div class="d-flex justify-content-start">
                    <h1 class="fs-4 text-start">Mes factures</h1>
                </div>
                <div class="d-flex justify-content-end">
                    <?php
                    $perms = has_permission();
                    if ($perms['new_invoice']) {
                    ?>
                    <a href="create-invoice.php" type="button" class="btn btn-primary">Nouvelle facture</a>
                    <?php 
                    } else {
                    ?>
                    <a href="create-invoice.php" type="button" class="btn btn-primary disabled">Nouvelle facture</a>
                    <?php
                    }
                    ?>
                </div>

                <div class="bg-light-subtle rounded p-1 mt-2">        
                            <div class="d-flex justify-content-start mt-3">
                                    <div>
                                        <div class="input-group gap-2">
                                            <input id="search" class="form-control me-2 rounded" type="search" placeholder="Rechercher une facture" aria-label="Search">
                                        </div>
                                    </div>
                            </div>
                        

                    <table class="table mt-3 rounded">
                        <thead>
                            <tr>
                                <th>Clients</th>
                                <th>Date</th>
                                <th>N° de facture</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <?php

                            if ($result && count($result) >= 1) {
                                foreach ($result as $row) {
                            ?>   
                            <tr>
                                <td><?php echo(strlen($row['client']) > 50 ? substr($row['client'], 0, 50) . "..." : $row['client']); ?></td>
                                <td><?php $date = new DateTime($row['issue_date']); echo($date->format('d/m/Y')); ?></td>
                                <td><?php echo(strlen($row['name']) > 50 ? substr($row['name'], 0, 50) . "..." : $row['name']); ?></td>
                                <td><a href="/app/invoice/invoice.php?id=<?php echo($row['id']); ?>" class="btn btn-secondary">Visualiser</a></td>
                                <td><a href="/share?type=invoice&id=<?php echo($row['id']); ?>&token=<?php echo($row['shared']); ?>" class="btn btn-primary">Exporter</a></td>
                            </tr>

                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        
        <script>

            function transformDate(dateTimeStr) {
                
                const datePart = dateTimeStr.split(' ')[0];
               
                const [year, month, day] = datePart.split('-');
            
                return `${day}/${month}/${year}`;
            }
            document.getElementById('search').addEventListener('input', function() {
                const searchTerm = this.value;
                
                fetch(`<?php echo(getenv('URL_PATH')) ?>/app/backend/search-invoice.php?search=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            displayInvoices(data.invoices);
                        } else {
                            console.error('Erreur:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur de requête:', error);
                    });
            });

            function displayInvoices(invoices) {
                const tbody = document.querySelector('#tbody');
                tbody.innerHTML = ''; // Vider le tableau avant d'ajouter de nouveaux résultats

                invoices.forEach(invoice => {
                    const row = document.createElement('tr');

                    const companyNameCell = document.createElement('td');
                    companyNameCell.textContent = invoice.company_name;
                    row.appendChild(companyNameCell);

                    const issue_dateCell = document.createElement('td');
                    issue_dateCell.textContent = transformDate(invoice.issue_date);
                    row.appendChild(issue_dateCell);

                    const nameCell = document.createElement('td');
                    nameCell.textContent = invoice.name;
                    row.appendChild(nameCell);

                    const viewCell = document.createElement('td');
                    viewCell.innerHTML = '<a href="/app/invoice/invoice.php?id='+invoice.id+'" class="btn btn-secondary">Visualiser</a>';
                    row.appendChild(viewCell);

                    const deleteCell = document.createElement('td');
                    deleteCell.innerHTML = '<a href="/share?type=invoice&id='+ invoice.id +'&token=' + invoice.shared+'" class="btn btn-primary">Exporter</a>';
                    row.appendChild(deleteCell);

                    tbody.appendChild(row);
                });
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../../static/js/script.js"></script>
        <script src="../../static/js/notify.js"></script>
</body>
</html>