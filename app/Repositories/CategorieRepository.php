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
 
    /**
     * Récupère une catégorie quel que soit son statut (actif ou inactif).
     * Contrairement à findById(), utilisée pour l'administration
     * (ex. réactiver une catégorie désactivée).
     */
    public function findByIdAny(int $id): ?Categorie
    {
        $sql = "SELECT id, nom, icone, statut FROM categories WHERE id = ? LIMIT 1";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$id]);
 
        $donnees = $stmt->fetch();
 
        if (!$donnees) {
            return null;
        }
 
        return $this->creerDepuisLigne($donnees);
    }
 
    /**
     * Liste toutes les catégories, actives ou non (vue admin).
     *
     * @return Categorie[]
     */
    public function findAll(): array
    {
        $sql = "SELECT id, nom, icone, statut FROM categories ORDER BY nom ASC";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute();
 
        $donnees = $stmt->fetchAll();
 
        $categories = [];
 
        foreach ($donnees as $donnee) {
            $categories[] = $this->creerDepuisLigne($donnee);
        }
 
        return $categories;
    }
 
    public function count(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM categories";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute();
 
        return (int) $stmt->fetch()["total"];
    }
 
    public function insert(string $nom, ?string $icone): void
    {
        $sql = "INSERT INTO categories (nom, icone, statut) VALUES (?, ?, 'actif')";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$nom, $icone]);
    }
 
    public function updateStatut(int $id, string $statut): void
    {
        $sql = "UPDATE categories SET statut = ? WHERE id = ?";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$statut, $id]);
    }
 
    /**
     * Supprime une catégorie. Laisse remonter la PDOException en cas de
     * contrainte de clé étrangère (professionnels encore rattachés) —
     * c'est au Service de la traduire en exception métier.
     */
    public function delete(int $id): void
    {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$id]);
    }
}
 