<?php
 
/*
|--------------------------------------------------------------------------
| Exceptions métier liées à Utilisateur
|--------------------------------------------------------------------------
*/
 
class EmailDejaUtiliseException extends Exception
{
    public function __construct(string $message = "Cette adresse email est déjà utilisée.")
    {
        parent::__construct($message);
    }
}
 
class IdentifiantsInvalidesException extends Exception
{
    public function __construct(string $message = "Email ou mot de passe incorrect.")
    {
        parent::__construct($message);
    }
}
 
class CompteInactifException extends Exception
{
    public function __construct(string $message = "Votre compte n'est pas actif.")
    {
        parent::__construct($message);
    }
}
 