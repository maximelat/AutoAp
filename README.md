# AutoAp

Application automatisée avec intégration MCP (Multi-Cloud Provider) via Zapier.

## Configuration

### Prérequis
- Node.js
- Accès FTP au serveur d'hébergement
- Clé MCP Zapier

### Déploiement
Le déploiement est automatisé via GitHub Actions. À chaque push sur la branche `main`, les étapes suivantes sont exécutées :
1. Construction de l'application
2. Déploiement sur le serveur FTP
3. Notification au service MCP Zapier

### Variables d'environnement
Pour que le déploiement fonctionne, ajoutez le secret suivant dans les paramètres de votre dépôt GitHub :
- `FTP_PASSWORD` : Mot de passe pour l'accès FTP

### Point d'accès MCP
L'application communique avec le service MCP à l'adresse :
```
https://actions.zapier.com/mcp/sk-ak-LybLOg46s92oXPE8jxsNVa5RoZ/sse
```

### Informations du serveur
- Serveur FTP : `ftp.cluster029.hosting.ovh.net`
- Utilisateur : `latrycf`
- Répertoire : `/www/projet/AutoAp` 