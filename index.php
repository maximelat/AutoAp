<?php
// Vérifier si l'application est en cours d'exécution
function isNodeRunning() {
    $url = 'http://localhost:3000/api/status';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpcode >= 200 && $httpcode < 300;
}

// Démarrer l'application Node.js si elle n'est pas en cours d'exécution
function startNodeApp() {
    $output = [];
    $command = 'cd ' . __DIR__ . ' && /usr/bin/node index.js > node_app.log 2>&1 &';
    exec($command, $output, $return_var);
    return $return_var === 0;
}

// Route pour démarrer l'application
if (isset($_GET['action']) && $_GET['action'] === 'start') {
    if (!isNodeRunning()) {
        $success = startNodeApp();
        echo json_encode(['success' => $success, 'message' => $success ? 'Application démarrée' : 'Erreur au démarrage']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Application déjà en cours d\'exécution']);
    }
    exit;
}

// Route pour vérifier le statut
if (isset($_GET['action']) && $_GET['action'] === 'check') {
    $running = isNodeRunning();
    echo json_encode(['running' => $running]);
    exit;
}

// Page d'accueil
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoAp - Gestionnaire MCP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .running {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .stopped {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <h1>AutoAp - Gestionnaire MCP</h1>
    
    <div class="card">
        <h2>Statut de l'application</h2>
        <div id="status" class="status">Vérification...</div>
        <button onclick="checkStatus()">Actualiser le statut</button>
        <button onclick="startApp()">Démarrer l'application</button>
    </div>
    
    <div class="card">
        <h2>Accès à l'application</h2>
        <p>Une fois l'application démarrée, vous pouvez y accéder :</p>
        <ul>
            <li><a href="/api/test-mcp" target="_blank">Tester la connexion MCP</a></li>
            <li><a href="/" onclick="redirectToApp(event)">Accéder à l'interface</a></li>
        </ul>
    </div>

    <script>
        function checkStatus() {
            fetch('index.php?action=check')
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('status');
                    if (data.running) {
                        statusDiv.className = 'status running';
                        statusDiv.innerText = 'Application en cours d\'exécution';
                    } else {
                        statusDiv.className = 'status stopped';
                        statusDiv.innerText = 'Application arrêtée';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    const statusDiv = document.getElementById('status');
                    statusDiv.className = 'status stopped';
                    statusDiv.innerText = 'Erreur de vérification du statut';
                });
        }

        function startApp() {
            fetch('index.php?action=start')
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    setTimeout(checkStatus, 2000);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du démarrage de l\'application');
                });
        }

        function redirectToApp(event) {
            event.preventDefault();
            checkStatus();
            setTimeout(() => {
                window.location.href = 'http://' + window.location.hostname + ':3000/';
            }, 1000);
        }

        // Vérifier le statut au chargement de la page
        document.addEventListener('DOMContentLoaded', checkStatus);
    </script>
</body>
</html> 