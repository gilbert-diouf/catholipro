<?php
 
    require_once __DIR__ . '/../Database/Database.php';
    require_once __DIR__ . '/../Models/Localisation.php';
    
    class LocalisationRepository
    {
        private PDO $connexion;
    
        public function __construct()
        {
            $database = new Database();
            $this->connexion = $database->getConnection();
        }
    
        private function creerDepuisLigne(array $donnees): Localisation
        {
            return new Localisation(
                $donnees['id'],
                $donnees['region'],
                $donnees['departement'] ?? null,
                $donnees['ville'],
                $donnees['quartier'] ?? null
            );
        }
    
        public function findAll(): array
        {
            $sql = "SELECT id, region, departement, ville, quartier FROM localisations ORDER BY region, ville, quartier";
    
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute();
    
            $donnees = $stmt->fetchAll();
    
            $localisations = [];
    
            foreach ($donnees as $donnee) {
                $localisations[] = $this->creerDepuisLigne($donnee);
            }
    
            return $localisations;
        }
    
        public function findById(int $id): ?Localisation
        {
            $sql = "SELECT id, region, departement, ville, quartier FROM localisations WHERE id = ? LIMIT 1";
    
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute([$id]);
    
            $donnees = $stmt->fetch();
    
            if (!$donnees) {
                return null;
            }
    
            return $this->creerDepuisLigne($donnees);
        }
    }
?>    