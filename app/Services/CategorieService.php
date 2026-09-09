<?php
 
require_once __DIR__ . '/../Repositories/CategorieRepository.php';
 
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
}
 