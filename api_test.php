<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si le fichier client existe
$clientFile = file_exists('mcp_client_simple.php') ? 'mcp_client_simple.php' : 'mcp_client.php';

if (!file_exists($clientFile)) {
    echo "Erreur : Le fichier client MCP est introuvable.";
    exit;
}

// Charger le client MCP
require_once $clientFile;

// Initialiser le client MCP
$mcpClient = new MCPClient();

// Action pour tester la connexion MCP
if (isset($_GET['action']) && $_GET['action'] === 'test_connection') {
    try {
        $result = $mcpClient->testConnection();
        
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Action pour envoyer un email
if (isset($_GET['action']) && $_GET['action'] === 'send_email') {
    $email = isset($_GET['email']) ? $_GET['email'] : '';
    
    if (!$email) {
        // Afficher un formulaire pour saisir l'email
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Envoyer un email via MCP</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    max-width: 600px;
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
                button, input[type="submit"] {
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
                input[type="email"] {
                    width: 100%;
                    padding: 10px;
                    margin: 8px 0;
                    box-sizing: border-box;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <h1>Envoyer un email via MCP</h1>
            
            <div class="card">
                <form action="api_test.php" method="get">
                    <input type="hidden" name="action" value="send_email">
                    <label for="email">Adresse email :</label>
                    <input type="email" id="email" name="email" required>
                    <input type="submit" value="Envoyer l'email">
                </form>
            </div>
            
            <div>
                <a href="index.php"><button style="background-color: #5bc0de;">Retour</button></a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    // Si l'email est fourni, envoyer l'email
    try {
        $result = $mcpClient->sendEmail(
            $email, 
            'Test AutoAp MCP', 
            'Ceci est un email de test envoyé depuis l\'application AutoAp via MCP Zapier.'
        );
        
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Redirection par défaut
header('Location: index.php');
exit; 