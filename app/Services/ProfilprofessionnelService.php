<?php
 
require_once __DIR__ . '/../Repositories/ProfilProfessionnelRepository.php';
require_once __DIR__ . '/../Exceptions/ProfilProfessionnelExceptions.php';
require_once __DIR__ . '/CategorieService.php';
require_once __DIR__ . '/LocalisationService.php';
 
class ProfilProfessionnelService
{
    private ProfilProfessionnelRepository $repository;
    private CategorieService $categorieService;
    private LocalisationService $localisationService;
 
    public function __construct()
    {
        $this->repository = new ProfilProfessionnelRepository();
        $this->categorieService = new CategorieService();
        $this->localisationService = new LocalisationService();
    }
 
    /**
     * Recherche les professionnels vérifiés, filtrés par métier et/ou localisation.
     *
     * @return ProfilProfessionnelRecherche[]
     */
    public function rechercher(?int $categorieId, ?int $localisationId): array
    {
        return $this->repository->rechercher($categorieId, $localisationId);
    }
 
    /**
     * Récupère la fiche détaillée d'un professionnel pour la page publique.
     */
    public function obtenirDetail(int $id): ?ProfilProfessionnelDetail
    {
        return $this->repository->findDetailById($id);
    }
 
    /**
     * Récupère le profil professionnel (entité brute) d'un utilisateur,
     * pour pré-remplir le formulaire de gestion.
     */
    public function obtenirParUtilisateur(int $utilisateurId): ?ProfilProfessionnel
    {
        return $this->repository->findByUtilisateurId($utilisateurId);
    }
 
    /**
     * Crée le profil professionnel de l'utilisateur s'il n'en a pas,
     * ou le met à jour s'il en a déjà un.
     *
     * @throws CategorieInvalideException si la catégorie n'existe pas / n'est pas active
     * @throws LocalisationInvalideException si la localisation n'existe pas
     */
    public function creerOuMettreAJour(
        int $utilisateurId,
        int $categorieId,
        int $localisationId,
        int $anneesExperience,
        ?string $description,
        ?string $specialites,
        ?string $adresse,
        ?string $whatsapp,
        ?string $disponibilite
    ): ProfilProfessionnel {
 
        if ($this->categorieService->obtenirParId($categorieId) === null) {
            throw new CategorieInvalideException();
        }
 
        if ($this->localisationService->obtenirParId($localisationId) === null) {
            throw new LocalisationInvalideException();
        }
 
        $profilExistant = $this->repository->findByUtilisateurId($utilisateurId);
 
        $profil = new ProfilProfessionnel(
            $profilExistant ? $profilExistant->getId() : 0,
            $utilisateurId,
            $categorieId,
            $localisationId,
            $description,
            $specialites,
            $anneesExperience,
            $adresse,
            $whatsapp,
            $disponibilite,
            $profilExistant ? $profilExistant->getStatutVerification() : "en_attente"
        );
 
        return $this->repository->save($profil);
    }
 
    public function compterEnAttente(): int
    {
        return $this->repository->countEnAttente();
    }
 
    /**
     * @return ProfilProfessionnelEnAttente[]
     */
    public function listerEnAttente(): array
    {
        return $this->repository->findEnAttente();
    }
}
 