const express = require('express');
const MCPClient = require('./mcp-client');

const app = express();
const port = process.env.PORT || 3000;
const mcpClient = new MCPClient();

// Middleware pour parser le JSON
app.use(express.json());

// Page d'accueil
app.get('/', (req, res) => {
  res.send(`
    <h1>AutoAp - Application MCP</h1>
    <p>Cette application est connectée à votre serveur MCP Zapier.</p>
    <div>
      <button onclick="testConnection()">Tester la connexion MCP</button>
      <button onclick="sendEmail()">Envoyer un email test</button>
    </div>
    
    <script>
      async function testConnection() {
        const response = await fetch('/api/test-mcp', {method: 'POST'});
        const data = await response.json();
        alert(data.message);
      }
      
      async function sendEmail() {
        const email = prompt('Entrez votre adresse email:');
        if (!email) return;
        
        const response = await fetch('/api/send-email', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({email})
        });
        const data = await response.json();
        alert(data.message);
      }
    </script>
  `);
});

// API pour tester la connexion MCP
app.post('/api/test-mcp', async (req, res) => {
  try {
    const result = await mcpClient.testConnection();
    
    if (result.success) {
      res.json({
        success: true,
        message: 'Connexion au MCP réussie !',
        data: result.data
      });
    } else {
      throw new Error(result.error);
    }
  } catch (error) {
    console.error('Erreur de connexion MCP:', error);
    res.status(500).json({
      success: false,
      message: 'Erreur de connexion au MCP',
      error: error.message
    });
  }
});

// API pour envoyer un email via MCP
app.post('/api/send-email', async (req, res) => {
  try {
    const { email } = req.body;
    
    if (!email) {
      return res.status(400).json({
        success: false,
        message: 'Adresse email requise'
      });
    }
    
    const result = await mcpClient.sendEmail(
      email,
      'Test AutoAp MCP',
      'Ceci est un email ! de test envoyé depuis l\'application AutoAp via MCP Zapier.'
    );
    
    if (result.success) {
      res.json({
        success: true,
        message: 'Email envoyé avec succès !',
        data: result.data
      });
    } else {
      throw new Error(result.error);
    }
  } catch (error) {
    console.error('Erreur d\'envoi d\'email:', error);
    res.status(500).json({
      success: false,
      message: 'Erreur lors de l\'envoi de l\'email',
      error: error.message
    });
  }
});

// Démarrage du serveur
app.listen(port, () => {
  console.log(`Application AutoAp démarrée sur http://localhost:${port}`);
}); 