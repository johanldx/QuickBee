<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__, 4));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';
require ROOT_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;

if (!isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signature']) && isset($_POST['risk-check'])) {
        $image = $_POST['signature'];

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        $fileName = 'signature_'. $_SESSION['logged'] .'_' . date('Y-m-d') . '.png';
        $filePath = '../../../../signatures/' . $fileName;

        if (!file_exists('../../../../signatures')) {
            mkdir('../../../../signatures', 0777, true);
        }

        file_put_contents($filePath, $imageData);
        
        $db = new Database();
        $conn = $db->connect();
        $sql = "SELECT id, email, first_name, last_name, administrator, newsletter, organization, rank, last_login, created_at FROM user WHERE id = :id";
        $params = [':id' => $_SESSION['logged']];
        $stmt = $db->query($sql, $params);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->close();

        $datelastLogin = new DateTime($user[0]['last_login']);
        $lastLogin = $datelastLogin->format('d/m/Y à H:i');

        $datecreatedAt = new DateTime($user[0]['created_at']);
        $createdAt = $datecreatedAt->format('d/m/Y à H:i');

        writeLog('/auth/account/personal-informations.php', "Consultation des informations du compte.", getUserIP(), $_SESSION['logged']);

        $dompdf = new Dompdf();

        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="shortcut icon" href="../../static/img/favicon.ico" type="image/x-icon">
            <title>Informations personnelles de '.$user[0]['first_name'].' '.$user[0]['last_name'].'</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: "Noto Sans", sans-serif;
                    margin: 0 5%;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                th, td {
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                }

                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                hr {
                    border: 0.2px solid;
                    color: black;
                }
            </style>
        </head>
        <body>
            <header>
                <img src="" alt="">
                <h1>Informations personnelles</h1>
                <p>Ce document est un récapitulatif de vos informations personnelles.</p>
            </header>

            <section>
                <h2>Votre compte</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Information</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Identifiant unique</td>
                            <td>'.$user[0]['id'].'</td>
                        </tr>
                        <tr>
                            <td>E-mail</td>
                            <td>'.$user[0]['email'].'</td>
                        </tr>
                        <tr>
                            <td>Mot de passe</td>
                            <td>*** Chiffré ***</td>
                        </tr>
                        <tr>
                            <td>Prénom</td>
                            <td>'.$user[0]['first_name'].'</td>
                        </tr>
                        <tr>
                            <td>Nom</td>
                            <td>'.$user[0]['last_name'].'</td>
                        </tr>
                        <tr>
                            <td>Administrateur</td>
                            <td>'.($user[0]['administrator'] ? 'Oui' : 'Non').'</td>
                        </tr>
                        <tr>
                            <td>Abonné à la newletter</td>
                            <td>'.($user[0]['newsletter'] ? 'Oui' : 'Non').'</td>
                        </tr>
                        <tr>
                            <td>Rang dans l\'entreprise</td>
                            <td>'.($user[0]['rank'] == 'administrator' ? 'Administrateur' : 'Utilisateur').'</td>
                        </tr>
                        <tr>
                            <td>Dernière connexion</td>
                            <td>'.$lastLogin.'</td>
                        </tr>
                        <tr>
                            <td>Compte créé le</td>
                            <td>'.$createdAt.'</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        ';

        if ($user[0]['administrator'] && $user[0]['organization'] != null) {
            $conn = $db->connect();
            $sql = "SELECT id, name, email, siren, phone, address, postal_code, city, iban, bic, stripe_customer_id, created_at FROM organization WHERE id = :id";
            $params = [':id' => $user[0]['organization']];
            $stmt = $db->query($sql, $params);
            $organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->close();

            $datecreatedAt = new DateTime($organization[0]['created_at']);
            $createdAt = $datecreatedAt->format('d/m/Y à H:i');

            $html = $html.'
            <section>
                <h2>Votre entreprise</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Information</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Identifiant unique</td>
                            <td>'.$organization[0]['id'].'</td>
                        </tr>
                        <tr>
                            <td>Nom</td>
                            <td>'.$organization[0]['name'].'</td>
                        </tr>
                        <tr>
                            <td>E-mail</td>
                            <td>'.$organization[0]['email'].'</td>
                        </tr>
                        <tr>
                            <td>SIREN</td>
                            <td>'.$organization[0]['siren'].'</td>
                        </tr>
                        <tr>
                            <td>Téléphone</td>
                            <td>'.$organization[0]['phone'].'</td>
                        </tr>
                        <tr>
                            <td>Adresse</td>
                            <td>'.$organization[0]['address'].', '.$organization[0]['postal_code'].' '.$organization[0]['city'].'</td>
                        </tr>
                        <tr>
                            <td>IBAN</td>
                            <td>'.$organization[0]['iban'].'</td>
                        </tr>
                        <tr>
                            <td>BIC</td>
                            <td>'.$organization[0]['bic'].'</td>
                        </tr>
                        <tr>
                            <td>Entreprise créé le</td>
                            <td>'.$createdAt.'</td>
                        </tr>
                        <tr>
                            <td>Identifiant unique Stripe</td>
                            <td>'.$organization[0]['stripe_customer_id'].'</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section>
                <h2>Vos documents</h2>
                <p>Votre identifiant unique peut être lié à des documents (factures et devis). Pour en savoir plus veuillez vous rapporcher de votre entreprise ou du support de Quickbee.</p>
                <p>Il ne peut pas être affiche ici pour des raisons de confidentialitées.</p>    
            </section>
            ';

        } else if ($user[0]['organization'] != null) {
            $conn = $db->connect();
            $sql = "SELECT id, name FROM organization WHERE id = :id";
            $params = [':id' => $user[0]['organization']];
            $stmt = $db->query($sql, $params);
            $organization = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db->close();

            $html = $html.'
            <section>
            <h2>Votre entreprise</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Information</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Identifiant unique</td>
                            <td>'.$organization[0]['id'].'</td>
                        </tr>
                        <tr>
                            <td>Nom</td>
                            <td>'.$organization[0]['name'].'</td>
                        </tr>
                    </tbody>
                </table>
            </section>
            
            <section>
                <h2>Vos documents</h2>
                <p>Votre identifiant unique peut être lié à des documents (factures et devis). Pour en savoir plus veuillez vous rapprocher de votre entreprise ou du support de Quickbee.</p>
            </section>
            ';
        } else {
            $html = $html.'
            <section>
                <h2>Votre entreprise</h2>
                <p>Vous n\'avez pas d\'entreprise</p>
            </section>
            ';
        }

        $dateNow = new DateTime();
        $now = $dateNow->format('d/m/Y à H:i');

        $html = $html.'
            <section>
                <hr>
                <p>Rapport généré le '.$now.'</p>
                <p>Quickbee - 2024</p>
            </section>
        </body>
        </html>
        ';

        // Charger le HTML
        $dompdf->loadHtml($html);

        // (Optionnel) Configurer la taille et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML en PDF
        $dompdf->render();

        // Afficher le PDF généré dans la fenêtre du navigateur
        $dompdf->stream("document.pdf", ["Attachment" => false]);

    } else {
        $error = 'Veuillez remplir tout les champs.';
        header('Location: ' . getenv('URL_PATH') . '/auth/account/personal-informations.php?error='.$error, true, 301);
        exit;
    }
} else {
    $error = 'Impossibe de récupérer les informations.';
    header('Location: ' . getenv('URL_PATH') . '/auth/account/personal-informations.php?error='.$error, true, 301);
    exit;
}