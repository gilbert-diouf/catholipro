<?php
 
    require_once __DIR__ . '/../Repositories/LocalisationRepository.php';
    
    class LocalisationService
    {
        private LocalisationRepository $repository;
    
        public function __construct()
        {
            $this->repository = new LocalisationRepository();
        }
    
        public function listerToutes(): array
        {
            return $this->repository->findAll();
        }
    
        public function obtenirParId(int $id): ?Localisation
        {
            return $this->repository->findById($id);
        }
    }
?>
 