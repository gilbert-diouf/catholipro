<?php
 
require_once __DIR__ . '/../Services/UtilisateurService.php';
 
class UtilisateurController
{
    private UtilisateurService $service;
 
    public function __construct()
    {
        $this->service = new UtilisateurService();
    }
 
    /**
     * Traite une demande d'inscription.
     * Valide le format des champs (équivalent d'un @Valid sur un DTO),
     * puis délègue la règle métier au Service.
     *
     * @param array $donnees Les données brutes de $_POST
     * @return array ["succes" => bool, "message" => string]
     */
    public function inscrire(array $donnees): array
    {
        $nom = trim($donnees["nom"] ?? "");
        $prenom = trim($donnees["prenom"] ?? "");
        $email = trim($donnees["email"] ?? "");
        $telephone = trim($donnees["telephone"] ?? "");
        $motDePasse = $donnees["mot_de_passe"] ?? "";
        $role = $donnees["role"] ?? "professionnel";
 
        /*
        |--------------------------------------------------------------------------
        | Validation de forme (niveau HTTP / DTO)
        |--------------------------------------------------------------------------
        */
        if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($motDePasse)) {
            return ["succes" => false, "message" => "Veuillez remplir tous les champs obligatoires."];
        }
 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["succes" => false, "message" => "Veuillez saisir une adresse email valide."];
        }
 
        if (strlen($motDePasse) < 8) {
            return ["succes" => false, "message" => "Le mot de passe doit contenir au moins 8 caractères."];
        }
 
        if (!in_array($role, ["client", "professionnel"], true)) {
            return ["succes" => false, "message" => "Le type de compte sélectionné est invalide."];
        }
 
        /*
        |--------------------------------------------------------------------------
        | Délégation au Service (règles métier)
        |--------------------------------------------------------------------------
        */
        try {
 
            $this->service->inscrire($nom, $prenom, $email, $telephone, $motDePasse, $role);
 
            return [
                "succes" => true,
                "message" => "Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.",
            ];
 
        } catch (EmailDejaUtiliseException $e) {
 
            return ["succes" => false, "message" => $e->getMessage()];
        }
    }
 
    /**
     * Traite une demande de connexion.
     * Sur succès, ouvre la session et indique où rediriger.
     *
     * @param array $donnees Les données brutes de $_POST
     * @return array ["succes" => bool, "message" => string, "redirection" => string]
     */
    public function connecter(array $donnees): array
    {
        $email = trim($donnees["email"] ?? "");
        $motDePasse = $donnees["mot_de_passe"] ?? "";
 
        if (empty($email) || empty($motDePasse)) {
            return ["succes" => false, "message" => "Veuillez remplir tous les champs.", "redirection" => ""];
        }
 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["succes" => false, "message" => "Veuillez saisir une adresse email valide.", "redirection" => ""];
        }
 
        try {
 
            $utilisateur = $this->service->connecter($email, $motDePasse);
 
            session_regenerate_id(true);
 
            $_SESSION["utilisateur_id"] = $utilisateur->getId();
            $_SESSION["prenom"] = $utilisateur->getPrenom();
            $_SESSION["nom"] = $utilisateur->getNom();
            $_SESSION["email"] = $utilisateur->getEmail();
            $_SESSION["role"] = $utilisateur->getRole();
 
            $redirection = $utilisateur->getRole() === "administrateur"
                ? "admin/index.php"
                : "profil.php";
 
            return ["succes" => true, "message" => "", "redirection" => $redirection];
 
        } catch (IdentifiantsInvalidesException | CompteInactifException $e) {
 
            return ["succes" => false, "message" => $e->getMessage(), "redirection" => ""];
        }
    }
 
    /**
     * Récupère le profil d'un utilisateur connecté, pour affichage.
     */
    public function obtenirProfil(int $id): ?Utilisateur
    {
        return $this->service->obtenirParId($id);
    }
 
    public function compterTous(): int
    {
        return $this->service->compterTous();
    }
 
    public function compterParRole(string $role): int
    {
        return $this->service->compterParRole($role);
    }
 
    public function basculerStatut(int $utilisateurCibleId, int $utilisateurConnecteId): void
    {
        $this->service->basculerStatut($utilisateurCibleId, $utilisateurConnecteId);
    }
 
    /**
     * Liste les utilisateurs, filtrés par rôle si le paramètre GET est valide.
     * Un rôle absent ou invalide est traité comme "aucun filtre".
     *
     * @return Utilisateur[]
     */
    public function lister(array $filtres): array
    {
        $rolesValides = ["client", "professionnel", "administrateur"];
        $role = $filtres["role"] ?? "";
 
        $roleFiltre = in_array($role, $rolesValides, true) ? $role : null;
 
        return $this->service->lister($roleFiltre);
    }

    /**
     * Traite l'envoi d'une nouvelle photo de profil.
     */
    public function televerserPhoto(array $fichier, int $utilisateurId): array
    {
        $extensionsAutorisees = ["jpg", "jpeg", "png", "webp"];
        $typesMimeAutorises = ["image/jpeg", "image/png", "image/webp"];
        $tailleMaxOctets = 2 * 1024 * 1024; // 2 Mo
 
        if (empty($fichier["name"]) || ($fichier["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ["succes" => false, "message" => "Veuillez sélectionner une image."];
        }
 
        if ($fichier["error"] !== UPLOAD_ERR_OK) {
            return ["succes" => false, "message" => "Le téléversement a échoué, veuillez réessayer."];
        }
 
        if ($fichier["size"] > $tailleMaxOctets) {
            return ["succes" => false, "message" => "L'image ne doit pas dépasser 2 Mo."];
        }
 
        $extension = strtolower(pathinfo($fichier["name"], PATHINFO_EXTENSION));
 
        if (!in_array($extension, $extensionsAutorisees, true)) {
            return ["succes" => false, "message" => "Formats acceptés : JPG, PNG, WEBP."];
        }
 
        // Vérification que le fichier est une image réellement décodable (pas seulement son extension déclarée)
        $infosImage = @getimagesize($fichier["tmp_name"]);

        if ($infosImage === false || !in_array($infosImage["mime"], $typesMimeAutorises, true)) {
            return ["succes" => false, "message" => "Le fichier envoyé n'est pas une image valide."];
        }
        
        $dossierDestination = __DIR__ . "/../../uploads/photos";
 
        if (!is_dir($dossierDestination)) {
            mkdir($dossierDestination, 0755, true);
        }
 
        $nomFichier = "utilisateur_" . $utilisateurId . "_" . uniqid() . "." . $extension;
        $cheminAbsolu = $dossierDestination . "/" . $nomFichier;
        $cheminRelatif = "uploads/photos/" . $nomFichier;
 
        if (!move_uploaded_file($fichier["tmp_name"], $cheminAbsolu)) {
            return ["succes" => false, "message" => "Impossible d'enregistrer l'image, veuillez réessayer."];
        }
 
        $this->service->mettreAJourPhoto($utilisateurId, $cheminRelatif);
 
        return ["succes" => true, "message" => "Votre photo de profil a été mise à jour."];
    }
}
 