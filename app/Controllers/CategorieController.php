<?php
 
require_once __DIR__ . '/../Services/CategorieService.php';
 
class CategorieController
{
    private CategorieService $service;
 
    public function __construct()
    {
        $this->service = new CategorieService();
    }
 
    /**
     * @return Categorie[]
     */
    public function listerActives(): array
    {
        return $this->service->listerActives();
    }
 
    /**
     * @return Categorie[]
     */
    public function listerToutes(): array
    {
        return $this->service->listerToutes();
    }
 
    public function compterToutes(): int
    {
        return $this->service->compterToutes();
    }
 
    /**
     * Traite l'ajout d'un métier depuis le formulaire admin.
     *
     * @return array ["succes" => bool, "message" => string]
     */
    public function ajouter(array $donnees): array
    {
        $nom = trim($donnees["nom"] ?? "");
        $icone = trim($donnees["icone"] ?? "");
 
        if (empty($nom)) {
            return ["succes" => false, "message" => "Le nom du métier est obligatoire."];
        }
 
        $this->service->ajouter($nom, $icone);
 
        return ["succes" => true, "message" => "Le métier a été ajouté avec succès."];
    }
 
    public function basculerStatut(int $id): void
    {
        $this->service->basculerStatut($id);
    }
 
    /**
     * @return array ["succes" => bool, "message" => string]
     */
    public function supprimer(int $id): array
    {
        try {
 
            $this->service->supprimer($id);
 
            return ["succes" => true, "message" => "Le métier a été supprimé."];
 
        } catch (CategorieUtiliseeException $e) {
 
            return ["succes" => false, "message" => $e->getMessage()];
        }
    }
}
 