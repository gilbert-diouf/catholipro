<?php

    class Database
    {
        private string $serveur = "localhost";
        private string $nomBase = "catholipro";
        private string $utilisateur = "root";
        private string $motDePasse = "root";

        private PDO $connexion;

        public function __construct()
        {
            $this->connexion = new PDO(
                "mysql:host=$this->serveur;dbname=$this->nomBase;charset=utf8mb4",
                $this->utilisateur,
                $this->motDePasse
            );

            $this->connexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->connexion->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );
        }

        public function getConnection(): PDO
        {
            return $this->connexion;
        }
    }
?>