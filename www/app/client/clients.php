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

writeLog('/app/client/clients.php', "Visité la page mes clients de l'app", getUserIP(), $_SESSION['logged']);

$db = new Database();
$conn = $db->connect();
$sql = "SELECT id, company_name, phone, address, first_name, last_name FROM client WHERE organization = :organization";
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
        <title>QuickBee - Gestion de clients</title>
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
                        <a href="../invoice/invoices.php" class="sidebar-link text-decoration-none">
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
                        <a href="clients.php" class="sidebar-link text-decoration-none item-selected">
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
                    <h1 class="fs-4 text-start">Mes clients</h1>
                </div>
                <div class="d-flex justify-content-end">
                    <?php
                    $perms = has_permission();
                    if ($perms['new_client']) {
                    ?>
                    <a href="create-client.php" class="btn btn-primary">Nouveau client</a>
                    <?php 
                    } else {
                    ?>
                    <a href="create-client.php" class="btn btn-primary disabled">Nouveau client</a>
                    <?php
                    }
                    ?>
                </div>

                <div class="bg-light-subtle rounded p-1 mt-2">        
                            <div class="d-flex justify-content-start mt-3">
                                <div>
                                    <div class="input-group">
                                        <input id="search" class="form-control me-2 rounded" type="search" placeholder="Rechercher un client" aria-label="Search">
                                    </div>
                                </div>
                            </div>
                        
                    
                        <table class="table mt-3 rounded">
                            <thead>
                                <tr>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Entreprise</th>
                                    <th>Téléphone</th>
                                    <th>Adresse</th>
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
                                    <td><?php echo(strlen($row['first_name']) > 50 ? substr($row['first_name'], 0, 50) . "..." : $row['first_name']); ?></td>
                                    <td><?php echo(strlen($row['last_name']) > 50 ? substr($row['last_name'], 0, 50) . "..." : $row['last_name']); ?></td>
                                    <td><?php echo(strlen($row['company_name']) > 50 ? substr($row['company_name'], 0, 50) . "..." : $row['company_name']); ?></td>
                                    <td><?php echo(strlen($row['phone']) > 50 ? substr($row['phone'], 0, 50) . "..." : $row['phone']); ?></td>
                                    <td><?php echo(strlen($row['address']) > 50 ? substr($row['address'], 0, 50) . "..." : $row['address']); ?></td>
                                    <td><a href="/app/client/edit-client.php?id=<?php echo($row['id']); ?>" class="btn btn-secondary">Modifier</a></td>
                                    <?php 
                                    $conn = $db->connect();
                                    $sql = "SELECT c.id, c.company_name, 
                                        CASE 
                                            WHEN i.id IS NOT NULL THEN 'Invoice'
                                            WHEN q.id IS NOT NULL THEN 'Quotation'
                                            ELSE 'None'
                                        END as record_type
                                    FROM client c
                                    LEFT JOIN invoice i ON c.id = i.client
                                    LEFT JOIN quotation q ON c.id = q.client
                                    WHERE c.id = :id";
                                    $params = [':id' => $row['id']];
                                    $stmt = $db->query($sql, $params);
                                    $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    $db->close();
                                    
                                    $allNone = true;
                                    foreach ($count as $c) {
                                        if ($c['record_type'] !== 'None') {
                                            $allNone = false;
                                            break;
                                        }
                                    }

                                    if (!$allNone) {
                                    ?>
                                    <td><button disabled class="btn btn-danger">Supprimer</a></td>
                                    <?php
                                    } else {
                                    ?>
                                    <td><a href="/app/backend/delete-client-process.php?id=<?php echo($row['id']); ?>" class="btn btn-danger">Supprimer</a></td>
                                    <?php
                                    }
                                    ?>
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
            document.getElementById('search').addEventListener('input', function() {
                const searchTerm = this.value;
                
                fetch(`<?php echo(getenv('URL_PATH')) ?>/app/backend/search-client.php?search=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            displayClients(data.clients);
                        } else {
                            console.error('Erreur:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur de requête:', error);
                    });
            });

            function displayClients(clients) {
                const tbody = document.querySelector('#tbody');
                tbody.innerHTML = ''; // Vider le tableau avant d'ajouter de nouveaux résultats

                clients.forEach(client => {
                    const row = document.createElement('tr');

                    const firstNameCell = document.createElement('td');
                    firstNameCell.textContent = client.first_name;
                    row.appendChild(firstNameCell);

                    const lastNameCell = document.createElement('td');
                    lastNameCell.textContent = client.last_name;
                    row.appendChild(lastNameCell);

                    const companyNameCell = document.createElement('td');
                    companyNameCell.textContent = client.company_name;
                    row.appendChild(companyNameCell);

                    const phoneCell = document.createElement('td');
                    phoneCell.textContent = client.phone;
                    row.appendChild(phoneCell);

                    const addressCell = document.createElement('td');
                    addressCell.textContent = client.address;
                    row.appendChild(addressCell);

                    const modifyCell = document.createElement('td');
                    modifyCell.innerHTML = '<a href="/app/client/edit-client.php?id='+client.id+'" class="btn btn-secondary">Modifier</a>';
                    row.appendChild(modifyCell);

                    const deleteCell = document.createElement('td');
                    deleteCell.innerHTML = '<a href="/app/backend/delete-client-process.php?id='+client.id+'" class="btn btn-danger">Supprimer</a>';
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