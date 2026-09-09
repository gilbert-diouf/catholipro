<?php

    class Categorie
     {
        private int $id;
        private string $nom;
        private ?string $icone;
        private ?string $statut;

        public function __construct(int $id, string $nom, ?string $icone, ?string $statut = "actif")
        {
            $this->id = $id;
            $this->nom = $nom;
            $this->icone = $icone;
            $this->statut = $statut;
        }

        public function getId(): int 
        {
            return $this->id;
        }
        public function getNom(): string 
        {
            return $this->nom;
        }
        public function getIcone(): ?string 
        {
            return $this->icone;
        }
        public function getStatut(): ?string
        {
            return $this->statut;
        }
    }

?>