<?php
 
    /**
     * DTO en lecture seule pour l'écran admin "Professionnels en attente".
     */
    class ProfilProfessionnelEnAttente
    {
        private int $id;
        private string $prenom;
        private string $nom;
        private ?string $photoProfil;
        private string $categorieNom;
        private ?string $categorieIcone;
        private string $ville;
        private ?string $quartier;
        private int $anneesExperience;
        private ?string $description;
        private ?string $specialites;
        private ?string $whatsapp;
        private ?string $disponibilite;
    
        public function __construct(
            int $id,
            string $prenom,
            string $nom,
            ?string $photoProfil,
            string $categorieNom,
            ?string $categorieIcone,
            string $ville,
            ?string $quartier,
            int $anneesExperience,
            ?string $description,
            ?string $specialites,
            ?string $whatsapp,
            ?string $disponibilite
        ) {
            $this->id = $id;
            $this->prenom = $prenom;
            $this->nom = $nom;
            $this->photoProfil = $photoProfil;
            $this->categorieNom = $categorieNom;
            $this->categorieIcone = $categorieIcone;
            $this->ville = $ville;
            $this->quartier = $quartier;
            $this->anneesExperience = $anneesExperience;
            $this->description = $description;
            $this->specialites = $specialites;
            $this->whatsapp = $whatsapp;
            $this->disponibilite = $disponibilite;
        }
    
        public function getId(): int 
        { 
            return $this->id; 
        }
        public function getPrenom(): string 
        { 
            return $this->prenom; 
        }
        public function getNom(): string 
        { 
            return $this->nom; 
        }
        public function getPhotoProfil(): ?string 
        { 
            return $this->photoProfil; 
        }
        public function getCategorieNom(): string 
        { 
            return $this->categorieNom; 
        }
        public function getCategorieIcone(): ?string 
        { 
            return $this->categorieIcone; 
        }
        public function getVille(): string 
        { 
            return $this->ville; 
        }
        public function getQuartier(): ?string 
        { 
            return $this->quartier; 
        }
        public function getAnneesExperience(): int 
        { 
            return $this->anneesExperience; 
        }
        public function getDescription(): ?string 
        { 
            return $this->description; 
        }
        public function getSpecialites(): ?string 
        { 
            return $this->specialites; 
        }
        public function getWhatsapp(): ?string 
        { 
            return $this->whatsapp; 
        }
        public function getDisponibilite(): ?string 
        { 
            return $this->disponibilite; 
        }
    }
?>
 