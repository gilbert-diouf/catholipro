<?php
 
class Localisation
{
    private int $id;
    private string $region;
    private ?string $departement;
    private string $ville;
    private ?string $quartier;
 
    public function __construct(int $id, string $region, ?string $departement, string $ville, ?string $quartier)
    {
        $this->id = $id;
        $this->region = $region;
        $this->departement = $departement;
        $this->ville = $ville;
        $this->quartier = $quartier;
    }
 
    public function getId(): int
    {
        return $this->id;
    }
 
    public function getRegion(): string
    {
        return $this->region;
    }
 
    public function getDepartement(): ?string
    {
        return $this->departement;
    }
 
    public function getVille(): string
    {
        return $this->ville;
    }
 
    public function getQuartier(): ?string
    {
        return $this->quartier;
    }
 
    /**
     * Libellé affichable, ex: "Pikine - Guinaw Rails"
     */
    public function getLibelle(): string
    {
        return $this->quartier
            ? $this->ville . " - " . $this->quartier
            : $this->ville;
    }
}
 