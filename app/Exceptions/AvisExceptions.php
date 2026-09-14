<?php
 
/*
|--------------------------------------------------------------------------
| Exceptions métier liées à Avis
|--------------------------------------------------------------------------
*/
 
class RoleInvalidePourAvisException extends Exception
{
    public function __construct(string $message = "Seuls les clients peuvent laisser un avis.")
    {
        parent::__construct($message);
    }
}
 
class ProfilProfessionnelIntrouvableException extends Exception
{
    public function __construct(string $message = "Ce professionnel n'existe pas ou n'est pas encore vérifié.")
    {
        parent::__construct($message);
    }
}
