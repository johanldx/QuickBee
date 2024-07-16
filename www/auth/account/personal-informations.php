<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/database.php';
require_once ROOT_PATH . '/php/env.php';
require_once ROOT_PATH . '/php/utils.php';

if (!isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
    exit;
}

writeLog('/auth/account/account.php', "Consultation du compte.", getUserIP(), $_SESSION['logged']);

?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Informations personnels - QuickBee</title>
    <link rel="stylesheet" href="/static/css/btn-custom.css">
</head>
<style>
    body {
        width: 100%;
        height: 100vh;
        font-family: "Noto Sans", sans-serif;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #E6B3FF;
    }

    .card-link {
        font-size: 12px;
    }
</style>
<body>
    <?php displayMessage() ?>
    <div class="container card text-center border-1 rounded-4 p-5" style="display: flex; margin: auto;">
        <div class="card-body p-5">
            <a href=""><img class="mb-5" id="logo-img" src="../../static/img/logo-d.png" alt="Logo QuickBee" width="150px"></a>
            <p class="card-title fs-2 mb-5 fw-bold">Visualiser mes informations personnels</p>
            
            <form id="signature-form" action="/auth/account/backend/personal-informations.php" method="post">
                <div>
                    <canvas id="signature-pad" class="border rounded"></canvas>
                </div>
                <div class="buttons">
                    <button class="btn btn-secondary my-3" type="button" id="clear">Réinitialiser la signature</button>
                </div>
                <div class="my-3">
                    <input class="form-check-input" type="checkbox" id="risk-check" name="risk-check" require>
                    <label class="form-check-label" for="risk-check">
                        Je comprends que accéder à mes données en PDF peut être risqué.
                    </label>
                </div>
                <div class="buttons">
                    <a class="btn btn-secondary mt-3" href="/auth/account/account">Retour</a>
                    <button class="btn btn-primary mt-3" type="submit" id="save">Visualiser</button>
                </div>
                <input type="hidden" name="signature" id="signature">
            </form>
        </div>
    </div>
    <script>
        const canvas = document.getElementById('signature-pad');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        function resizeCanvas() {
            const parentWidth = canvas.parentElement.clientWidth;
            canvas.width = parentWidth * 0.8;
            canvas.height = 200;
        }

        function getMousePos(canvas, evt) {
            var rect = canvas.getBoundingClientRect();
            return {
                x: evt.clientX - rect.left,
                y: evt.clientY - rect.top
            };
        }

        function getTouchPos(canvas, evt) {
            var rect = canvas.getBoundingClientRect();
            var touch = evt.touches[0];
            return {
                x: touch.clientX - rect.left,
                y: touch.clientY - rect.top
            };
        }

        function draw(event) {
            if (!drawing) return;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#303841';

            let pos;
            if (event.type.startsWith('mouse')) {
                pos = getMousePos(canvas, event);
            } else {
                pos = getTouchPos(canvas, event);
                event.preventDefault();
            }
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function startDrawing(event) {
            drawing = true;
            let pos;
            if (event.type.startsWith('mouse')) {
                pos = getMousePos(canvas, event);
            } else {
                pos = getTouchPos(canvas, event);
                event.preventDefault();
            }
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function stopDrawing() {
            drawing = false;
            ctx.beginPath();
        }

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mousemove', draw);

        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchmove', draw);

        document.getElementById('clear').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        document.getElementById('signature-form').addEventListener('submit', (event) => {
            if (!document.getElementById('risk-check').checked) {
                alert("Veuillez cocher la case pour accepter les risques.");
                event.preventDefault();
                return;
            }

            const dataURL = canvas.toDataURL('image/png');
            document.getElementById('signature').value = dataURL;
        });

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
    </script>
    <script src="../../static/js/theme.js"></script>
    <script src="../../static/js/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>