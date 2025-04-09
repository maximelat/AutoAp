<?php
// Vérifier si l'application est en cours d'exécution en vérifiant un fichier de statut
function isNodeRunning() {
    $pidFile = __DIR__ . '/node_app.pid';
    
    if (!file_exists($pidFile)) {
        return false;
    }
    
    $pid = file_get_contents($pidFile);
    $pid = intval(trim($pid));
    
    // Vérifier si le processus existe toujours
    if ($pid > 0) {
        // Sur les systèmes Unix/Linux
        if (function_exists('posix_kill')) {
            return posix_kill($pid, 0);
        } else {
            // Alternative pour les systèmes non-Unix ou sans posix_kill
            return file_exists("/proc/$pid");
        }
    }
    
    return false;
}

// Démarrer l'application Node.js si elle n'est pas en cours d'exécution
function startNodeApp() {
    if (isNodeRunning()) {
        return true;
    }
    
    $output = [];
    $logFile = __DIR__ . '/node_app.log';
    $pidFile = __DIR__ . '/node_app.pid';
    
    // Commande pour démarrer Node.js et stocker le PID
    $command = '(cd ' . __DIR__ . ' && nohup node index.js > ' . $logFile . ' 2>&1 & echo $! > ' . $pidFile . ')';
    
    exec($command, $output, $return_var);
    
    // Attendre un peu pour que le processus démarre
    sleep(2);
    
    return $return_var === 0 && isNodeRunning();
}

// Route pour démarrer l'application
if (isset($_GET['action']) && $_GET['action'] === 'start') {
    $success = startNodeApp();
    echo json_encode(['success' => $success, 'message' => $success ? 'Application démarrée' : 'Erreur au démarrage']);
    exit;
}

// Route pour vérifier le statut
if (isset($_GET['action']) && $_GET['action'] === 'check') {
    $running = isNodeRunning();
    echo json_encode(['running' => $running]);
    exit;
}

// Route pour voir les logs
if (isset($_GET['action']) && $_GET['action'] === 'logs') {
    $logFile = __DIR__ . '/node_app.log';
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        header('Content-Type: text/plain');
        echo $logs;
    } else {
        echo "Aucun fichier de log trouvé.";
    }
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
        <a href="index.php?action=logs" target="_blank"><button style="background-color: #5bc0de;">Voir les logs</button></a>
    </div>
    
    <div class="card">
        <h2>Accès à l'application</h2>
        <p>Une fois l'application démarrée, vous pouvez interagir avec l'API MCP :</p>
        <ul>
            <li><a href="api_test.php?action=test_connection" target="_blank">Tester la connexion MCP</a></li>
            <li><a href="api_test.php?action=send_email" target="_blank">Envoyer un email test</a></li>
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

        // Vérifier le statut au chargement de la page
        document.addEventListener('DOMContentLoaded', checkStatus);
    </script>
</body>
</html> 