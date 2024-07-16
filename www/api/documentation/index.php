<?php
session_start();

define('ROOT_PATH', dirname(__DIR__, 3));
require_once ROOT_PATH . '/php/utils.php';
require_once ROOT_PATH . '/php/env.php';

if (!isset($_SESSION['logged'])) {
    header('Location: ' . getenv('URL_PATH') . '/auth/login.php', true, 301);
    exit;
}

writeLog('/api/docs', "Visité la docs de l'API", getUserIP(), $_SESSION['logged']);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="/static/img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.19.5/swagger-ui.css">
  <style>
      .topbar {
        display: none;
      }
  </style>
  <title>Quickbee API Documentation</title>
</head>
<body>
  <div id="swagger-ui"></div>

  <script>
    window.PATH = '<?php echo getenv('URL_PATH') ; ?>/api/v1/';
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.19.5/swagger-ui-bundle.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/3.19.5/swagger-ui-standalone-preset.js"></script>
  <script src="/static/js/api.js"></script>
  <script>
    window.onload = function() {
      const ui = SwaggerUIBundle({
        spec: spec,
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        plugins: [
          SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout",
      });
      window.ui = ui;
    }
  </script>
</body>
</html>