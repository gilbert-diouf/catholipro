<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../app/Controllers/ProfilProfessionnelController.php";
require_once "../app/Controllers/CategorieController.php";
require_once "../app/Controllers/LocalisationController.php";
require_once "../app/Security/Csrf.php";
 
 
/*
|--------------------------------------------------------------------------
| Vérifier la connexion
|--------------------------------------------------------------------------
*/
 
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: ../connexion.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Vérifier le rôle
|--------------------------------------------------------------------------
*/
 
if ($_SESSION["role"] !== "professionnel") {
    header("Location: ../profil.php");
    exit;
}
 
 
$profilProfessionnelController = new ProfilProfessionnelController();
$categorieController = new CategorieController();
$localisationController = new LocalisationController();
 
$utilisateur_id = $_SESSION["utilisateur_id"];
 
$erreur = "";
$succes = "";
 
 
/*
|--------------------------------------------------------------------------
| Charger le profil existant (pour pré-remplir le formulaire)
|--------------------------------------------------------------------------
*/
 
$profil = $profilProfessionnelController->obtenirParUtilisateur($utilisateur_id);
 
$categorie_id = $profil ? $profil->getCategorieId() : "";
$localisation_id = $profil ? $profil->getLocalisationId() : "";
$annees_experience = $profil ? $profil->getAnneesExperience() : "";
$description = $profil ? $profil->getDescription() : "";
$specialites = $profil ? $profil->getSpecialites() : "";
$adresse = $profil ? $profil->getAdresse() : "";
$whatsapp = $profil ? $profil->getWhatsapp() : "";
$disponibilite = $profil ? $profil->getDisponibilite() : "";
 
 
/*
|--------------------------------------------------------------------------
| Traitement du formulaire
|--------------------------------------------------------------------------
*/
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    // Conserver les saisies pour le réaffichage
    $categorie_id = $_POST["categorie_id"] ?? "";
    $localisation_id = $_POST["localisation_id"] ?? "";
    $annees_experience = $_POST["annees_experience"] ?? "";
    $description = trim($_POST["description"] ?? "");
    $specialites = trim($_POST["specialites"] ?? "");
    $adresse = trim($_POST["adresse"] ?? "");
    $whatsapp = trim($_POST["whatsapp"] ?? "");
    $disponibilite = trim($_POST["disponibilite"] ?? "");
 
    $resultat = null;
 
    if (!Csrf::verifier($_POST["csrf_token"] ?? null)) {
 
        $erreur = "Votre session a expiré, veuillez réessayer.";
 
    } else {
 
        $resultat = $profilProfessionnelController->enregistrer($_POST, $utilisateur_id);
 
        if ($resultat["succes"]) {
            $succes = $resultat["message"];
            $profil = $resultat["profil"]; // pour que le bouton affiche "Mettre à jour" dès maintenant
        } else {
            $erreur = $resultat["message"];
        }
    }
}
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les catégories et localisations (pour les listes déroulantes)
|--------------------------------------------------------------------------
*/
 
$categories = $categorieController->listerActives();
$localisations = $localisationController->listerToutes();
 
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon profil professionnel | ProCatho</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 
<!-- ==========================================================
     EN-TÊTE / NAVIGATION
     ========================================================== -->
<header class="entete">
  <nav class="nav container">
    <a href="../index.php" class="nav-logo">
      <img src="../assets/images/logo.jpeg" alt="Logo ProCatho">
      ProCatho
    </a>
 
    <ul class="nav-liens">
      <li><a href="../index.php">Accueil</a></li>
      <li><a href="../professionnels.php">Trouver un artisan</a></li>
      <li><a href="../index.php#comment-ca-marche">Comment ça marche</a></li>
      <li><a href="../index.php#a-propos">À propos</a></li>
    </ul>
 
    <div class="nav-actions">
      <a href="../profil.php" class="btn btn-primaire">Mon espace</a>
    </div>
  </nav>
</header>
 
 
<!-- ==========================================================
     FORMULAIRE PROFIL PROFESSIONNEL
     ========================================================== -->
