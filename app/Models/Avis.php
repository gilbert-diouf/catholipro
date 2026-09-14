<?php
 
class Avis
{
    private int $id;
    private int $profilProfessionnelId;
    private int $utilisateurId;
    private int $note;
    private ?string $commentaire;
    private string $statutModeration;
    private ?string $dateCreation;
    private ?string $dateModification;
 
    public function __construct(
        int $id,
        int $profilProfessionnelId,
        int $utilisateurId,
        int $note,
        ?string $commentaire,
        string $statutModeration = "en_attente",
        ?string $dateCreation = null,
        ?string $dateModification = null
    ) {
        $this->id = $id;
        $this->profilProfessionnelId = $profilProfessionnelId;
        $this->utilisateurId = $utilisateurId;
        $this->note = $note;
        $this->commentaire = $commentaire;
        $this->statutModeration = $statutModeration;
        $this->dateCreation = $dateCreation;
        $this->dateModification = $dateModification;
    }
 
    public function getId(): int 
    { 
        return $this->id;
    }
    public function getProfilProfessionnelId(): int 
    { 
        return $this->profilProfessionnelId; 
    }
    public function getUtilisateurId(): int 
    { 
        return $this->utilisateurId; 
    }
    public function getNote(): int 
    { 
        return $this->note; 
    }
    public function getCommentaire(): ?string 
    { 
        return $this->commentaire; 
    }
    public function getStatutModeration(): string 
    { 
        return $this->statutModeration; 
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