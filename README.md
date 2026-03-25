# Auteure : SAMEY Koffivi Herve

# AvenirPro - Projet d'Orientation Professionnelle par IA

Bienvenue sur le monorepo **AvenirPro**, une plateforme moderne conçue pour aider les jeunes étudiants et professionnels à trouver leur voie grâce à un test de personnalité RIASEC, soutenu par la puissance de l'Intelligence Artificielle.

Le projet est divisé en deux parties indépendantes (Architecture Headless) :

- 🌍 **Frontend** (`/frontend`) : Interface utilisateur développée en Vue.js 3, Vite et Tailwind CSS. *Permet une navigation fluide, ultra-rapide (Single Page Application) et un UI/UX particulièrement soigné (effet Glassmorphism, animations avancées).*
- ⚙️ **Backend** (`/backend`) : Moteur d'API développé en Laravel 12 (PHP 8.2). *Gère l'authentification (Sanctum/Socialite), l'algorithme RIASEC pour lier personnalité et métiers, le stockage BDD et l'interrogation des LLM via OpenRouter (Gemini).*

## 📖 Démarrage Rapide (Ensemble)

Pour faire fonctionner le projet localement, vous devez lancer les deux environnements en parallèle.

### 1. Démarrer l'API (Backend)
Rendez-vous dans le dossier `/backend`.
Consultez le fichier `backend/README.md` pour l'installation détaillée (Composer, configuration `.env`, base de données, seeding).

Une fois configuré, lancez :
```bash
cd backend
php artisan serve
```
Le serveur écoutera sur `http://localhost:8000`.

### 2. Démarrer l'Interface (Frontend)
Rendez-vous dans le dossier `/frontend`.
Consultez le fichier `frontend/README.md` pour l'installation (Node.js, npm).

Une fois les dépendances installées :
```bash
cd frontend
npm run dev
```
L'interface sera accessible sur `http://localhost:5173`.

## 🛠 Technologies Clés
*   **Vue.js 3 / Composition API**
*   **Tailwind CSS V4**
*   **Pinia (State Management)**
*   **Laravel 12**
*   **MySQL & Serveur Apache/Nginx**
*   **API OpenRouter (LLM)**

## 🚀 Déploiement Séparé

Ce projet est préparé pour un déploiement professionnel découplé :
1. Poussez votre dossier `frontend` sur une solution serverless comme **Vercel** ou **Netlify**. Le coût d'hébergement y est nul, avec des performances de CDN globales.
2. Poussez votre dossier `backend` sur un VPS ou serveur PHP (cPanel, Laravel Forge).
3. Connectez simplement l'URL de votre API dans le `.env` de production du frontend, et n'oubliez pas d'autoriser l'URL frontend dans les paramètres de CORS `config/cors.php` du backend, ou via votre `.env` (`FRONTEND_URL`).

Lisez les guides à l'intérieur des sous-dossiers pour plus d'informations.
