<?php
 
    /**
     * Entité représentant une ligne de la table profils_professionnels.
     */
    class ProfilProfessionnel
    {
        private int $id;
        private int $utilisateurId;
        private int $categorieId;
        private int $localisationId;
        private ?string $description;
        private ?string $specialites;
        private int $anneesExperience;
        private ?string $adresse;
        private ?string $whatsapp;
        private ?string $disponibilite;
        private string $statutVerification;
        private ?string $dateCreation;
        private ?string $dateModification;
    
        public function __construct(
            int $id,
            int $utilisateurId,
            int $categorieId,
            int $localisationId,
            ?string $description,
            ?string $specialites,
            int $anneesExperience,
            ?string $adresse,
            ?string $whatsapp,
            ?string $disponibilite,
            string $statutVerification = "en_attente",
            ?string $dateCreation = null,
            ?string $dateModification = null
        ) {
            $this->id = $id;
            $this->utilisateurId = $utilisateurId;
            $this->categorieId = $categorieId;
            $this->localisationId = $localisationId;
            $this->description = $description;
            $this->specialites = $specialites;
            $this->anneesExperience = $anneesExperience;
            $this->adresse = $adresse;
            $this->whatsapp = $whatsapp;
            $this->disponibilite = $disponibilite;
            $this->statutVerification = $statutVerification;
            $this->dateCreation = $dateCreation;
            $this->dateModification = $dateModification;
        }
    
        public function getId(): int 
        { 
            return $this->id;
        }
        public function getUtilisateurId(): int 
        { 
            return $this->utilisateurId; 
        }
        public function getCategorieId(): int 
        { 
            return $this->categorieId; 
        }
        public function getLocalisationId(): int 
        { 
            return $this->localisationId; 
        }
        public function getDescription(): ?string 
        { 
            return $this->description; 
        }
        public function getSpecialites(): ?string 
        { 
            return $this->specialites; 
        }
        public function getAnneesExperience(): int 
        { 
            return $this->anneesExperience; 
        }
        public function getAdresse(): ?string 
        { 
            return $this->adresse; 
        }
        public function getWhatsapp(): ?string 
        { 
            return $this->whatsapp; 
        }
        public function getDisponibilite(): ?string 
        {
            return $this->disponibilite; 
        }
        public function getStatutVerification(): string 
        { 
            return $this->statutVerification; 
        }
        public function getDateCreation(): ?string 
        { 
            return $this->dateCreation; 
        }
        public function getDateModification(): ?string 
        { 
            return $this->dateModification; 
        }
    }
?>