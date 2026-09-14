<?php
 
require_once __DIR__ . '/../Services/AvisService.php';
 
class AvisController
{
    private AvisService $service;
 
    public function __construct()
    {
        $this->service = new AvisService();
    }
 
    /**
     * Traite la soumission (création ou modification) d'un avis.
     *
     * @return array ["succes" => bool, "message" => string]
     */
    public function soumettre(array $donnees, int $utilisateurId): array
    {
        $profilProfessionnelId = $donnees["profil_professionnel_id"] ?? "";
        $note = $donnees["note"] ?? "";
        $commentaire = trim($donnees["commentaire"] ?? "");
 
        /*
        |--------------------------------------------------------------------------
        | Validation de forme
        |--------------------------------------------------------------------------
        */
        if (empty($profilProfessionnelId) || !is_numeric($profilProfessionnelId)) {
            return ["succes" => false, "message" => "Professionnel invalide."];
        }
 
        if ($note === "" || !is_numeric($note) || (int) $note < 1 || (int) $note > 5) {
            return ["succes" => false, "message" => "Veuillez sélectionner une note entre 1 et 5."];
        }
 
        /*
        |--------------------------------------------------------------------------
        | Délégation au Service
        |--------------------------------------------------------------------------
        */
        try {
 
            $this->service->soumettre(
                $utilisateurId,
                (int) $profilProfessionnelId,
                (int) $note,
                $commentaire !== "" ? $commentaire : null
            );
 
            return ["succes" => true, "message" => "Merci ! Votre avis a été soumis et sera visible après modération."];
 
        } catch (RoleInvalidePourAvisException | ProfilProfessionnelIntrouvableException $e) {
 
            return ["succes" => false, "message" => $e->getMessage()];
        }
    }
 
    public function obtenirAvisUtilisateur(int $utilisateurId, int $profilProfessionnelId): ?Avis
    {
        return $this->service->obtenirAvisUtilisateur($utilisateurId, $profilProfessionnelId);
    }
 
    /**
     * @return AvisAffichage[]
     */
    public function listerPublies(int $profilProfessionnelId): array
    {
        return $this->service->listerPublies($profilProfessionnelId);
    }
 
    /**
     * @return array{moyenne: float, total: int}
     */
    public function obtenirStatistiques(int $profilProfessionnelId): array
    {
        return $this->service->obtenirStatistiques($profilProfessionnelId);
    }
 
    /**
     * @return AvisModeration[]
     */
    public function listerEnAttente(): array
    {
        return $this->service->listerEnAttente();
    }
 
    /**
     * @return AvisModeration[]
     */
    public function listerTousPublies(): array
    {
        return $this->service->listerTousPublies();
    }
 
    public function compterEnAttente(): int
    {
        return $this->service->compterEnAttente();
    }
 
    public function publier(int $id): void
    {
        $this->service->publier($id);
    }
 
    public function rejeter(int $id): void
    {
        $this->service->rejeter($id);
    }
 
    /**
     * @return array ["succes" => bool, "message" => string]
     */
    public function supprimer(int $id): array
    {
        $this->service->supprimer($id);
 
        return ["succes" => true, "message" => "L'avis a été supprimé."];
    }
}