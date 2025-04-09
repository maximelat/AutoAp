<?php
// Activer l'affichage des erreurs en mode développement
// À commenter en production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier quel fichier client utiliser
$clientFile = file_exists('mcp_client_simple.php') ? 'mcp_client_simple.php' : 'mcp_client.php';

// Vérifier si le fichier existe
if (!file_exists($clientFile)) {
    echo "<div style='color:red; padding:20px; margin:20px; border:1px solid red;'>
        <h2>Erreur : Fichier manquant</h2>
        <p>Le fichier client MCP est introuvable. Veuillez vérifier votre déploiement.</p>
        <p>Voir <a href='debug.php'>la page de diagnostic</a> pour plus d'informations.</p>
    </div>";
    exit;
}

// Charger le client MCP
try {
    require_once $clientFile;
    
    // Initialiser le client MCP
    $mcpClient = new MCPClient();
    
    // Route pour tester la connexion MCP
    if (isset($_GET['action']) && $_GET['action'] === 'test_connection') {
        $result = $mcpClient->testConnection();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
} catch (Exception $e) {
    echo "<div style='color:red; padding:20px; margin:20px; border:1px solid red;'>
        <h2>Erreur d'initialisation</h2>
        <p>Une erreur est survenue lors de l'initialisation de l'application : " . $e->getMessage() . "</p>
        <p>Voir <a href='debug.php'>la page de diagnostic</a> pour plus d'informations.</p>
    </div>";
    exit;
}

// Page d'accueil
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoAp - Interface MCP</title>
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
        button, .button {
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
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <h1>AutoAp - Interface MCP</h1>
    
    <div class="card">
        <h2>API MCP Zapier</h2>
        <p>Cette application vous permet d'interagir avec l'API MCP Zapier pour effectuer diverses opérations.</p>
        <div id="status" class="status">Prêt à interagir avec MCP</div>
        <button onclick="testConnection()">Tester la connexion MCP</button>
    </div>
    
    <div class="card">
        <h2>Actions disponibles</h2>
        <ul>
            <li><a href="api_test.php?action=test_connection" class="button">Tester la connexion MCP</a></li>
            <li><a href="api_test.php?action=send_email" class="button">Envoyer un email test</a></li>
        </ul>
    </div>
    
    <div class="card">
        <h2>Diagnostic</h2>
        <p>Si vous rencontrez des problèmes, visitez la <a href="debug.php">page de diagnostic</a> pour plus d'informations.</p>
        <p><small>Utilisation du client: <?php echo basename($clientFile); ?></small></p>
    </div>

    <script>
        function testConnection() {
            fetch('index.php?action=test_connection')
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('status');
                    if (data.success) {
                        statusDiv.className = 'status success';
                        statusDiv.innerText = data.message || 'Connexion au MCP réussie !';
                    } else {
                        statusDiv.className = 'status error';
                        statusDiv.innerText = data.message || 'Erreur de connexion au MCP';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    const statusDiv = document.getElementById('status');
                    statusDiv.className = 'status error';
                    statusDiv.innerText = 'Erreur technique lors de la connexion';
                });
        }
    </script>
</body>
</html> 