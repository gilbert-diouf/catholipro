<?php
    class Utilisateur{
        private int $id;
        private string $nom;
        private string $prenom;
        private string $email;
        private string $telephone;
        private string $motDePasse;
        private string $role;
        private string $statut;
        private ?string $photoProfil;
        private ?string $dateCreation;

        public function __construct(
            int $id, string $nom, string $prenom, string $email, string $telephone,string $motDePasse, string $role, string $statut = "actif", ?string $photoProfil = null, ?string $dateCreation = null){
            $this->id=$id;
            $this->nom=$nom;
            $this->prenom=$prenom;
            $this->email=$email;
            $this->telephone=$telephone;
            $this->motDePasse=$motDePasse;
            $this->role=$role;
            $this->statut=$statut;
            $this->photoProfil=$photoProfil;
            $this->dateCreation=$dateCreation;

        }

        public function getId(): int{
            return $this->id;
        }
        public function getNom(): string{
            return $this->nom;
        }
        public function getPrenom(): string{
            return $this->prenom;
        }
        public function getEmail(): string{
            return $this->email;
        }
        public function getTelephone(): string{
            return $this->telephone;
        }
        public function getRole(): string{
            return $this->role;
        }
        public function getMotDePasse(): string{
            return $this->motDePasse;
        }
        public function getStatut(): string{
            return $this->statut;
        }
        public function getPhotoProfil(): ?string{
            return $this->photoProfil;
        }
        public function getDateCreation(): ?string{
            return $this->dateCreation;
        }

    }
?>