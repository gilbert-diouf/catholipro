<?php
 
require_once __DIR__ . '/../Repositories/AvisRepository.php';
require_once __DIR__ . '/../Exceptions/AvisExceptions.php';
require_once __DIR__ . '/UtilisateurService.php';
require_once __DIR__ . '/ProfilProfessionnelService.php';
 
class AvisService
{
    private AvisRepository $repository;
    private UtilisateurService $utilisateurService;
    private ProfilProfessionnelService $profilProfessionnelService;
 
    public function __construct()
    {
        $this->repository = new AvisRepository();
        $this->utilisateurService = new UtilisateurService();
        $this->profilProfessionnelService = new ProfilProfessionnelService();
    }
 
    /**
     * Soumet un nouvel avis, ou met à jour l'avis existant de cet
     * utilisateur pour ce professionnel (un seul avis par paire).
     * Toute soumission (nouvelle ou modifiée) repart en modération.
     *
     * @throws RoleInvalidePourAvisException si l'auteur n'est pas un client
     * @throws ProfilProfessionnelIntrouvableException si le professionnel ciblé n'existe pas / n'est pas vérifié
     */
    public function soumettre(int $utilisateurId, int $profilProfessionnelId, int $note, ?string $commentaire): Avis
    {
        $utilisateur = $this->utilisateurService->obtenirParId($utilisateurId);
 
        if ($utilisateur === null || $utilisateur->getRole() !== "client") {
            throw new RoleInvalidePourAvisException();
        }
 
        $profil = $this->profilProfessionnelService->obtenirDetail($profilProfessionnelId);
 
        if ($profil === null) {
            throw new ProfilProfessionnelIntrouvableException();
        }
 
        $avisExistant = $this->repository->findByUtilisateurEtProfil($utilisateurId, $profilProfessionnelId);
 
        $avis = new Avis(
            $avisExistant ? $avisExistant->getId() : 0,
            $profilProfessionnelId,
            $utilisateurId,
            $note,
            $commentaire,
            "en_attente"
        );
 
        return $this->repository->save($avis);
    }
 
    public function obtenirAvisUtilisateur(int $utilisateurId, int $profilProfessionnelId): ?Avis
    {
        return $this->repository->findByUtilisateurEtProfil($utilisateurId, $profilProfessionnelId);
    }
 
    /**
     * @return AvisAffichage[]
     */
    public function listerPublies(int $profilProfessionnelId): array
    {
        return $this->repository->findPubliesByProfil($profilProfessionnelId);
    }
 
    /**
     * @return array{moyenne: float, total: int}
     */
    public function obtenirStatistiques(int $profilProfessionnelId): array
    {
        return $this->repository->calculerStatistiques($profilProfessionnelId);
    }
 
    /**
     * @return AvisModeration[]
     */
    public function listerEnAttente(): array
    {
        return $this->repository->findEnAttente();
    }
 
    /**
     * @return AvisModeration[]
     */
    public function listerTousPublies(): array
    {
        return $this->repository->findPublies();
    }
 
    public function compterEnAttente(): int
    {
        return $this->repository->countEnAttente();
    }
 
    public function publier(int $id): void
    {
        $this->repository->updateStatutModeration($id, "publie");
    }
 
    public function rejeter(int $id): void
    {
        $this->repository->updateStatutModeration($id, "rejete");
    }
 
    public function supprimer(int $id): void
    {
        $this->repository->delete($id);
    }
}