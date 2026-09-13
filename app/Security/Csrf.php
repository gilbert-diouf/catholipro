<?php
 
/**
 * Protection CSRF (Cross-Site Request Forgery).
 * Génère un jeton unique par session, à inclure dans chaque formulaire
 * sensible, et à vérifier avant tout traitement de soumission.
 */
class Csrf
{
    private const CLE_SESSION = "csrf_token";
 
    /**
     * Retourne le jeton CSRF de la session en cours, en le générant
     * s'il n'existe pas encore.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::CLE_SESSION])) {
            $_SESSION[self::CLE_SESSION] = bin2hex(random_bytes(32));
        }
 
        return $_SESSION[self::CLE_SESSION];
    }
 
    /**
     * Génère le champ HTML caché à insérer dans chaque formulaire POST.
     */
    public static function champCache(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token()) . '">';
    }
 
    /**
     * Vérifie qu'un jeton reçu correspond à celui de la session.
     * Utilise hash_equals() pour une comparaison à temps constant
     * (protection contre les attaques par timing).
     */
    public static function verifier(?string $tokenRecu): bool
    {
        if (empty($tokenRecu) || empty($_SESSION[self::CLE_SESSION])) {
            return false;
        }
 
        return hash_equals($_SESSION[self::CLE_SESSION], $tokenRecu);
    }
}
