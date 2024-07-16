<?php
session_start();


define('ROOT_PATH', dirname(__DIR__, 1));
require_once ROOT_PATH . '/php/utils.php';

writeLog('/index', "Visité la page d'accueil de l'app", getUserIP());

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickBee - Votre facturation n'aura jamais été aussi simple qu'avec Quickbee !</title>
    <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap-reboot.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap-grid.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/static/css/btn-custom.css">
</head>
<style>
    #rotatingImage {
        position: fixed;
        top: -270px;
        left: 50%;
        transform-origin: center center;
        transform: translateX(-50%) rotate(0deg);
    }
</style>
<body class="" style="background-color: #2a003b;">
    <img id="rotatingImage" src="/static/img/troll.png" width="200px" alt="Oscillating Image">
    <header class="my-3 px-5 d-flex justify-content-between align-items-center">
        <button id="startButton" class="d-flex align-items-center" style="background: none; border: none;">
            <img src="/static/img/logo-l.png" width="150px" alt="Logo de Quickbee">
        </button>

        <?php
        if (isset($_SESSION['logged'])) {
        ?>
        <a href="<?php echo isset($_SESSION['administrator']) ? '/admin/' : "/app/" ?>" class="btn btn-primary rounded-pill px-4"><?php echo isset($_SESSION['administrator']) ? 'Panel admin' : "Application" ?></a>
        <?php
        } else {
            echo '<a href="/auth/login" class="btn btn-primary rounded-pill px-4">Connexion</a>';
        }
        ?>
    </header>

    <main class="my-3 px-5">
        <div class="text-center my-5">
            <h1 class="text-white my-3" style="font-weight: 700;">Votre facturation n'aura jamais été <span style="color: #fb7eff;">aussi simple</span> <br> qu'avec Quickbee ! 🥂</h1>
            <p class="text-white my-4">Quickbee réinvente la gestion de votre facturation avec une interface <br> sobre et agréable et une API facile à prendre en main.</p>
            <div class="">
                <a href="/auth/register" class="btn  btn-primary rounded-pill px-4 mx-2 mt-3">Démarrer</a>
                <a href="/contact" class="btn btn-secondary rounded-pill px-4 mx-2 lg:mt-3 mt-3">Une question ?</a>
            </div>
            <img src="/static/img/screen.png" width="80%" alt="" class="my-5 rounded" style="box-shadow: 0px 0px 50px 20px #e6b3ff2c;">
        </div>

        <div class="my-5">
            <h2 class="text-white text-center" style="font-weight: 700;">Pourquoi Quickbee ?</h2>
            <div class="d-flex d-grid gap-3 justify-content-around flex-wrap my-5">
                    <p class="text-white p-4 rounded" style="background-color: #1a0124;">Une gestion complète de votre facturation 🧾</p>
                    <p class="text-white p-4 rounded" style="background-color: #1a0124;">Une gestion complète de vos clients 🧔</p>
                    <p class="text-white p-4 rounded" style="background-color: #1a0124;">Une gestion complète de vos produits 👠</p>
                    <p class="text-white p-4 rounded" style="background-color: #1a0124;">Une API simple d'utilisation ⚙️</p>
                    <p class="text-white p-4 rounded" style="background-color: #1a0124;">Une inteface user-friendly 🤗</p>
                    <p class="text-white p-4 rounded" style="background-color: #1a0124;">Des tarifs à prix cassé 💰</p>
            </div>
        </div>

        <div class="my-5">
            <h2 class="text-white text-center" style="font-weight: 700;">Nos offres</h2>
            <div class="my-5 w-100 rounded" style="background-color: #1a0124;">
                <table class="text-white w-100 m-auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="py-4">Découverte</th>
                            <th>Team</th>
                            <th>Business</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background-color: #1f002b;">
                            <td class="px-3 py-2">Produits</td>
                            <td>5</td>
                            <td>25</td>
                            <td>Illimité</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2">Clients</td>
                            <td>10</td>
                            <td>20</td>
                            <td>Illimité</td>
                        </tr>
                        <tr style="background-color: #1f002b;">
                            <td class="px-3 py-2">Devis</td>
                            <td>20</td>
                            <td>200</td>
                            <td>Illimité</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2">Factures</td>
                            <td>20</td>
                            <td>200</td>
                            <td>Illimité</td>
                        </tr>
                        <tr style="background-color: #1f002b;">
                            <td class="px-3 py-2">API</td>
                            <td>❌</td>
                            <td>✅</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2">Comptes</td>
                            <td>1</td>
                            <td>5</td>
                            <td>Illimité</td>
                        </tr>
                        <tr style="background-color: #1f002b;">
                            <td class="px-3 py-2" style="font-weight: 700;">Tarif</td>
                            <td style="font-weight: 700;">2 €</td>
                            <td style="font-weight: 700;">74.99 €</td>
                            <td style="font-weight: 700;">149.99 €</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="my-5 text-center">
            <h2 class="text-white text-center" style="font-weight: 700;">Notre newsletter</h2>
            <p class="pt-5 pb-3 text-center text-white">Abonnez vous à notre newsletter pour recevoir des conseils pour gérer au mieux votre facturation et utiliser au mieux notre outils.</p>
            <a href="/auth/register" class="btn btn-primary rounded-pill text-center">Démarrer</a>
        </div>
    </main>

    <footer class="py-5 px-5" style="background-color: #1f002b;">
        <a href="" class="d-flex align-items-center">
            <img src="/static/img/logo-l.png" width="150px" alt="Logo de Quickbee">
        </a>

        <div class="mt-4">
            <a href="/api/documentation" class="text-white text-decoration-none">Documentation de l'API</a> <br>
            <a href="/auth/login" class="text-white text-decoration-none">Se connecter</a> <br>
            <a href="/auth/register" class="text-white text-decoration-none">S'inscrire</a> <br>
            <a href="/contact" class="text-white text-decoration-none">Nous contacter</a> <br>
            <a href="/legal-notice" class="text-white text-decoration-none">Mentions légales</a> <br>
            <a href="/auth/login-admin" class="text-white text-decoration-none">Panel admin</a>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const img = document.getElementById('rotatingImage');
            const button = document.getElementById('startButton');
            let start = null;
            const duration = 500;
            const initialRotation = 0;
            let animationFrameId;

            function animate(timestamp) {
                if (!start) start = timestamp;
                const progress = timestamp - start;
                const additionalRotation = Math.sin(progress / duration * 2 * Math.PI) * 5;
                const currentRotation = initialRotation + additionalRotation;

                const maxHeight = window.innerHeight + 400;
                const newY = maxHeight * (progress / 5000);
                img.style.transform = `translateX(-50%) translateY(${newY}px) rotate(${currentRotation}deg)`;

                if (progress < 7000) {
                    animationFrameId = requestAnimationFrame(animate);
                } else {
                    cancelAnimationFrame(animationFrameId);
                }
            }

            function fadeOutAudio(audio, duration) {
                const fadeOutTime = duration / 20;
                let fadeOutInterval = setInterval(() => {
                    if (audio.volume > 0.05) {
                        audio.volume -= 0.05;
                    } else {
                        audio.volume = 0;
                        audio.pause();
                        clearInterval(fadeOutInterval);
                    }
                }, fadeOutTime);
            }

            button.addEventListener('click', () => {
                const audio = new Audio('/static/audio/troll.mp3');
                audio.currentTime = 10;
                audio.volume = 1;

                setTimeout(3000);

                start = null;
                animationFrameId = requestAnimationFrame(animate);
                
                const confettiDuration = 3000;
                const end = Date.now() + confettiDuration;

                (function frame() {
                    confetti({
                        particleCount: 3,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 }
                    });
                    confetti({
                        particleCount: 3,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1 }
                    });

                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                }());

                audio.play();

                setTimeout(() => {
                    fadeOutAudio(audio, 2000);
                }, 3000);
            });
        });
    </script>
</body>
</html>