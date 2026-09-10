<?php
 
require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Models/Utilisateur.php';
 
class UtilisateurRepository{
 
    private PDO $connexion;
 
    public function __construct(){
        $database = new Database();
        $this->connexion = $database->getConnection();
    }
 
    private function creerDepuisLigne(array $donnees): Utilisateur
    {
        return new Utilisateur(
            $donnees['id'],
            $donnees['nom'],
            $donnees['prenom'],
            $donnees['email'],
            $donnees['telephone'],
            $donnees['mot_de_passe'],
            $donnees['role'],
            $donnees['statut'],
            $donnees['photo_profil'] ?? null,
            $donnees['date_creation'] ?? null
        );
    }
 
    public function findById(int $id): ?Utilisateur{
        $sql = "SELECT * FROM utilisateurs WHERE id = ?";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$id]);
 
        $donnees = $stmt->fetch();
 
        if (!$donnees) {
            return null;
        }
 
        return $this->creerDepuisLigne($donnees);
    }
 
    public function findByEmail(string $email): ?Utilisateur{
        $sql = "SELECT * FROM utilisateurs WHERE email = ?";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$email]);
 
        $donnees = $stmt->fetch();
 
        if (!$donnees) {
            return null;
        }
 
        return $this->creerDepuisLigne($donnees);
    }
 
    public function save(Utilisateur $utilisateur): void
    {
        $sql = "INSERT INTO utilisateurs
                (nom, prenom, email, telephone, mot_de_passe, role, statut)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
 
        $stmt = $this->connexion->prepare($sql);
 
        $stmt->execute([
            $utilisateur->getNom(),
            $utilisateur->getPrenom(),
            $utilisateur->getEmail(),
            $utilisateur->getTelephone(),
            $utilisateur->getMotDePasse(),
            $utilisateur->getRole(),
            $utilisateur->getStatut()
        ]);
    }
 
    public function count(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM utilisateurs";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute();
 
        return (int) $stmt->fetch()["total"];
    }
 
    public function countByRole(string $role): int
    {
        $sql = "SELECT COUNT(*) AS total FROM utilisateurs WHERE role = ?";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$role]);
 
        return (int) $stmt->fetch()["total"];
    }
 
    public function updateStatut(int $id, string $statut): void
    {
        $sql = "UPDATE utilisateurs SET statut = ? WHERE id = ?";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$statut, $id]);
    }
 
    /**
     * Liste tous les utilisateurs, avec filtre optionnel par rôle.
     *
     * @return Utilisateur[]
     */
    public function findAll(?string $role = null): array
    {
        $sql = "SELECT * FROM utilisateurs";
        $params = [];
 
        if ($role !== null) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
 
        $sql .= " ORDER BY date_creation DESC";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute($params);
 
        $donnees = $stmt->fetchAll();
 
        $utilisateurs = [];
 
        foreach ($donnees as $donnee) {
            $utilisateurs[] = $this->creerDepuisLigne($donnee);
        }
 
        return $utilisateurs;
    }
}
 