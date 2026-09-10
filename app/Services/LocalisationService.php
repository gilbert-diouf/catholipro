<?php
 
require_once __DIR__ . '/../Repositories/LocalisationRepository.php';
require_once __DIR__ . '/../Exceptions/LocalisationExceptions.php';
 
class LocalisationService
{
    private LocalisationRepository $repository;
 
    public function __construct()
    {
        $this->repository = new LocalisationRepository();
    }
 
    public function listerToutes(): array
    {
        return $this->repository->findAll();
    }
 
    public function obtenirParId(int $id): ?Localisation
    {
        return $this->repository->findById($id);
    }
 
    public function compterToutes(): int
    {
        return $this->repository->count();
    }
 
    public function ajouter(string $region, ?string $departement, string $ville, ?string $quartier): void
    {
        $this->repository->insert($region, $departement, $ville, $quartier);
    }
 
    /**
     * @throws LocalisationUtiliseeException si des professionnels y sont encore rattachés
     */
    public function supprimer(int $id): void
    {
        try {
 
            $this->repository->delete($id);
 
        } catch (PDOException $e) {
 
            throw new LocalisationUtiliseeException();
        }
    }
}
 