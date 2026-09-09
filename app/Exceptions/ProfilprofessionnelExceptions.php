<?php
 
    /*
    |--------------------------------------------------------------------------
    | Exceptions métier liées à ProfilProfessionnel
    |--------------------------------------------------------------------------
    */
    
    class CategorieInvalideException extends Exception
    {
        public function __construct(string $message = "Le métier sélectionné est invalide.")
        {
            parent::__construct($message);
        }
    }
    
    class LocalisationInvalideException extends Exception
    {
        public function __construct(string $message = "La localisation sélectionnée est invalide.")
        {
            parent::__construct($message);
        }
    }
?>
 