<?php
/**
 * Client MCP (Multi-Cloud Provider) simplifié pour interagir avec l'API Zapier
 * Version simplifiée pour éviter les problèmes de compatibilité
 */
class MCPClient {
    // URL de l'API MCP Zapier
    private $apiUrl;
    
    /**
     * Constructeur
     */
    public function __construct($apiUrl = null) {
        $this->apiUrl = $apiUrl ? $apiUrl : 'https://actions.zapier.com/mcp/sk-ak-LybLOg46s92oXPE8jxsNVa5RoZ/sse';
    }
    
    /**
     * Envoie une requête à l'API MCP
     */
    public function sendRequest($action, $data = array()) {
        // Construction des données
        $payload = array(
            'action' => $action,
            'timestamp' => date('c')
        );
        
        // Fusionner les données supplémentaires
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $payload[$key] = $value;
            }
        }
        
        // Conversion en JSON
        $json_payload = json_encode($payload);
        
        // Construction des options cURL
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        
        // Exécution de la requête
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Construction de la réponse
        $result = array();
        
        if ($error) {
            $result['success'] = false;
            $result['error'] = "Erreur cURL: " . $error;
            $result['httpCode'] = $httpCode;
            return $result;
        }
        
        // Décodage de la réponse JSON
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $result['success'] = true;
            $result['data'] = $responseData;
            $result['httpCode'] = $httpCode;
        } else {
            $result['success'] = false;
            $result['error'] = "Erreur HTTP: " . $httpCode;
            $result['data'] = $responseData;
            $result['httpCode'] = $httpCode;
        }
        
        return $result;
    }
    
    /**
     * Teste la connexion à l'API MCP
     */
    public function testConnection() {
        $result = $this->sendRequest('test_connection');
        
        if ($result['success']) {
            $result['message'] = 'Connexion au MCP réussie !';
        } else {
            $result['message'] = 'Erreur de connexion au MCP: ' . (isset($result['error']) ? $result['error'] : 'Erreur inconnue');
        }
        
        return $result;
    }
    
    /**
     * Envoie un email via l'API MCP
     */
    public function sendEmail($to, $subject, $body) {
        $data = array(
            'to' => $to,
            'subject' => $subject,
            'body' => $body
        );
        
        $result = $this->sendRequest('send_email', $data);
        
        if ($result['success']) {
            $result['message'] = 'Email envoyé avec succès !';
        } else {
            $result['message'] = 'Erreur lors de l\'envoi de l\'email: ' . (isset($result['error']) ? $result['error'] : 'Erreur inconnue');
        }
        
        return $result;
    }
} 