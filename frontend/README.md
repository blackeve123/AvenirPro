# AvenirPro - Frontend (Vue.js 3 + Vite)

AvenirPro est une application web innovante permettant aux étudiants de passer un test d'orientation (type RIASEC) enrichi par l'Intelligence Artificielle (Gemini/OpenRouter) pour obtenir des recommandations de carrières ciblées.

Ce dossier contient l'interface utilisateur (Single Page Application) construite avec **Vue.js 3**, **Vite**, **Tailwind CSS**, et le state management **Pinia**.

## 🚀 Installation Locale (Développement)

### Prérequis
- [Node.js](https://nodejs.org/) (version 18+ recommandée)
- Le backend Laravel doit être configuré et en cours d'exécution sur le port 8000.

### Étapes

1. **Installer les dépendances**
   ```bash
   npm install
   ```

2. **Configurer l'environnement**
   Copiez le fichier d'exemple et configurez l'URL pointant vers le backend.
   ```bash
   cp .env.example .env.local
   ```
   *Astuce : Si le backend est sur un autre port que 8000, modifiez `VITE_API_URL` dans le fichier `.env.local`.*

3. **Lancer le serveur de développement**
   ```bash
   npm run dev
   ```
   L'application sera accessible sur `http://localhost:5173`. L'interface bénéficie du Hot Module Replacement (HMR) pour recharger automatiquement vos modifications de code.

## 📦 Environnement de Production (Build & Déploiement)

Le frontend Vue.js étant statique, il peut être hébergé très facilement et gratuitement sur des plateformes comme **Vercel** ou **Netlify**, indépendamment du backend.

### Déploiement automatisé sur Vercel (Recommandé)

1. Créez un compte sur [Vercel](https://vercel.com).
2. Cliquez sur **Add New... > Project** et connectez votre dépôt GitHub.
3. Importez le dossier `frontend` (ou configurez le "Root Directory" sur `frontend`).
4. Dans les paramètres d'environnement (Environment Variables) sur Vercel, ajoutez :
   - `VITE_API_URL` : L'URL de votre backend en production (ex: `https://api.votre-domaine.com/api/v1`)
5. Le Framework Preset devrait être détecté automatiquement sur **Vite**.
6. Cliquez sur **Deploy**.

Vercel exécutera automatiquement `npm install` et `npm run build`, puis distribuera le dossier `dist` sur son réseau CDN global.

### Déploiement manuel sur cPanel / Serveur Apache

Si vous souhaitez héberger le frontend et le backend sur le même serveur mutualisé (ex: cPanel) :

1. Compilez le projet localement :
   ```bash
   npm run build
   ```
2. Le build va générer un dossier `/dist`.
3. Uploadez le **contenu** de ce dossier `/dist` dans le dossier public de votre serveur (ex: `public_html`).
4. **Important (Routing)** : Sur Apache, les applications SPA (Vue Router) nécessitent un fichier `.htaccess` dans `public_html` pour rediriger toutes les requêtes vers `index.html`. Créez un `.htaccess` avec le code suivant :
   ```apache
   <IfModule mod_rewrite.c>
     RewriteEngine On
     RewriteBase /
     RewriteRule ^index\.html$ - [L]
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteCond %{REQUEST_FILENAME} !-d
     RewriteRule . /index.html [L]
   </IfModule>
   ```

## 🛠 Bibliothèques clés utilisées

- **Vue Router** : Gestion des routes frontend (Métiers, Test, Profil).
- **Pinia** : Gestion de l'état global et persistance de la session utilisateur.
- **Tailwind CSS 4** : Design system moderne et réactif.
- **Axios** : Appels HTTP vers l'API Laravel, avec gestion sécurisée des cookies CORS (Sanctum).
