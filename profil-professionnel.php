<?php
 
    session_start();
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    require_once "app/Controllers/ProfilProfessionnelController.php";
    
    $profilProfessionnelController = new ProfilProfessionnelController();
    
    
    /*
    |--------------------------------------------------------------------------
    | Récupérer l'identifiant du profil
    |--------------------------------------------------------------------------
    */
    
    $profil_id = filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );
    
    if (!$profil_id) {
        header("Location: professionnels.php");
        exit;
    }
    
    
    /*
    |--------------------------------------------------------------------------
    | Récupérer le professionnel
    |--------------------------------------------------------------------------
    */
    
    $professionnel = $profilProfessionnelController->obtenirDetail($profil_id);
    
    if (!$professionnel) {
        header("Location: professionnels.php");
        exit;
    }
 
?>
 
<!DOCTYPE html>
  <html lang="fr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($professionnel->getPrenom()) ?> <?= htmlspecialchars($professionnel->getNom()) ?> | ProCatho</title>
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
        <li><a href="professionnels.php" class="actif">Trouver un artisan</a></li>
        <li><a href="index.php#comment-ca-marche">Comment ça marche</a></li>
        <li><a href="index.php#a-propos">À propos</a></li>
      </ul>
  
      <div class="nav-actions">
        <?php if (isset($_SESSION["utilisateur_id"])) : ?>
          <a href="profil.php" class="btn btn-primaire">Mon espace</a>
        <?php else : ?>
          <a href="connexion.php" class="btn btn-primaire">Devenir Partenaire</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>
  
  
  <!-- ==========================================================
      FIL D'ARIANE
      ========================================================== -->
  <div class="container fil-ariane">
    <a href="index.php">Accueil</a> &gt;
    <a href="professionnels.php">Trouver un artisan</a> &gt;
    <strong><?= htmlspecialchars($professionnel->getPrenom()) ?> <?= htmlspecialchars($professionnel->getNom()) ?></strong>
  </div>
  
  
  <!-- ==========================================================
      PROFIL PROFESSIONNEL
      ========================================================== -->
  <main>
    <div class="container profil-grille">
  
      <!-- ================= Colonne gauche : contact ================= -->
      <aside class="profil-carte-contact">
  
        <?php if (!empty($professionnel->getPhotoProfil())): ?>
          <img
            class="profil-photo"
            src="<?= htmlspecialchars($professionnel->getPhotoProfil()) ?>"
            alt="Photo de <?= htmlspecialchars($professionnel->getPrenom()) ?>"
          >
        <?php else: ?>
          <div class="profil-photo-vide">👤</div>
        <?php endif; ?>
  
        <div class="profil-contact-corps">
  
          <div class="profil-nom">
            <?= htmlspecialchars($professionnel->getPrenom()) ?>
            <?= htmlspecialchars($professionnel->getNom()) ?>
            <span class="icone-verif-inline">✓</span>
          </div>
  
          <div class="profil-metier">
            <?php if (!empty($professionnel->getCategorieIcone())): ?>
              <?= htmlspecialchars($professionnel->getCategorieIcone()) ?>
            <?php endif; ?>
            <?= htmlspecialchars($professionnel->getCategorieNom()) ?>
          </div>
  
          <div class="profil-tags">
            <span class="profil-tag">
              📍 <?= htmlspecialchars($professionnel->getVille()) ?>
            </span>
            <span class="profil-tag">
              ⭐ <?= $professionnel->getAnneesExperience() ?> an(s) d'exp.
            </span>
          </div>
  
          <div class="profil-boutons">
            <?php if ($professionnel->getLienWhatsapp()): ?>
              <a
                href="<?= htmlspecialchars($professionnel->getLienWhatsapp()) ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-whatsapp"
              >
                📱 Contacter sur WhatsApp
              </a>
            <?php endif; ?>
  
            <?php if (!empty($professionnel->getTelephone())): ?>
              <a href="tel:<?= htmlspecialchars($professionnel->getTelephone()) ?>" class="btn btn-primaire">
                📞 Appeler (<?= htmlspecialchars($professionnel->getTelephone()) ?>)
              </a>
            <?php endif; ?>
          </div>
  
        </div>
  
        <div class="profil-gages">
          <h4>Gages de confiance</h4>
          <div class="gage-ligne">
            <span class="icone-verif-inline">✓</span> Identité vérifiée
          </div>
          <div class="gage-ligne">
            <span class="icone-verif-inline">✓</span> Membre ProCatho
          </div>
          <?php if (!empty($professionnel->getDisponibilite())): ?>
            <div class="gage-ligne">
              🟢 <?= htmlspecialchars($professionnel->getDisponibilite()) ?>
            </div>
          <?php endif; ?>
        </div>
  
      </aside>
  
  
      <!-- ================= Colonne droite : détails ================= -->
      <div>
  
        <!-- À propos -->
        <div class="profil-bloc">
          <h3>À propos</h3>
  
          <?php if (!empty($professionnel->getDescription())): ?>
            <p><?= nl2br(htmlspecialchars($professionnel->getDescription())) ?></p>
          <?php else: ?>
            <p>Ce professionnel n'a pas encore ajouté de description.</p>
          <?php endif; ?>
  
          <div class="profil-stats">
            <div>
              <strong><?= $professionnel->getAnneesExperience() ?>+</strong>
              <span>Années exp.</span>
            </div>
            <div>
              <strong>100%</strong>
              <span>Garantie</span>
            </div>
            <div>
              <strong>✓</strong>
              <span>Vérifié</span>
            </div>
          </div>
        </div>
  
        <!-- Spécialités -->
        <?php if (!empty($professionnel->getSpecialites())): ?>
          <div class="profil-bloc">
            <h3>Mes spécialités</h3>
            <p><?= nl2br(htmlspecialchars($professionnel->getSpecialites())) ?></p>
          </div>
        <?php endif; ?>
  
        <!-- Zone d'activité -->
        <div class="profil-bloc">
          <h3>Zone d'activité</h3>
          <p>
            📍 <?= htmlspecialchars($professionnel->getRegion()) ?>
            <?php if (!empty($professionnel->getDepartement())): ?>
              — <?= htmlspecialchars($professionnel->getDepartement()) ?>
            <?php endif; ?>
          </p>
          <p>
            <?= htmlspecialchars($professionnel->getVille()) ?>
            <?php if (!empty($professionnel->getQuartier())): ?>
              — <?= htmlspecialchars($professionnel->getQuartier()) ?>
            <?php endif; ?>
          </p>
          <?php if (!empty($professionnel->getAdresse())): ?>
            <p><?= htmlspecialchars($professionnel->getAdresse()) ?></p>
          <?php endif; ?>
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
 