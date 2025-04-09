<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic AutoAp</h1>";

// Vérifier si le fichier mcp_client.php existe
echo "<h2>Vérification des fichiers</h2>";
if (file_exists('mcp_client.php')) {
    echo "<p style='color:green'>✓ Le fichier mcp_client.php existe</p>";
} else {
    echo "<p style='color:red'>✗ Le fichier mcp_client.php n'existe pas</p>";
}

// Lister les fichiers dans le répertoire
echo "<h2>Fichiers dans le répertoire</h2>";
echo "<pre>";
print_r(scandir('.'));
echo "</pre>";

// Afficher les permissions
echo "<h2>Permissions des fichiers</h2>";
$files = ['index.php', 'mcp_client.php', 'api_test.php'];
echo "<ul>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li>{$file}: " . substr(sprintf('%o', fileperms($file)), -4) . "</li>";
    } else {
        echo "<li>{$file}: N'existe pas</li>";
    }
}
echo "</ul>";

// Tester le chargement direct du fichier mcp_client.php
echo "<h2>Test de chargement du fichier mcp_client.php</h2>";
try {
    include_once 'mcp_client.php';
    echo "<p style='color:green'>✓ Le fichier mcp_client.php a été chargé avec succès</p>";
    
    // Vérifier si la classe MCPClient existe
    if (class_exists('MCPClient')) {
        echo "<p style='color:green'>✓ La classe MCPClient existe</p>";
    } else {
        echo "<p style='color:red'>✗ La classe MCPClient n'existe pas</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Erreur lors du chargement de mcp_client.php: " . $e->getMessage() . "</p>";
}

// Test de communication avec l'API Zapier
echo "<h2>Test de communication avec l'API Zapier</h2>";
try {
    $url = 'https://actions.zapier.com/mcp/sk-ak-LybLOg46s92oXPE8jxsNVa5RoZ/sse';
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'action' => 'test_connection',
        'timestamp' => date('c')
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        echo "<p style='color:red'>✗ Erreur cURL: {$error}</p>";
    } else {
        echo "<p style='color:green'>✓ Communication avec l'API Zapier réussie (Code HTTP: {$httpCode})</p>";
        echo "<pre>";
        echo htmlspecialchars($response);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Erreur lors du test de communication: " . $e->getMessage() . "</p>";
}

echo "<h2>Informations PHP</h2>";
echo "<p>Version PHP: " . phpversion() . "</p>";
echo "<p>Extensions PHP chargées:</p>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
?> 