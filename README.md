# E-commerce Laravel 12 + Vue.js (Inertia.js)

## 🚀 Description
Ce projet est un **starter kit Laravel 12** avec **Vue.js** et **Inertia.js**, destiné à digitaliser une entreprise commerciale de produits et matériels informatiques.  

Le projet permet :
- Aux visiteurs : parcourir les produits, filtrer par catégorie, et consulter les méthodes de paiement.
- Aux clients : s’inscrire, se connecter, gérer leur panier et tester les paiements via l’agrégateur **PAPI**.

> **Entreprise :** Les Casainiers (Page Facebook : Les Casainiers)  
> **État du projet :** ~40% complété – certaines fonctionnalités comme l’admin ne sont pas encore disponibles  

---

## 🛠️ Prérequis

Avant d’installer et de lancer le projet, assurez-vous d’avoir :

### 1️⃣ Environnement serveur
- **PHP 8.2+**  
- **Serveur local** : XAMPP, WAMP ou Laragon (Apache/Nginx + MySQL)  

### 2️⃣ Gestionnaire de dépendances PHP
- **Composer** ([getcomposer.org](https://getcomposer.org))  

### 3️⃣ Environnement frontend
- **Node.js 18+** et **npm** ([nodejs.org](https://nodejs.org))  

### 4️⃣ Base de données
- **MySQL / MariaDB**  
- Créer une base vide pour le projet  
- Configurer l’accès dans le fichier `.env`  

### 5️⃣ Gestion des clés API
- **PAPI** ([papi.mg](https://papi.mg)) pour les paiements  
- Mode TEST recommandé pour le développement  

---

## ⭐ Fonctionnalités principales

### Côté visiteurs
- Parcourir les produits
- Rechercher et filtrer par catégorie
- Consulter les méthodes de paiement

### Côté clients
- S’inscrire et se connecter
- Gérer son panier
- Tester les paiements via PAPI (mode test)

> ⚠️ **Note** : l’interface admin n’est pas encore disponible.  
> Les données initiales sont fournies via **seeders** pour pouvoir tester le site.  

---

## 🏗️ Architecture du projet

Le projet suit le **modèle MVC** :

- **Models** : Gestion des données et de la base  
- **Views** : Interfaces utilisateurs avec Vue.js + Inertia  
- **Controllers** : Logique métier et routes  

---

## ⚡ Installation et test avec seeders

```bash
# 1. Cloner le projet
git clone https://github.com/Tafita025/Les-cassainiers.git
cd Les-cassainiers

# 2. Installer les dépendances
composer install
npm install

# 3. Copier l'environnement
cp .env.example .env
php artisan key:generate

# 4. Migrer la base de données
php artisan migrate

# 5. Lancer les seeders pour les données de test
php artisan db:seed

# 6. Compiler les assets frontend
npm run dev

# 7. Lancer le serveur Laravel
php artisan serve
