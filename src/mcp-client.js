const axios = require('axios');

class MCPClient {
  constructor(apiUrl) {
    this.apiUrl = apiUrl || 'https://actions.zapier.com/mcp/sk-ak-LybLOg46s92oXPE8jxsNVa5RoZ/sse';
  }

  /**
   * Envoie une requête au serveur MCP
   * @param {string} action - L'action à exécuter
   * @param {object} data - Les données à envoyer
   * @returns {Promise<object>} - La réponse du serveur
   */
  async sendRequest(action, data = {}) {
    try {
      const payload = {
        action,
        ...data,
        timestamp: new Date().toISOString()
      };

      const response = await axios.post(this.apiUrl, payload);
      return {
        success: true,
        data: response.data
      };
    } catch (error) {
      console.error(`Erreur MCP (${action}):`, error);
      return {
        success: false,
        error: error.message
      };
    }
  }

  /**
   * Teste la connexion au serveur MCP
   */
  async testConnection() {
    return this.sendRequest('test_connection');
  }

  /**
   * Envoie un email via le service MCP
   * @param {string} to - Destinataire
   * @param {string} subject - Sujet
   * @param {string} body - Corps du message
   */
  async sendEmail(to, subject, body) {
    return this.sendRequest('send_email', { to, subject, body });
  }

  /**
   * Notifie le serveur MCP d'un déploiement
   * @param {string} status - Statut du déploiement ('success', 'failure')
   * @param {object} details - Détails additionnels
   */
  async notifyDeployment(status, details = {}) {
    return this.sendRequest('deployment_notification', { 
      status, 
      details,
      deployedAt: new Date().toISOString()
    });
  }
}

module.exports = MCPClient; 