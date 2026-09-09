<?php
 
  session_start();
  
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  
  require_once "app/Controllers/CategorieController.php";
  require_once "app/Controllers/LocalisationController.php";
  require_once "app/Controllers/ProfilProfessionnelController.php";
  
  $categorieController = new CategorieController();
  $localisationController = new LocalisationController();
  $profilProfessionnelController = new ProfilProfessionnelController();
  
  
  /*
  |--------------------------------------------------------------------------
  | Récupération des filtres
  |--------------------------------------------------------------------------
  */
  
  $categorie_id = $_GET["categorie_id"] ?? "";
  $localisation_id = $_GET["localisation_id"] ?? "";
  
  
  /*
  |--------------------------------------------------------------------------
  | Données de la page (catégories, localisations, résultats de recherche)
  |--------------------------------------------------------------------------
  */
  
  $categories = $categorieController->listerActives();
  $localisations = $localisationController->listerToutes();
  $professionnels = $profilProfessionnelController->rechercher($_GET);
  
?>
 
<!DOCTYPE html>
  <html lang="fr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trouver un artisan | ProCatho</title>
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
      EN-TÊTE DE PAGE
      ========================================================== -->
  <section class="page-entete">
    <div class="container">
      <h1>Trouver un artisan</h1>
      <p>Recherchez des professionnels catholiques de confiance près de chez vous.</p>
    </div>
  </section>
  
  
  <!-- ==========================================================
      BARRE DE RECHERCHE (filtres conservés : categorie_id / localisation_id)
      ========================================================== -->
  <section class="recherche-page">
    <div class="container">
      <form method="GET">
  
        <div class="recherche-champ">
          <label for="categorie_id">Métier</label>
          <select id="categorie_id" name="categorie_id">
            <option value="">Tous les métiers</option>
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
  
        <div class="recherche-champ">
          <label for="localisation_id">Localisation</label>
          <select id="localisation_id" name="localisation_id">
            <option value="">Toutes les localisations</option>
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
  
        <button type="submit" class="btn btn-primaire">🔎 Rechercher</button>
  
      </form>
    </div>
  </section>
  
  
  <!-- ==========================================================
      CORPS : SIDEBAR FILTRES RAPIDES + RÉSULTATS
      ========================================================== -->
  <main>
    <div class="container corps-recherche">
  
      <!-- Sidebar filtres rapides par métier -->
      <aside>
        <div class="artisan-carte" style="padding: 20px;">
          <h3 style="margin-bottom: 14px; font-size: 1rem;">Filtrer par métier</h3>
          <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
            <li>
              <a href="professionnels.php" style="font-size: 0.9rem; <?= empty($categorie_id) ? 'color: var(--bleu-marine); font-weight: 700;' : 'color: var(--gris-texte);' ?>">
                Tous les métiers
              </a>
            </li>
            <?php foreach ($categories as $categorie): ?>
              <li>
                <a
                  href="professionnels.php?categorie_id=<?= $categorie->getId() ?><?= !empty($localisation_id) ? '&localisation_id=' . $localisation_id : '' ?>"
                  style="font-size: 0.9rem; <?= $categorie_id == $categorie->getId() ? 'color: var(--bleu-marine); font-weight: 700;' : 'color: var(--gris-texte);' ?>"
                >
                  <?= htmlspecialchars($categorie->getNom()) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </aside>
  
      <!-- Résultats -->
      <div>
  
        <div class="resultats-en-tete">
          <h2>Professionnels disponibles</h2>
          <span><?= count($professionnels) ?> professionnel(s) trouvé(s)</span>
        </div>
  
        <?php if (empty($professionnels)): ?>
  
          <div class="carte-vide">
            <h3>Aucun professionnel trouvé</h3>
            <p>Aucun professionnel vérifié ne correspond actuellement à votre recherche.</p>
          </div>
  
        <?php else: ?>
  
          <div class="grille-artisans">
  
            <?php foreach ($professionnels as $professionnel): ?>
  
              <article class="artisan-carte">
  
                <div class="artisan-photo-bloc">
                  <img
                    class="artisan-avatar"
                    src="<?= !empty($professionnel->getPhotoProfil()) ? htmlspecialchars($professionnel->getPhotoProfil()) : 'assets/images/avatar-defaut.png' ?>"
                    alt="Photo de <?= htmlspecialchars($professionnel->getPrenom()) ?>"
                  >
                  <span class="artisan-badge-verif">✓ Vérifié</span>
                </div>
  
                <div class="artisan-corps">
  
                  <div class="artisan-nom">
                    <?= htmlspecialchars($professionnel->getPrenom()) ?>
                    <?= htmlspecialchars($professionnel->getNom()) ?>
                  </div>
  
                  <div class="artisan-metier">
                    <?php if (!empty($professionnel->getCategorieIcone())): ?>
                      <?= htmlspecialchars($professionnel->getCategorieIcone()) ?>
                    <?php endif; ?>
                    <?= htmlspecialchars($professionnel->getCategorieNom()) ?>
                  </div>
  
                  <div class="artisan-info-ligne">
                    📍
                    <?= htmlspecialchars($professionnel->getVille()) ?>
                    <?php if (!empty($professionnel->getQuartier())): ?>
                      - <?= htmlspecialchars($professionnel->getQuartier()) ?>
                    <?php endif; ?>
                  </div>
  
                  <div class="artisan-info-ligne">
                    ⭐ <?= $professionnel->getAnneesExperience() ?> an(s) d'expérience
                  </div>
  
                  <?php if (!empty($professionnel->getDescription())): ?>
                    <p class="artisan-description">
                      <?= htmlspecialchars($professionnel->getDescription()) ?>
                    </p>
                  <?php endif; ?>
  
                  <?php if (!empty($professionnel->getSpecialites())): ?>
                    <p class="artisan-specialites">
                      <strong>Spécialités :</strong>
                      <?= htmlspecialchars($professionnel->getSpecialites()) ?>
                    </p>
                  <?php endif; ?>
  
                  <a
                    href="profil-professionnel.php?id=<?= $professionnel->getId() ?>"
                    class="btn btn-primaire"
                  >
                    Voir le profil
                  </a>
  
                </div>
  
              </article>
  
            <?php endforeach; ?>
  
          </div>
  
        <?php endif; ?>
  
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