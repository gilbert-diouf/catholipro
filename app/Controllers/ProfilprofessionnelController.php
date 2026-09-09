<?php
 
    require_once __DIR__ . '/../Services/ProfilProfessionnelService.php';
    
    class ProfilProfessionnelController
    {
        private ProfilProfessionnelService $service;
    
        public function __construct()
        {
            $this->service = new ProfilProfessionnelService();
        }
    
        /**
         * Traite les paramètres de recherche ($_GET) et retourne les résultats.
         *
         * @return ProfilProfessionnelRecherche[]
         */
        public function rechercher(array $filtres): array
        {
            $categorieId = !empty($filtres["categorie_id"]) ? (int) $filtres["categorie_id"] : null;
            $localisationId = !empty($filtres["localisation_id"]) ? (int) $filtres["localisation_id"] : null;
    
            return $this->service->rechercher($categorieId, $localisationId);
        }
    
        /**
         * Récupère la fiche détaillée d'un professionnel, pour affichage public.
         */
        public function obtenirDetail(int $id): ?ProfilProfessionnelDetail
        {
            return $this->service->obtenirDetail($id);
        }
    
        /**
         * Récupère le profil professionnel existant d'un utilisateur (pour pré-remplir le formulaire).
         */
        public function obtenirParUtilisateur(int $utilisateurId): ?ProfilProfessionnel
        {
            return $this->service->obtenirParUtilisateur($utilisateurId);
        }
    
        /**
         * Traite la soumission du formulaire "Mon profil professionnel".
         * Valide le format des champs, puis délègue la règle métier au Service.
         *
         * @return array ["succes" => bool, "message" => string, "profil" => ?ProfilProfessionnel]
         */
        public function enregistrer(array $donnees, int $utilisateurId): array
        {
            $categorieId = $donnees["categorie_id"] ?? "";
            $localisationId = $donnees["localisation_id"] ?? "";
            $anneesExperience = $donnees["annees_experience"] ?? "";
            $description = trim($donnees["description"] ?? "");
            $specialites = trim($donnees["specialites"] ?? "");
            $adresse = trim($donnees["adresse"] ?? "");
            $whatsapp = trim($donnees["whatsapp"] ?? "");
            $disponibilite = trim($donnees["disponibilite"] ?? "");
    
            /*
            |--------------------------------------------------------------------------
            | Validation de forme (niveau HTTP / DTO)
            |--------------------------------------------------------------------------
            */
            if (empty($categorieId) || empty($localisationId) || $anneesExperience === "") {
                return ["succes" => false, "message" => "Veuillez remplir tous les champs obligatoires.", "profil" => null];
            }
    
            if (!is_numeric($categorieId) || !is_numeric($localisationId)) {
                return ["succes" => false, "message" => "Les informations sélectionnées sont invalides.", "profil" => null];
            }
    
            if (!is_numeric($anneesExperience) || $anneesExperience < 0 || $anneesExperience > 60) {
                return ["succes" => false, "message" => "Le nombre d'années d'expérience est invalide.", "profil" => null];
            }
    
            /*
            |--------------------------------------------------------------------------
            | Délégation au Service (règles métier)
            |--------------------------------------------------------------------------
            */
            $profilExistaitDeja = $this->service->obtenirParUtilisateur($utilisateurId) !== null;
    
            try {
    
                $profil = $this->service->creerOuMettreAJour(
                    $utilisateurId,
                    (int) $categorieId,
                    (int) $localisationId,
                    (int) $anneesExperience,
                    $description ?: null,
                    $specialites ?: null,
                    $adresse ?: null,
                    $whatsapp ?: null,
                    $disponibilite ?: null
                );
    
                $message = $profilExistaitDeja
                    ? "Votre profil professionnel a été mis à jour."
                    : "Votre profil professionnel a été créé avec succès.";
    
                return ["succes" => true, "message" => $message, "profil" => $profil];
    
            } catch (CategorieInvalideException | LocalisationInvalideException $e) {
    
                return ["succes" => false, "message" => $e->getMessage(), "profil" => null];
            }
        }
    }
?>