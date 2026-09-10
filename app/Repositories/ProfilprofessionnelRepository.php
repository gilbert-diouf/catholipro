<?php
 
require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Models/ProfilProfessionnelRecherche.php';
require_once __DIR__ . '/../Models/ProfilProfessionnelDetail.php';
require_once __DIR__ . '/../Models/ProfilProfessionnel.php';
require_once __DIR__ . '/../Models/ProfilProfessionnelEnAttente.php';
 
class ProfilProfessionnelRepository
{
    private PDO $connexion;
 
    public function __construct()
    {
        $database = new Database();
        $this->connexion = $database->getConnection();
    }
 
    /**
     * Recherche les professionnels vérifiés et actifs, avec filtres
     * optionnels par catégorie et localisation.
     *
     * @return ProfilProfessionnelRecherche[]
     */
    public function rechercher(?int $categorieId, ?int $localisationId): array
    {
        $sql = "
            SELECT
                pp.id,
                pp.description,
                pp.specialites,
                pp.annees_experience,
 
                u.prenom,
                u.nom,
                u.photo_profil,
 
                c.nom AS categorie_nom,
                c.icone AS categorie_icone,
 
                l.ville,
                l.quartier
 
            FROM profils_professionnels pp
 
            INNER JOIN utilisateurs u ON u.id = pp.utilisateur_id
            INNER JOIN categories c ON c.id = pp.categorie_id
            INNER JOIN localisations l ON l.id = pp.localisation_id
 
            WHERE pp.statut_verification = 'verifie'
            AND u.statut = 'actif'
        ";
 
        $params = [];
 
        if (!empty($categorieId)) {
            $sql .= " AND pp.categorie_id = ? ";
            $params[] = $categorieId;
        }
 
        if (!empty($localisationId)) {
            $sql .= " AND pp.localisation_id = ? ";
            $params[] = $localisationId;
        }
 
        $sql .= " ORDER BY pp.date_creation DESC ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute($params);
 
        $donnees = $stmt->fetchAll();
 
        $resultats = [];
 
        foreach ($donnees as $ligne) {
            $resultats[] = new ProfilProfessionnelRecherche(
                $ligne['id'],
                $ligne['prenom'],
                $ligne['nom'],
                $ligne['photo_profil'] ?? null,
                $ligne['categorie_nom'],
                $ligne['categorie_icone'] ?? null,
                $ligne['ville'],
                $ligne['quartier'] ?? null,
                (int) $ligne['annees_experience'],
                $ligne['description'] ?? null,
                $ligne['specialites'] ?? null
            );
        }
 
        return $resultats;
    }
 
