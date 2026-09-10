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
     * Valide le format des champs ,
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
}
 