<?php
 
/**
 * DTO en lecture seule pour l'affichage public d'un avis publié
 * (page profil-professionnel.php), joint avec le prénom/nom de l'auteur.
 */
class AvisAffichage
{
    private int $id;
    private string $auteurPrenom;
    private string $auteurNom;
    private int $note;
    private ?string $commentaire;
    private ?string $dateCreation;
 
    public function __construct(
        int $id,
        string $auteurPrenom,
        string $auteurNom,
        int $note,
        ?string $commentaire,
        ?string $dateCreation
    ) {
        $this->id = $id;
        $this->auteurPrenom = $auteurPrenom;
        $this->auteurNom = $auteurNom;
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