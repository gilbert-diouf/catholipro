<?php
 
/**
 * DTO en lecture seule pour la file de modération admin,
 * joint avec l'auteur ET le professionnel concerné (contexte nécessaire
 * pour que l'admin sache de quoi il juge la pertinence).
 */
class AvisModeration
{
    private int $id;
    private string $auteurPrenom;
    private string $auteurNom;
    private string $professionnelPrenom;
    private string $professionnelNom;
    private int $note;
    private ?string $commentaire;
    private ?string $dateCreation;
 
    public function __construct(
        int $id,
        string $auteurPrenom,
        string $auteurNom,
        string $professionnelPrenom,
        string $professionnelNom,
        int $note,
        ?string $commentaire,
        ?string $dateCreation
    ) {
        $this->id = $id;
        $this->auteurPrenom = $auteurPrenom;
        $this->auteurNom = $auteurNom;
        $this->professionnelPrenom = $professionnelPrenom;
        $this->professionnelNom = $professionnelNom;
        $this->note = $note;
        $this->commentaire = $commentaire;
        $this->dateCreation = $dateCreation;
    }
 
    public function getId(): int 
    { 
        return $this->id; 
    }
    public function getAuteurPrenom(): string 
    { 
        return $this->auteurPrenom; 
    }
    public function getAuteurNom(): string 
    { 
        return $this->auteurNom; 
    }
    public function getProfessionnelPrenom(): string 
    { 
        return $this->professionnelPrenom; 
    }
    public function getProfessionnelNom(): string 
    { 
        return $this->professionnelNom; 
    }
    public function getNote(): int 
    { 
        return $this->note; 
    }
    public function getCommentaire(): ?string 
    { 
        return $this->commentaire; 
    }
    public function getDateCreation(): ?string 
    { 
        return $this->dateCreation; 
    }
}