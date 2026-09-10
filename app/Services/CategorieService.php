<?php
 
require_once __DIR__ . '/../Repositories/CategorieRepository.php';
require_once __DIR__ . '/../Exceptions/CategorieExceptions.php';
 
class CategorieService
{
    private CategorieRepository $repository;
 
    public function __construct()
    {
        $this->repository = new CategorieRepository();
    }
 
    /**
     * Liste les catégories actives, pour alimenter les filtres de recherche.
     */
    public function listerActives(): array
    {
        return $this->repository->findAllActives();
    }
 
    public function obtenirParId(int $id): ?Categorie
    {
        return $this->repository->findById($id);
    }
 
    /**
     * @return Categorie[]
     */
    public function listerToutes(): array
    {
        return $this->repository->findAll();
    }
 
    public function compterToutes(): int
    {
        return $this->repository->count();
    }
 
    public function ajouter(string $nom, ?string $icone): void
    {
        $this->repository->insert($nom, $icone);
    }
 
    /**
     * Active ou désactive une catégorie. Ne fait rien si elle n'existe pas.
     */
    public function basculerStatut(int $id): void
    {
        $categorie = $this->repository->findByIdAny($id);
 
        if ($categorie === null) {
            return;
        }
 
        $nouveauStatut = $categorie->getStatut() === "actif" ? "inactif" : "actif";
 
        $this->repository->updateStatut($id, $nouveauStatut);
    }
 
    /**
     * Supprime une catégorie.
     *
     * @throws CategorieUtiliseeException si des professionnels y sont encore rattachés
     */
    public function supprimer(int $id): void
    {
        try {
 
            $this->repository->delete($id);
 
        } catch (PDOException $e) {
 
            throw new CategorieUtiliseeException();
        }
    }
}
 