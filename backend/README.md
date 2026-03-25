# AvenirPro - Backend (Laravel 12 API)

AvenirPro est une API REST développée avec **Laravel 12 (PHP 8.2+)**. 
Elle sert de moteur logique pour l'application d'orientation scolaire et de présentation de métiers, tout en assurant la liaison avec des outils d'Intelligence Artificielle (Gemini via OpenRouter).

## 🚀 Fonctionnalités Principales

- **Authentification Sécurisée** : Connexion classique et via Google OAuth (Socialite).
- **Session API** : Utilisation de Laravel Sanctum avec protection CSRF.
- **Gestion des Métiers** : Endpoint de récupération des métiers (avec filtres, recherche dynamique, et pagination).
- **Test d'Orientation RIASEC** : Questionnaire interactif générant un profil de personnalité basé sur un algorithme de corrélation.
- **Enrichissement IA** : Intégration de l'API OpenRouter (Gemini Pro) pour fournir à l'élève une recommandation hautement personnalisée ("Pourquoi ce métier te correspond").
- **Backoffice Admin** : Endpoints protégés par middleware pour l'ajout/suppression de métiers et questions.

## ⚙️ Prérequis

- **PHP** : >= 8.2
- **Composer** : Pour la gestion des dépendances
- **Base de données** : MySQL, MariaDB, ou PostgreSQL

## 🛠 Installation Locale (Développement)

1. **Cloner le projet** et se positionner dans le dossier `backend`.

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   Copiez le fichier d'exemple fourni et modifiez-le avec vos identifiants de base de données.
   ```bash
   cp .env.example .env
   ```

4. **Générer la clé d'application sécurisée**
   ```bash
   php artisan key:generate
   ```

5. **Lancer les Migrations et les Seeders**
   Créez une base de données MySQL vide (ex: `avenirpro`).
   *(Si vous êtes sur WAMP, assurez-vous que la base est accessible via root sans mot de passe, ou modifiez votre `.env`)*
   ```bash
   php artisan migrate --seed
   ```
   *Astuce : Le `DatabaseSeeder` génère les données initiales nécessaires : questions du test, catégories, et un catalogue complet de métiers avec images.*

6. **Démarrer le serveur local**
   ```bash
   php artisan serve
   ```
   L'API sera disponible sur `http://localhost:8000`.

## 📦 Environnement de Production (Déploiement)

Le backend Laravel doit être déployé sur un VPS (via Laravel Forge ou manuellement) ou un hébergement mutualisé compatible (ex: cPanel).

### Déploiement classique (cPanel / Mutu)

1. Uploadez tous les fichiers de l'application (sauf `node_modules` si présents).
2. Pointez la racine web du domaine (Document Root) vers le dossier `public/` de Laravel, et **non pas** à la racine de l'application. C'est crucial pour la sécurité.
3. Créez une base de données MySQL via cPanel et importez la structure ou exécutez les migrations via SSH (`php artisan migrate --force`).
4. Remplissez le fichier `.env` sur le serveur avec les bonnes identifiants :
   - Assurez-vous d'avoir `APP_ENV=production` et `APP_DEBUG=false`
   - Modifiez `APP_URL` pour y insérer l'URL finale (ex: `https://api.votre-domaine.com`)
   - Modifiez `FRONTEND_URL` pour les headers CORS (ex: `https://votre-domaine.com`)
5. Liez le dossier de stockage et optimisez (en SSH) :
   ```bash
   php artisan storage:link
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🔐 Configuration des API Externes (.env)

Afin que toutes les fonctionnalités soient opérantes, certaines clés d'API requièrent votre attention :

- **Google OAuth** : Obtenez un `GOOGLE_CLIENT_ID` et `SECRET` via *Google Cloud Console*. Ajoutez l'URI de redirection `http://localhost:8000/api/v1/auth/google/callback`.
- **OpenRouter (IA)** : L'enrichissement des données repose sur *google/gemini-pro-1.5*. Créez un compte sur [OpenRouter](https://openrouter.ai/), créez une clé API et ajoutez-la à la directive `OPENROUTER_API_KEY`.
