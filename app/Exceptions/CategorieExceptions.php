<?php
 
/*
|--------------------------------------------------------------------------
| Exceptions métier liées à Categorie
|--------------------------------------------------------------------------
*/
 
class CategorieUtiliseeException extends Exception
{
    public function __construct(string $message = "Impossible de supprimer ce métier : des professionnels y sont encore rattachés.")
    {
        parent::__construct($message);
    }
}