<?php
 
    require_once __DIR__ . '/../Database/Database.php';
    require_once __DIR__ . '/../Models/Categorie.php';
    
    class CategorieRepository
    {
        private PDO $connexion;
    
        public function __construct()
        {
            $database = new Database();
            $this->connexion = $database->getConnection();
        }
    
        private function creerDepuisLigne(array $donnees): Categorie
        {
            return new Categorie(
                $donnees['id'],
                $donnees['nom'],
                $donnees['icone'] ?? null,
                $donnees['statut']
            );
        }
    
        /**
         * Récupère uniquement les catégories actives, triées par nom.
         * (Utilisé pour les filtres de recherche côté public)
         */
        public function findAllActives(): array
        {
            $sql = "SELECT id, nom, icone, statut FROM categories WHERE statut = 'actif' ORDER BY nom ASC";
    
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute();
    
            $donnees = $stmt->fetchAll();
    
            $categories = [];
    
            foreach ($donnees as $donnee) {
                $categories[] = $this->creerDepuisLigne($donnee);
            }
    
            return $categories;
        }
    
        /**
         * Récupère une catégorie active par son id.
         * Retourne null si elle n'existe pas ou n'est pas active.
         */
        public function findById(int $id): ?Categorie
        {
            $sql = "SELECT id, nom, icone, statut FROM categories WHERE id = ? AND statut = 'actif' LIMIT 1";
    
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
 