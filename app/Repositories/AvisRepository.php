<?php
 
require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Models/Avis.php';
require_once __DIR__ . '/../Models/AvisAffichage.php';
require_once __DIR__ . '/../Models/AvisModeration.php';
 
class AvisRepository
{
    private PDO $connexion;
 
    public function __construct()
    {
        $database = new Database();
        $this->connexion = $database->getConnection();
    }
 
    private function creerDepuisLigne(array $donnees): Avis
    {
        return new Avis(
            $donnees['id'],
            $donnees['profil_professionnel_id'],
            $donnees['utilisateur_id'],
            (int) $donnees['note'],
            $donnees['commentaire'] ?? null,
            $donnees['statut_moderation'],
            $donnees['date_creation'] ?? null,
            $donnees['date_modification'] ?? null
        );
    }
 
    public function findById(int $id): ?Avis
    {
        $sql = "SELECT * FROM avis WHERE id = ? LIMIT 1";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$id]);
 
        $donnees = $stmt->fetch();
 
        return $donnees ? $this->creerDepuisLigne($donnees) : null;
    }
 
    public function findByUtilisateurEtProfil(int $utilisateurId, int $profilProfessionnelId): ?Avis
    {
        $sql = "
            SELECT * FROM avis
            WHERE utilisateur_id = ? AND profil_professionnel_id = ?
            LIMIT 1
        ";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$utilisateurId, $profilProfessionnelId]);
 
        $donnees = $stmt->fetch();
 
        return $donnees ? $this->creerDepuisLigne($donnees) : null;
    }
 
    /**
     * Crée ou met à jour un avis (id === 0 → création).
     */
    public function save(Avis $avis): Avis
    {
        if ($avis->getId() === 0) {
 
            $sql = "
                INSERT INTO avis
                (profil_professionnel_id, utilisateur_id, note, commentaire, statut_moderation)
                VALUES (?, ?, ?, ?, ?)
            ";
 
            $stmt = $this->connexion->prepare($sql);
            $stmt->execute([
                $avis->getProfilProfessionnelId(),
                $avis->getUtilisateurId(),
                $avis->getNote(),
                $avis->getCommentaire(),
                $avis->getStatutModeration()
            ]);
 
            $nouvelId = (int) $this->connexion->lastInsertId();
 
            return new Avis(
                $nouvelId,
                $avis->getProfilProfessionnelId(),
                $avis->getUtilisateurId(),
                $avis->getNote(),
                $avis->getCommentaire(),
                $avis->getStatutModeration()
            );
        }
 
        $sql = "
            UPDATE avis
            SET note = ?, commentaire = ?, statut_moderation = ?, date_modification = NOW()
            WHERE id = ?
        ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([
            $avis->getNote(),
            $avis->getCommentaire(),
            $avis->getStatutModeration(),
            $avis->getId()
        ]);
 
        return $avis;
    }
 
    /**
     * @return AvisAffichage[]
     */
    public function findPubliesByProfil(int $profilProfessionnelId): array
    {
        $sql = "
            SELECT
                a.id, a.note, a.commentaire, a.date_creation,
                u.prenom AS auteur_prenom, u.nom AS auteur_nom
            FROM avis a
            INNER JOIN utilisateurs u ON u.id = a.utilisateur_id
            WHERE a.profil_professionnel_id = ?
            AND a.statut_moderation = 'publie'
            ORDER BY a.date_creation DESC
        ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$profilProfessionnelId]);
 
        $resultats = [];
 
        foreach ($stmt->fetchAll() as $ligne) {
            $resultats[] = new AvisAffichage(
                $ligne['id'],
                $ligne['auteur_prenom'],
                $ligne['auteur_nom'],
                (int) $ligne['note'],
                $ligne['commentaire'] ?? null,
                $ligne['date_creation'] ?? null
            );
        }
 
        return $resultats;
    }
 
    /**
     * @return array{moyenne: float, total: int}
     */
    public function calculerStatistiques(int $profilProfessionnelId): array
    {
        $sql = "
            SELECT AVG(note) AS moyenne, COUNT(*) AS total
            FROM avis
            WHERE profil_professionnel_id = ? AND statut_moderation = 'publie'
        ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$profilProfessionnelId]);
 
        $ligne = $stmt->fetch();
 
        return [
            "moyenne" => $ligne["moyenne"] !== null ? round((float) $ligne["moyenne"], 1) : 0.0,
            "total" => (int) $ligne["total"]
        ];
    }
 
    /**
     * @return AvisModeration[]
     */
    public function findEnAttente(): array
    {
        return $this->findParStatutModeration("en_attente");
    }
 
    /**
     * @return AvisModeration[]
     */
    public function findPublies(): array
    {
        return $this->findParStatutModeration("publie");
    }
 
    /**
     * @return AvisModeration[]
     */
    private function findParStatutModeration(string $statut): array
    {
        $sql = "
            SELECT
                a.id, a.note, a.commentaire, a.date_creation,
                auteur.prenom AS auteur_prenom, auteur.nom AS auteur_nom,
                pro.prenom AS pro_prenom, pro.nom AS pro_nom
            FROM avis a
            INNER JOIN utilisateurs auteur ON auteur.id = a.utilisateur_id
            INNER JOIN profils_professionnels pp ON pp.id = a.profil_professionnel_id
            INNER JOIN utilisateurs pro ON pro.id = pp.utilisateur_id
            WHERE a.statut_moderation = ?
            ORDER BY a.date_creation ASC
        ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$statut]);
 
        $resultats = [];
 
        foreach ($stmt->fetchAll() as $ligne) {
            $resultats[] = new AvisModeration(
                $ligne['id'],
                $ligne['auteur_prenom'],
                $ligne['auteur_nom'],
                $ligne['pro_prenom'],
                $ligne['pro_nom'],
                (int) $ligne['note'],
                $ligne['commentaire'] ?? null,
                $ligne['date_creation'] ?? null
            );
        }
 
        return $resultats;
    }
 
    public function countEnAttente(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM avis WHERE statut_moderation = 'en_attente'";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute();
 
        return (int) $stmt->fetch()["total"];
    }
 
    public function updateStatutModeration(int $id, string $statut): void
    {
        $sql = "UPDATE avis SET statut_moderation = ? WHERE id = ?";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$statut, $id]);
    }
 
    public function delete(int $id): void
    {
        $sql = "DELETE FROM avis WHERE id = ?";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$id]);
    }
}