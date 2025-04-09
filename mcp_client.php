<?php
/**
 * Client MCP (Multi-Cloud Provider) pour interagir avec l'API Zapier
 */
class MCPClient {
    /**
     * URL de l'API MCP Zapier
     * @var string
     */
    private $apiUrl;
    
    /**
     * Constructeur
     * @param string $apiUrl URL de l'API MCP (optionnel)
     */
    public function __construct($apiUrl = null) {
        $this->apiUrl = $apiUrl ?: 'https://actions.zapier.com/mcp/sk-ak-LybLOg46s92oXPE8jxsNVa5RoZ/sse';
    }
    
    /**
     * Envoie une requête à l'API MCP
     * @param string $action Action à exécuter
     * @param array $data Données à envoyer
     * @return array Réponse de l'API
     */
    public function sendRequest($action, $data = []) {
        try {
            // Préparation des données
            $payload = array_merge([
                'action' => $action,
                'timestamp' => date('c')
            ], $data);
            
            // Initialisation de cURL
            $ch = curl_init($this->apiUrl);
            
            // Configuration de cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            
            // Exécution de la requête
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            // Fermeture de cURL
            curl_close($ch);
            
            // Vérification des erreurs
            if ($error) {
                return [
                    'success' => false,
                    'error' => "Erreur cURL: $error",
                    'httpCode' => $httpCode
                ];
            }
            
            // Décodage de la réponse JSON
            $responseData = json_decode($response, true);
            
            // Vérification si la réponse est valide
            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'httpCode' => $httpCode
                ];
            } else {
                return [
                    'success' => false,
                    'error' => "Erreur HTTP: $httpCode",
                    'data' => $responseData,
                    'httpCode' => $httpCode
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => "Exception: " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Teste la connexion à l'API MCP
     * @return array Résultat du test
     */
    public function testConnection() {
        $result = $this->sendRequest('test_connection');
        
        // Ajout d'un message convivial
        if ($result['success']) {
            $result['message'] = 'Connexion au MCP réussie !';
        } else {
            $result['message'] = 'Erreur de connexion au MCP: ' . ($result['error'] ?? 'Erreur inconnue');
        }
        
        return $result;
    }
    
    /**
     * Envoie un email via l'API MCP
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $body Corps du message
     * @return array Résultat de l'envoi
     */
    public function sendEmail($to, $subject, $body) {
        $result = $this->sendRequest('send_email', [
            'to' => $to,
            'subject' => $subject,
            'body' => $body
        ]);
        
        // Ajout d'un message convivial
        if ($result['success']) {
            $result['message'] = 'Email envoyé avec succès !';
        } else {
            $result['message'] = 'Erreur lors de l\'envoi de l\'email: ' . ($result['error'] ?? 'Erreur inconnue');
        }
        
        return $result;
    }
    
    /**
     * Notifie d'un déploiement
     * @param string $status Statut du déploiement ('success', 'failure')
     * @param array $details Détails additionnels
     * @return array Résultat de la notification
     */
    public function notifyDeployment($status, $details = []) {
        return $this->sendRequest('deployment_notification', [
            'status' => $status,
            'details' => $details,
            'deployedAt' => date('c')
        ]);
    }
} 