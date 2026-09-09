<?php
 

    class ProfilProfessionnelDetail
    {
        private int $id;
        private string $prenom;
        private string $nom;
        private ?string $email;
        private ?string $telephone;
        private ?string $photoProfil;
        private string $categorieNom;
        private ?string $categorieIcone;
        private string $region;
        private ?string $departement;
        private string $ville;
        private ?string $quartier;
        private ?string $description;
        private ?string $specialites;
        private int $anneesExperience;
        private ?string $adresse;
        private ?string $whatsapp;
        private ?string $disponibilite;
    
        public function __construct(
            int $id,
            string $prenom,
            string $nom,
            ?string $email,
            ?string $telephone,
            ?string $photoProfil,
            string $categorieNom,
            ?string $categorieIcone,
            string $region,
            ?string $departement,
            string $ville,
            ?string $quartier,
            ?string $description,
            ?string $specialites,
            int $anneesExperience,
            ?string $adresse,
            ?string $whatsapp,
            ?string $disponibilite
        ) {
            $this->id = $id;
            $this->prenom = $prenom;
            $this->nom = $nom;
            $this->email = $email;
            $this->telephone = $telephone;
            $this->photoProfil = $photoProfil;
            $this->categorieNom = $categorieNom;
            $this->categorieIcone = $categorieIcone;
            $this->region = $region;
            $this->departement = $departement;
            $this->ville = $ville;
            $this->quartier = $quartier;
            $this->description = $description;
            $this->specialites = $specialites;
            $this->anneesExperience = $anneesExperience;
            $this->adresse = $adresse;
            $this->whatsapp = $whatsapp;
            $this->disponibilite = $disponibilite;
        }
    
        public function getId(): int { return $this->id; }
        public function getPrenom(): string { return $this->prenom; }
        public function getNom(): string { return $this->nom; }
        public function getEmail(): ?string { return $this->email; }
        public function getTelephone(): ?string { return $this->telephone; }
        public function getPhotoProfil(): ?string { return $this->photoProfil; }
        public function getCategorieNom(): string { return $this->categorieNom; }
        public function getCategorieIcone(): ?string { return $this->categorieIcone; }
        public function getRegion(): string { return $this->region; }
        public function getDepartement(): ?string { return $this->departement; }
        public function getVille(): string { return $this->ville; }
        public function getQuartier(): ?string { return $this->quartier; }
        public function getDescription(): ?string { return $this->description; }
        public function getSpecialites(): ?string { return $this->specialites; }
        public function getAnneesExperience(): int { return $this->anneesExperience; }
        public function getAdresse(): ?string { return $this->adresse; }
        public function getWhatsapp(): ?string { return $this->whatsapp; }
        public function getDisponibilite(): ?string { return $this->disponibilite; }
    
        /**
         * Numéro WhatsApp nettoyé (chiffres et + uniquement), prêt pour wa.me
         */
        public function getWhatsappNettoye(): ?string
        {
            if (empty($this->whatsapp)) {
                return null;
            }
    
            return preg_replace("/[^0-9+]/", "", $this->whatsapp);
        }
    
        /**
         * Lien complet vers la conversation WhatsApp, message pré-rempli inclus.
         * Retourne null si aucun numéro WhatsApp n'est renseigné.
         */
        public function getLienWhatsapp(): ?string
        {
            $numero = $this->getWhatsappNettoye();
    
            if (empty($numero)) {
                return null;
            }
    
            $message = urlencode(
                "Bonjour " . $this->prenom .
                ", je vous contacte via ProCatho concernant vos services de " .
                $this->categorieNom . "."
            );
    
            return "https://wa.me/" . ltrim($numero, "+") . "?text=" . $message;
        }
    }
?>
 