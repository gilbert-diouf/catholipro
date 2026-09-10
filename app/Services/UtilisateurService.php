<?php
 
require_once __DIR__ . '/../Repositories/UtilisateurRepository.php';
require_once __DIR__ . '/../Exceptions/UtilisateurExceptions.php';
 
class UtilisateurService
{
    private UtilisateurRepository $repository;
 
    public function __construct()
    {
        $this->repository = new UtilisateurRepository();
    }
 
    /**
     * Inscrit un nouvel utilisateur.
     * Vérifie l'unicité de l'email (règle métier), hache le mot de passe,
     * puis délègue la persistance au Repository.
     *
     * @throws EmailDejaUtiliseException si l'email est déjà pris
     */
    public function inscrire(
        string $nom,
        string $prenom,
        string $email,
        string $telephone,
        string $motDePasse,
        string $role
    ): Utilisateur {
 
        $utilisateurExistant = $this->repository->findByEmail($email);
 
        if ($utilisateurExistant !== null) {
            throw new EmailDejaUtiliseException();
        }
 
        $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);
 
        $nouvelUtilisateur = new Utilisateur(
            0,
            $nom,
            $prenom,
            $email,
            $telephone,
            $motDePasseHache,
            $role
        );
 
        $this->repository->save($nouvelUtilisateur);
 
        return $nouvelUtilisateur;
    }
 
    /**
     * Authentifie un utilisateur par email + mot de passe.
     *
     * @throws IdentifiantsInvalidesException si l'email est inconnu ou le mot de passe incorrect
     * @throws CompteInactifException si le compte n'est pas actif
     */
    public function connecter(string $email, string $motDePasse): Utilisateur
    {
        $utilisateur = $this->repository->findByEmail($email);
 
        if ($utilisateur === null) {
            throw new IdentifiantsInvalidesException();
        }
 
        if ($utilisateur->getStatut() !== "actif") {
            throw new CompteInactifException();
        }
 
        if (!password_verify($motDePasse, $utilisateur->getMotDePasse())) {
            throw new IdentifiantsInvalidesException();
        }
 
        return $utilisateur;
    }
 
    /**
     * Récupère un utilisateur par son identifiant.
     * Simple délégation au Repository : pas de règle métier ici.
     */
    public function obtenirParId(int $id): ?Utilisateur
    {
        return $this->repository->findById($id);
    }
 
    public function compterTous(): int
    {
        return $this->repository->count();
    }
 
    public function compterParRole(string $role): int
    {
        return $this->repository->countByRole($role);
    }
 
    /**
     * Suspend ou réactive un utilisateur.
     * Règle métier : un utilisateur ne peut pas basculer son propre statut
     * (ex. un administrateur qui se suspendrait lui-même).
     * Ne fait rien si la cible n'existe pas — comportement silencieux,
     * identique à un "no-op" plutôt qu'une erreur affichée.
     */
    public function basculerStatut(int $utilisateurCibleId, int $utilisateurConnecteId): void
    {
        if ($utilisateurCibleId === $utilisateurConnecteId) {
            return;
        }
 
        $utilisateur = $this->repository->findById($utilisateurCibleId);
 
        if ($utilisateur === null) {
            return;
        }
 
        $nouveauStatut = $utilisateur->getStatut() === "actif" ? "suspendu" : "actif";
 
        $this->repository->updateStatut($utilisateurCibleId, $nouveauStatut);
    }
 
    /**
     * Liste les utilisateurs, avec filtre optionnel par rôle.
     *
     * @return Utilisateur[]
     */
    public function lister(?string $role = null): array
    {
        return $this->repository->findAll($role);
    }
}
 