<main class="page-formulaire">
  <div class="container">
 
    <div class="formulaire-carte">
 
      <h2>Mon profil professionnel</h2>
      <p>Présentez votre activité à la communauté.</p>
 
 
      <!-- Statut de vérification -->
      <?php if ($profil): ?>
 
        <?php if ($profil->getStatutVerification() === "verifie"): ?>
          <div class="statut-profil-banniere statut-verifie">
            ✓ Votre profil est vérifié et visible dans l'annuaire public.
          </div>
        <?php elseif ($profil->getStatutVerification() === "rejete"): ?>
          <div class="statut-profil-banniere statut-rejete">
            ✕ Votre profil a été rejeté. Corrigez vos informations ci-dessous : votre profil repassera automatiquement en attente de vérification.
          </div>
        <?php else: ?>
          <div class="statut-profil-banniere statut-attente">
            🕐 Votre profil est en attente de vérification par un administrateur.
          </div>
        <?php endif; ?>
 
      <?php endif; ?>
 
 
      <!-- Message erreur -->
      <?php if (!empty($erreur)): ?>
        <div class="message-erreur">
          <?= htmlspecialchars($erreur) ?>
        </div>
      <?php endif; ?>
 
 
      <!-- Message succès -->
      <?php if (!empty($succes)): ?>
        <div class="message-succes">
          <?= htmlspecialchars($succes) ?>
        </div>
      <?php endif; ?>
 
 
      <form method="POST">
 
        <?= Csrf::champCache() ?>
 
        <!-- Métier -->
        <div class="formulaire-groupe">
          <label for="categorie_id">Métier</label>
          <select id="categorie_id" name="categorie_id" required>
            <option value="">Sélectionnez votre métier</option>
            <?php foreach ($categories as $categorie): ?>
              <option
                value="<?= $categorie->getId() ?>"
                <?= $categorie_id == $categorie->getId() ? "selected" : "" ?>
              >
                <?= htmlspecialchars($categorie->getNom()) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
 
 
        <!-- Localisation -->
        <div class="formulaire-groupe">
          <label for="localisation_id">Localisation</label>
          <select id="localisation_id" name="localisation_id" required>
            <option value="">Sélectionnez votre localisation</option>
            <?php foreach ($localisations as $localisation): ?>
              <option
                value="<?= $localisation->getId() ?>"
                <?= $localisation_id == $localisation->getId() ? "selected" : "" ?>
              >
                <?= htmlspecialchars($localisation->getLibelle()) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
 
 
        <!-- Expérience -->
        <div class="formulaire-groupe">
          <label for="annees_experience">Années d'expérience</label>
          <input
            type="number"
            id="annees_experience"
            name="annees_experience"
            min="0"
            max="60"
            value="<?= htmlspecialchars($annees_experience) ?>"
            placeholder="Exemple : 5"
            required
          >
        </div>
 
 
        <!-- Description -->
        <div class="formulaire-groupe">
          <label for="description">Présentez votre activité</label>
          <textarea
            id="description"
            name="description"
            rows="5"
            placeholder="Décrivez votre activité, votre expérience et les services que vous proposez..."
          ><?= htmlspecialchars($description ?? "") ?></textarea>
        </div>
 
 
        <!-- Spécialités -->
        <div class="formulaire-groupe">
          <label for="specialites">Spécialités</label>
          <textarea
            id="specialites"
            name="specialites"
            rows="4"
            placeholder="Exemple : construction, rénovation, carrelage..."
          ><?= htmlspecialchars($specialites ?? "") ?></textarea>
        </div>
 
 
        <!-- Adresse -->
        <div class="formulaire-groupe">
          <label for="adresse">Adresse</label>
          <input
            type="text"
            id="adresse"
            name="adresse"
            value="<?= htmlspecialchars($adresse ?? "") ?>"
            placeholder="Votre adresse ou zone d'activité"
          >
        </div>
 
 
        <!-- WhatsApp -->
        <div class="formulaire-groupe">
          <label for="whatsapp">Numéro WhatsApp</label>
          <input
            type="tel"
            id="whatsapp"
            name="whatsapp"
            value="<?= htmlspecialchars($whatsapp ?? "") ?>"
            placeholder="77 000 00 00"
          >
        </div>
 
 
        <!-- Disponibilité -->
        <div class="formulaire-groupe">
          <label for="disponibilite">Disponibilité</label>
          <input
            type="text"
            id="disponibilite"
            name="disponibilite"
            value="<?= htmlspecialchars($disponibilite ?? "") ?>"
            placeholder="Exemple : Disponible du lundi au samedi"
          >
        </div>
 
 
        <!-- Bouton -->
        <button type="submit" class="btn btn-primaire">
          <?= $profil ? "Mettre à jour mon profil" : "Créer mon profil" ?>
        </button>
 
      </form>
 
    </div>
 
  </div>
</main>
 
 
<!-- ==========================================================
     PIED DE PAGE
     ========================================================== -->
<footer class="pied">
  <div class="container pied-grille">
    <div class="pied-col">
      <div class="pied-logo">
        <img src="../assets/images/logo.jpeg" alt="Logo ProCatho">
        ProCatho
      </div>
      <p>La foi au service des talents.</p>
    </div>
 
    <div class="pied-col">
      <h4>Navigation</h4>
      <ul>
        <li><a href="../index.php">Accueil</a></li>
        <li><a href="../professionnels.php">Artisans</a></li>
        <li><a href="../inscription.php">Devenir Partenaire</a></li>
      </ul>
    </div>
 
    <div class="pied-col">
      <h4>Valeurs</h4>
      <ul>
        <li><a href="../index.php#a-propos">Confiance Mutuelle</a></li>
        <li><a href="../index.php#a-propos">Prix Juste</a></li>
        <li><a href="../index.php#a-propos">Bienveillance</a></li>
      </ul>
    </div>
 
    <div class="pied-col">
      <h4>Contact</h4>
      <ul>
        <li><a href="mailto:contact@procatho.sn">contact@procatho.sn</a></li>
      </ul>
    </div>
  </div>
 
  <p class="pied-bas">&copy; <?php echo date("Y"); ?> ProCatho. Tous droits réservés.</p>
</footer>
 
</body>
</html>