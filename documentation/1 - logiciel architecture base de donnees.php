<?php
/**
 * ================================================================
 *   Documentation interne - Logiciel de gestion du personnel
 *   Conçu pour un établissement étatique en Algérie
 * ================================================================
 *
 * OBJECTIF :
 * -----------
 * Ce logiciel permet de gérer :
 *   - Les données administratives du personnel
 *   - Les carrières (avancements, promotions, mutations)
 *   - La paie (salaires, primes, masse salariale, prévisions)
 *   - Les absences, congés, sanctions et formations
 *   - La génération de documents officiels (bulletins, arrêtés, états de service)
 *   - Les rapports statistiques et budgétaires
 *
 * ----------------------------------------------------------------
 *  STRUCTURE DU PROJET (Arborescence)
 * ----------------------------------------------------------------
 *
 * gestion_personnel/
 * │
 * ├── index.php                  # Tableau de bord (stats + alertes)
 * ├── login.php / logout.php      # Authentification
 * │
 * ├── includes/                  # Fichiers communs
 * │   ├── db.php                 # Connexion à la base de données
 * │   ├── functions.php          # Fonctions génériques (CRUD, helpers)
 * │   ├── calculs.php            # Calculs automatisés (salaire, primes, ancienneté)
 * │   ├── auth.php               # Sécurité et gestion des sessions
 * │   ├── header.php / footer.php / sidebar.php  # Templates communs
 * │
 * ├── parametres/                # Données de référence paramétrables
 * │   ├── services.php
 * │   ├── grades.php
 * │   ├── categories.php
 * │   ├── echelons.php
 * │   └── primes.php
 * │
 * ├── personnel/                 # Gestion du personnel
 * │   ├── liste.php / ajouter.php / modifier.php / supprimer.php
 * │   └── details.php            # Dossier individuel complet
 * │
 * ├── carriere/                  # Historique de carrière
 * │   ├── avancements.php
 * │   ├── promotions.php
 * │   ├── mutations.php
 * │   └── historique.php
 * │
 * ├── paie/                      # Gestion des salaires
 * │   ├── generer.php            # Génération automatique
 * │   ├── liste.php              # Historique des bulletins
 * │   ├── bulletin.php           # Bulletin PDF
 * │   ├── masse.php              # Masse salariale
 * │   └── previsions.php         # Prévisions budgétaires
 * │
 * ├── absences/                  # Gestion des absences
 * ├── conges/                    # Gestion des congés
 * ├── sanctions/                 # Gestion des sanctions
 * ├── formations/                # Gestion des formations
 * │
 * ├── documents/                 # Documents officiels
 * │   ├── arretes.php            # Arrêtés (mutation, nomination…)
 * │   ├── attestation.php        # Attestations de travail/salaire
 * │   └── etat_service.php       # États de service
 * │
 * ├── rapports/                  # Rapports et bilans
 * │   ├── effectif.php
 * │   ├── absences.php
 * │   ├── paie.php
 * │   ├── budget.php
 * │   └── export_excel.php
 * │
 * ├── utilisateurs/              # Gestion des utilisateurs et profils
 * └── assets/                    # Ressources (CSS, JS, images)
 *
 *
 * ----------------------------------------------------------------
 *  POINTS FORTS DE L’ARCHITECTURE
 * ----------------------------------------------------------------
 *
 * 1. Centralisation des données
 *    - Tout est stocké dans une base SQL normalisée (services, grades, échelons, primes, etc.)
 *    - Chaque agent a un dossier unique avec son historique complet.
 *
 * 2. Modularité
 *    - Les modules sont indépendants (paie, carrière, absences…).
 *    - Possibilité d’ajouter ou de modifier un module sans casser les autres.
 *
 * 3. Réutilisation du code
 *    - Les fonctions communes (CRUD, calculs) sont regroupées dans includes/functions.php et includes/calculs.php
 *    - Les templates communs (header, footer, sidebar) garantissent une interface homogène.
 *
 * 4. Automatisation
 *    - Calcul automatique des salaires, primes, ancienneté et prévisions budgétaires.
 *    - Génération des bulletins de paie et des arrêtés administratifs.
 *
 * 5. Traçabilité
 *    - Historique des avancements, promotions, absences et congés.
 *    - Rapports détaillés par grade, service, budget.
 *
 * 6. Production documentaire
 *    - Génération de documents PDF (bulletins, attestations, arrêtés, états de service).
 *    - Export des données vers Excel pour exploitation externe.
 *
 * ----------------------------------------------------------------
 * UTILISATION DE CE DOCUMENT
 * ----------------------------------------------------------------
 * Ce fichier sert uniquement de documentation interne pour les développeurs
 * et administrateurs du système. Il n'est pas destiné à être exécuté.
 *
 */
?>