    /**
     * Récupère la fiche détaillée d'un professionnel vérifié et actif.
     * Retourne null s'il n'existe pas, n'est pas vérifié, ou si son
     * compte utilisateur n'est plus actif.
     */
    public function findDetailById(int $id): ?ProfilProfessionnelDetail
    {
        $sql = "
            SELECT
                pp.id,
                pp.description,
                pp.specialites,
                pp.annees_experience,
                pp.adresse,
                pp.whatsapp,
                pp.disponibilite,
 
                u.prenom,
                u.nom,
                u.email,
                u.telephone,
                u.photo_profil,
 
                c.nom AS categorie_nom,
                c.icone AS categorie_icone,
 
                l.region,
                l.departement,
                l.ville,
                l.quartier
 
            FROM profils_professionnels pp
 
            INNER JOIN utilisateurs u ON u.id = pp.utilisateur_id
            INNER JOIN categories c ON c.id = pp.categorie_id
            INNER JOIN localisations l ON l.id = pp.localisation_id
 
            WHERE pp.id = ?
            AND pp.statut_verification = 'verifie'
            AND u.statut = 'actif'
 
            LIMIT 1
        ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$id]);
 
        $ligne = $stmt->fetch();
 
        if (!$ligne) {
            return null;
        }
 
        return new ProfilProfessionnelDetail(
            $ligne['id'],
            $ligne['prenom'],
            $ligne['nom'],
            $ligne['email'] ?? null,
            $ligne['telephone'] ?? null,
            $ligne['photo_profil'] ?? null,
            $ligne['categorie_nom'],
            $ligne['categorie_icone'] ?? null,
            $ligne['region'],
            $ligne['departement'] ?? null,
            $ligne['ville'],
            $ligne['quartier'] ?? null,
            $ligne['description'] ?? null,
            $ligne['specialites'] ?? null,
            (int) $ligne['annees_experience'],
            $ligne['adresse'] ?? null,
            $ligne['whatsapp'] ?? null,
            $ligne['disponibilite'] ?? null
        );
    }
 
    private function creerProfilDepuisLigne(array $donnees): ProfilProfessionnel
    {
        return new ProfilProfessionnel(
            $donnees['id'],
            $donnees['utilisateur_id'],
            $donnees['categorie_id'],
            $donnees['localisation_id'],
            $donnees['description'] ?? null,
            $donnees['specialites'] ?? null,
            (int) $donnees['annees_experience'],
            $donnees['adresse'] ?? null,
            $donnees['whatsapp'] ?? null,
            $donnees['disponibilite'] ?? null,
            $donnees['statut_verification'],
            $donnees['date_creation'] ?? null,
            $donnees['date_modification'] ?? null
        );
    }
 
    /**
     * Récupère le profil professionnel (entité brute) d'un utilisateur donné.
     * Retourne null si ce professionnel n'a pas encore créé de profil.
     */
    public function findByUtilisateurId(int $utilisateurId): ?ProfilProfessionnel
    {
        $sql = "SELECT * FROM profils_professionnels WHERE utilisateur_id = ? LIMIT 1";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute([$utilisateurId]);
 
        $donnees = $stmt->fetch();
 
        if (!$donnees) {
            return null;
        }
 
        return $this->creerProfilDepuisLigne($donnees);
    }
 
    /**
     * Crée ou met à jour un profil professionnel.
     * - id === 0  → création (INSERT)
     * - id > 0    → modification (UPDATE)
     *
     * Retourne le profil persisté (avec son id, en cas de création).
     */
    public function save(ProfilProfessionnel $profil): ProfilProfessionnel
    {
        if ($profil->getId() === 0) {
 
            $sql = "
                INSERT INTO profils_professionnels
                (utilisateur_id, categorie_id, localisation_id, annees_experience,
                 description, specialites, adresse, whatsapp, disponibilite, statut_verification)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
 
            $stmt = $this->connexion->prepare($sql);
 
            $stmt->execute([
                $profil->getUtilisateurId(),
                $profil->getCategorieId(),
                $profil->getLocalisationId(),
                $profil->getAnneesExperience(),
                $profil->getDescription(),
                $profil->getSpecialites(),
                $profil->getAdresse(),
                $profil->getWhatsapp(),
                $profil->getDisponibilite(),
                $profil->getStatutVerification()
            ]);
 
            $nouvelId = (int) $this->connexion->lastInsertId();
 
            return new ProfilProfessionnel(
                $nouvelId,
                $profil->getUtilisateurId(),
                $profil->getCategorieId(),
                $profil->getLocalisationId(),
                $profil->getDescription(),
                $profil->getSpecialites(),
                $profil->getAnneesExperience(),
                $profil->getAdresse(),
                $profil->getWhatsapp(),
                $profil->getDisponibilite(),
                $profil->getStatutVerification()
            );
        }
 
        $sql = "
            UPDATE profils_professionnels
            SET
                categorie_id = ?,
                localisation_id = ?,
                annees_experience = ?,
                description = ?,
                specialites = ?,
                adresse = ?,
                whatsapp = ?,
                disponibilite = ?
            WHERE utilisateur_id = ?
        ";
 
        $stmt = $this->connexion->prepare($sql);
 
        $stmt->execute([
            $profil->getCategorieId(),
            $profil->getLocalisationId(),
            $profil->getAnneesExperience(),
            $profil->getDescription(),
            $profil->getSpecialites(),
            $profil->getAdresse(),
            $profil->getWhatsapp(),
            $profil->getDisponibilite(),
            $profil->getUtilisateurId()
        ]);
 
        return $profil;
    }
 
    public function countEnAttente(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM profils_professionnels WHERE statut_verification = 'en_attente'";
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute();
 
        return (int) $stmt->fetch()["total"];
    }
 
    /**
     * Liste tous les professionnels en attente de vérification, pour l'admin.
     *
     * @return ProfilProfessionnelEnAttente[]
     */
    public function findEnAttente(): array
    {
        $sql = "
            SELECT
                pp.id,
                pp.description,
                pp.specialites,
                pp.annees_experience,
                pp.whatsapp,
                pp.disponibilite,
 
                u.prenom,
                u.nom,
                u.photo_profil,
 
                c.nom AS categorie_nom,
                c.icone AS categorie_icone,
 
                l.ville,
                l.quartier
 
            FROM profils_professionnels pp
 
            INNER JOIN utilisateurs u ON u.id = pp.utilisateur_id
            INNER JOIN categories c ON c.id = pp.categorie_id
            INNER JOIN localisations l ON l.id = pp.localisation_id
 
            WHERE pp.statut_verification = 'en_attente'
 
            ORDER BY pp.date_creation ASC
        ";
 
        $stmt = $this->connexion->prepare($sql);
        $stmt->execute();
 
        $donnees = $stmt->fetchAll();
 
        $resultats = [];
 
        foreach ($donnees as $ligne) {
            $resultats[] = new ProfilProfessionnelEnAttente(
                $ligne['id'],
                $ligne['prenom'],
                $ligne['nom'],
                $ligne['photo_profil'] ?? null,
                $ligne['categorie_nom'],
                $ligne['categorie_icone'] ?? null,
                $ligne['ville'],
                $ligne['quartier'] ?? null,
                (int) $ligne['annees_experience'],
                $ligne['description'] ?? null,
                $ligne['specialites'] ?? null,
                $ligne['whatsapp'] ?? null,
                $ligne['disponibilite'] ?? null
            );
        }
 
        return $resultats;
    }
}
 