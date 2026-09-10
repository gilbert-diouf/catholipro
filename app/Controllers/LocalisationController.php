<?php
 
require_once __DIR__ . '/../Services/LocalisationService.php';
 
class LocalisationController
{
    private LocalisationService $service;
 
    public function __construct()
    {
        $this->service = new LocalisationService();
    }
 
    /**
     * @return Localisation[]
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
     * @return array ["succes" => bool, "message" => string]
     */
    public function ajouter(array $donnees): array
    {
        $region = trim($donnees["region"] ?? "");
        $departement = trim($donnees["departement"] ?? "");
        $ville = trim($donnees["ville"] ?? "");
        $quartier = trim($donnees["quartier"] ?? "");
 
        if (empty($region) || empty($ville)) {
            return ["succes" => false, "message" => "La région et la ville sont obligatoires."];
        }
 
        $this->service->ajouter($region, $departement ?: null, $ville, $quartier ?: null);
 
        return ["succes" => true, "message" => "La localisation a été ajoutée avec succès."];
    }
 
    /**
     * @return array ["succes" => bool, "message" => string]
     */
    public function supprimer(int $id): array
    {
        try {
 
            $this->service->supprimer($id);
 
            return ["succes" => true, "message" => "La localisation a été supprimée."];
 
        } catch (LocalisationUtiliseeException $e) {
 
            return ["succes" => false, "message" => $e->getMessage()];
        }
    }
}
 