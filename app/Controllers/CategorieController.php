<?php
 
    require_once __DIR__ . '/../Services/CategorieService.php';
    
    class CategorieController
    {
        private CategorieService $service;
    
        public function __construct()
        {
            $this->service = new CategorieService();
        }
    
        /**
         * @return Categorie[]
         */
        public function listerActives(): array
        {
            return $this->service->listerActives();
        }
    }
?>
 