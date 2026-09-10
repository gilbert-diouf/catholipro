<?php
 
/*
|--------------------------------------------------------------------------
| Exceptions métier liées à Localisation
|--------------------------------------------------------------------------
*/
 
class LocalisationUtiliseeException extends Exception
{
    public function __construct(string $message = "Impossible de supprimer cette localisation : des professionnels y sont encore rattachés.")
    {
        parent::__construct($message);
    }
}