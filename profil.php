<?php
 
  session_start();
  
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  
  require_once "app/Controllers/UtilisateurController.php";
  
  $controller = new UtilisateurController();
  
  /*
  |--------------------------------------------------------------------------
  | Vérifier si l'utilisateur est connecté
  |--------------------------------------------------------------------------
  */
  
  if (!isset($_SESSION["utilisateur_id"])) {
  
      header("Location: connexion.php");
      exit;
  }
  
  
  /*
  |--------------------------------------------------------------------------
  | Récupérer l'utilisateur connecté
  |--------------------------------------------------------------------------
  */
  
  $utilisateur = $controller->obtenirProfil($_SESSION["utilisateur_id"]);
  
  
  /*
  |--------------------------------------------------------------------------
  | Vérifier que l'utilisateur existe toujours
  |--------------------------------------------------------------------------
  */
  
  if (!$utilisateur) {
  
      session_unset();
      session_destroy();
  
      header("Location: connexion.php");
      exit;
  }
  
  
  /*
  |--------------------------------------------------------------------------
  | Initiales pour l'avatar
  |--------------------------------------------------------------------------
  */
  
  $initiales = mb_strtoupper(
      mb_substr($utilisateur->getPrenom(), 0, 1) . mb_substr($utilisateur->getNom(), 0, 1)
  );
 
?>
 
<!DOCTYPE html>
<html lang="fr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon profil | ProCatho</title>
  <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
  
  <!-- ==========================================================
      EN-TÊTE / NAVIGATION
      ========================================================== -->
  <header class="entete">
    <nav class="nav container">
      <a href="index.php" class="nav-logo">
        <img src="assets/images/logo.jpeg" alt="Logo ProCatho">
        ProCatho
      </a>
  
      <ul class="nav-liens">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="professionnels.php">Trouver un artisan</a></li>
        <li><a href="index.php#comment-ca-marche">Comment ça marche</a></li>
        <li><a href="index.php#a-propos">À propos</a></li>
      </ul>
  
      <div class="nav-actions">
        <a href="profil.php" class="btn btn-primaire">Mon espace</a>
      </div>
    </nav>
  </header>
  
  
  <!-- ==========================================================
      MON COMPTE
      ========================================================== -->
  <main class="page-compte">
    <div class="container">
  
      <div class="compte-carte">
  
        <div class="compte-entete">
  
          <?php if (!empty($utilisateur->getPhotoProfil())): ?>
            <img
              class="compte-avatar"
              src="<?= htmlspecialchars($utilisateur->getPhotoProfil()) ?>"
              alt="Photo de <?= htmlspecialchars($utilisateur->getPrenom()) ?>"
            >
          <?php else: ?>
            <div class="compte-avatar"><?= htmlspecialchars($initiales) ?></div>
          <?php endif; ?>
  
          <div>
            <h2>Bienvenue, <?= htmlspecialchars($utilisateur->getPrenom()) ?></h2>
            <p>Voici les informations de votre compte.</p>
          </div>
  
        </div>
  
  
        <div class="compte-grille">
  
          <div class="compte-champ">
            <strong>Prénom</strong>
            <p><?= htmlspecialchars($utilisateur->getPrenom()) ?></p>
          </div>
  
          <div class="compte-champ">
            <strong>Nom</strong>
            <p><?= htmlspecialchars($utilisateur->getNom()) ?></p>
          </div>
  
          <div class="compte-champ">
            <strong>Email</strong>
            <p><?= htmlspecialchars($utilisateur->getEmail()) ?></p>
          </div>
  
          <div class="compte-champ">
            <strong>Téléphone</strong>
            <p>
              <?= !empty($utilisateur->getTelephone())
                  ? htmlspecialchars($utilisateur->getTelephone())
                  : "Non renseigné"
              ?>
            </p>
          </div>
  
          <div class="compte-champ">
            <strong>Type de compte</strong>
            <p><?= htmlspecialchars(ucfirst($utilisateur->getRole())) ?></p>
          </div>
  
          <div class="compte-champ">
            <strong>Statut</strong>
            <p>
              <span class="compte-badge-statut">
                <?= htmlspecialchars(ucfirst($utilisateur->getStatut())) ?>
              </span>
            </p>
          </div>
  
        </div>
  
  
        <div class="compte-actions">
  
          <?php if ($utilisateur->getRole() === "professionnel"): ?>
            <a href="professionnel/mon-profil.php" class="btn btn-or">Gérer mon profil professionnel</a>
          <?php endif; ?>
  
          <?php if ($utilisateur->getRole() === "administrateur"): ?>
            <a href="admin/index.php" class="btn btn-or">Espace administrateur</a>
          <?php endif; ?>
  
          <a href="deconnexion.php" class="btn btn-primaire">Se déconnecter</a>
  
        </div>
  
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
          <img src="assets/images/logo.jpeg" alt="Logo ProCatho">
          ProCatho
        </div>
        <p>La foi au service des talents.</p>
      </div>
  
      <div class="pied-col">
        <h4>Navigation</h4>
        <ul>
          <li><a href="index.php">Accueil</a></li>
          <li><a href="professionnels.php">Artisans</a></li>
          <li><a href="inscription.php">Devenir Partenaire</a></li>
        </ul>
      </div>
  
      <div class="pied-col">
        <h4>Valeurs</h4>
        <ul>
          <li><a href="index.php#a-propos">Confiance Mutuelle</a></li>
          <li><a href="index.php#a-propos">Prix Juste</a></li>
          <li><a href="index.php#a-propos">Bienveillance</a></li>
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