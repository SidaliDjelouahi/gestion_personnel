-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 29 oct. 2025 à 19:44
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_personnel`
--

-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE `absences` (
  `id_absence` int(11) NOT NULL,
  `id_personnel` int(11) NOT NULL,
  `date_absence` date NOT NULL,
  `duree` int(11) NOT NULL,
  `motif` varchar(150) DEFAULT NULL,
  `statut` enum('Justifiée','Non justifiée','En attente') DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `carriere`
--

CREATE TABLE `carriere` (
  `id_carriere` int(11) NOT NULL,
  `id_personnel` int(11) NOT NULL,
  `id_grade` int(11) NOT NULL,
  `id_service` int(11) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `type_mouvement` enum('Recrutement','Avancement','Mutation','Détachement') DEFAULT NULL,
  `observation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conges`
--

CREATE TABLE `conges` (
  `id_conge` int(11) NOT NULL,
  `id_personnel` int(11) NOT NULL,
  `type_conge` enum('Annuel','Maladie','Maternité','Exceptionnel') NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` enum('Accepté','Refusé','En attente') DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `echelons`
--

CREATE TABLE `echelons` (
  `id_echelon` int(11) NOT NULL,
  `nom_echelon` varchar(100) NOT NULL,
  `indice` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

CREATE TABLE `formations` (
  `id_formation` int(11) NOT NULL,
  `id_personnel` int(11) NOT NULL,
  `intitule` varchar(150) NOT NULL,
  `organisme` varchar(150) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `resultat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

CREATE TABLE `grades` (
  `id_grade` int(11) NOT NULL,
  `libelle_grade` varchar(100) NOT NULL,
  `categorie` varchar(50) DEFAULT NULL,
  `indice_salarial` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paie`
--

CREATE TABLE `paie` (
  `id_paie` int(11) NOT NULL,
  `id_personnel` int(11) NOT NULL,
  `mois` tinyint(4) NOT NULL,
  `annee` year(4) NOT NULL,
  `salaire_base` decimal(10,2) NOT NULL,
  `indemnites` decimal(10,2) DEFAULT 0.00,
  `retenues` decimal(10,2) DEFAULT 0.00,
  `net_a_payer` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel`
--

CREATE TABLE `personnel` (
  `id_personnel` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `adresse` varchar(200) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `etat_civil` varchar(50) DEFAULT NULL,
  `sexe` enum('Homme','Femme') DEFAULT NULL,
  `date_recrutement` date NOT NULL,
  `type_contrat` enum('Titulaire','Contractuel','Vacataire') NOT NULL,
  `id_service` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel_primes`
--

CREATE TABLE `personnel_primes` (
  `id_personnel` int(11) NOT NULL,
  `id_prime` int(11) NOT NULL,
  `valeur` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `primes`
--

CREATE TABLE `primes` (
  `id_prime` int(11) NOT NULL,
  `nom_prime` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `formule_calcul` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `regles_carriere`
--

CREATE TABLE `regles_carriere` (
  `id_regle` int(11) NOT NULL,
  `intitule` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duree_annees` int(11) DEFAULT NULL,
  `condition_grade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sanctions`
--

CREATE TABLE `sanctions` (
  `id_sanction` int(11) NOT NULL,
  `id_personnel` int(11) NOT NULL,
  `type_sanction` enum('Avertissement','Blâme','Exclusion') NOT NULL,
  `date_sanction` date NOT NULL,
  `motif` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id_service` int(11) NOT NULL,
  `nom_service` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id_absence`),
  ADD KEY `id_personnel` (`id_personnel`);

--
-- Index pour la table `carriere`
--
ALTER TABLE `carriere`
  ADD PRIMARY KEY (`id_carriere`),
  ADD KEY `id_personnel` (`id_personnel`),
  ADD KEY `id_grade` (`id_grade`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `conges`
--
ALTER TABLE `conges`
  ADD PRIMARY KEY (`id_conge`),
  ADD KEY `id_personnel` (`id_personnel`);

--
-- Index pour la table `echelons`
--
ALTER TABLE `echelons`
  ADD PRIMARY KEY (`id_echelon`);

--
-- Index pour la table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id_formation`),
  ADD KEY `id_personnel` (`id_personnel`);

--
-- Index pour la table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id_grade`);

--
-- Index pour la table `paie`
--
ALTER TABLE `paie`
  ADD PRIMARY KEY (`id_paie`),
  ADD KEY `id_personnel` (`id_personnel`);

--
-- Index pour la table `personnel`
--
ALTER TABLE `personnel`
  ADD PRIMARY KEY (`id_personnel`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD KEY `id_service` (`id_service`);

--
-- Index pour la table `personnel_primes`
--
ALTER TABLE `personnel_primes`
  ADD PRIMARY KEY (`id_personnel`,`id_prime`),
  ADD KEY `id_prime` (`id_prime`);

--
-- Index pour la table `primes`
--
ALTER TABLE `primes`
  ADD PRIMARY KEY (`id_prime`);

--
-- Index pour la table `regles_carriere`
--
ALTER TABLE `regles_carriere`
  ADD PRIMARY KEY (`id_regle`),
  ADD KEY `condition_grade` (`condition_grade`);

--
-- Index pour la table `sanctions`
--
ALTER TABLE `sanctions`
  ADD PRIMARY KEY (`id_sanction`),
  ADD KEY `id_personnel` (`id_personnel`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id_service`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absences`
--
ALTER TABLE `absences`
  MODIFY `id_absence` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `carriere`
--
ALTER TABLE `carriere`
  MODIFY `id_carriere` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `conges`
--
ALTER TABLE `conges`
  MODIFY `id_conge` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `echelons`
--
ALTER TABLE `echelons`
  MODIFY `id_echelon` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formations`
--
ALTER TABLE `formations`
  MODIFY `id_formation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `grades`
--
ALTER TABLE `grades`
  MODIFY `id_grade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paie`
--
ALTER TABLE `paie`
  MODIFY `id_paie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personnel`
--
ALTER TABLE `personnel`
  MODIFY `id_personnel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `primes`
--
ALTER TABLE `primes`
  MODIFY `id_prime` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `regles_carriere`
--
ALTER TABLE `regles_carriere`
  MODIFY `id_regle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sanctions`
--
ALTER TABLE `sanctions`
  MODIFY `id_sanction` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE;

--
-- Contraintes pour la table `carriere`
--
ALTER TABLE `carriere`
  ADD CONSTRAINT `carriere_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE,
  ADD CONSTRAINT `carriere_ibfk_2` FOREIGN KEY (`id_grade`) REFERENCES `grades` (`id_grade`) ON DELETE CASCADE;

--
-- Contraintes pour la table `conges`
--
ALTER TABLE `conges`
  ADD CONSTRAINT `conges_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE;

--
-- Contraintes pour la table `formations`
--
ALTER TABLE `formations`
  ADD CONSTRAINT `formations_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paie`
--
ALTER TABLE `paie`
  ADD CONSTRAINT `paie_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE;

--
-- Contraintes pour la table `personnel`
--
ALTER TABLE `personnel`
  ADD CONSTRAINT `personnel_ibfk_1` FOREIGN KEY (`id_service`) REFERENCES `services` (`id_service`) ON DELETE SET NULL;

--
-- Contraintes pour la table `personnel_primes`
--
ALTER TABLE `personnel_primes`
  ADD CONSTRAINT `personnel_primes_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE,
  ADD CONSTRAINT `personnel_primes_ibfk_2` FOREIGN KEY (`id_prime`) REFERENCES `primes` (`id_prime`) ON DELETE CASCADE;

--
-- Contraintes pour la table `regles_carriere`
--
ALTER TABLE `regles_carriere`
  ADD CONSTRAINT `regles_carriere_ibfk_1` FOREIGN KEY (`condition_grade`) REFERENCES `grades` (`id_grade`) ON DELETE SET NULL;

--
-- Contraintes pour la table `sanctions`
--
ALTER TABLE `sanctions`
  ADD CONSTRAINT `sanctions_ibfk_1` FOREIGN KEY (`id_personnel`) REFERENCES `personnel` (`id_personnel`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
