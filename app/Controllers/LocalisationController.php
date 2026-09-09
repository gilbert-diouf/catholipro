<?php
 
    require_once __DIR__ . '/../Services/LocalisationService.php';
    
    class LocalisationController
    {
        private LocalisationService $service;
    
        public function __construct()
        {
            $this->service = new LocalisationService();
        }
    
        /**
         * @return Localisation[]
         */
        public function listerToutes(): array
        {
            return $this->service->listerToutes();
        }
    }
?>
 