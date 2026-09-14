<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

?>
<!DOCTYPE html>
  <html lang="fr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ProCatho — Trouvez des professionnels catholiques de confiance</title>
  <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
  
  <!-- ==========================================================
      EN-TÊTE / NAVIGATION
      ========================================================== -->
  <header class="entete">
    <nav class="nav container">
      <a href="index.php" class="nav-logo">
        <img src="assets/images/logo.png" alt="Logo ChristianConnect">
        ChristianConnect
      </a>
  
      <ul class="nav-liens">
        <li><a href="index.php" class="actif">Accueil</a></li>
        <li><a href="professionnels.php">Trouver un artisan</a></li>
        <li><a href="#comment-ca-marche">Comment ça marche</a></li>
        <li><a href="#a-propos">À propos</a></li>
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
      SECTION HÉRO
      ========================================================== -->
  <section class="hero">
    <div class="container hero-grille">
      <div class="hero-contenu">
        <p class="hero-etiquette">Excellence &amp; Confiance</p>
        <h1 class="hero-titre">
          Trouvez des professionnels <span class="accent">catholiques</span>
          de confiance au Sénégal.
        </h1>
        <p class="hero-texte">
          Connectez-vous avec des artisans qualifiés qui partagent vos valeurs.
          Des profils vérifiés pour tous vos projets de construction, rénovation
          et création.
        </p>
  
        <form class="recherche-barre" action="professionnels.php" method="get">
          <div class="recherche-champ">
            <span>🔍</span>
            <input type="text" name="metier" placeholder="Métier, service (ex : Menuisier)">
          </div>
          <div class="recherche-champ">
            <span>📍</span>
            <input type="text" name="ville" placeholder="Ville, région">
          </div>
          <button type="submit" class="btn btn-primaire">Rechercher</button>
        </form>
  
        <div class="confiance-sociale">
          <div class="confiance-avatars">
            <span></span><span></span><span></span>
          </div>
          <p>Plus de 2000 professionnels font déjà confiance à ProCatho.</p>
        </div>
      </div>
  
      <div class="hero-visuel">
        <img src="assets/images/hero-artisan.jpg" alt="Artisan catholique au travail" class="hero-image">
        <div class="hero-badge">
          <div class="icone-verif">✓</div>
          <div>
            <strong>Profil Vérifié</strong>
            <span>Identité &amp; qualifications confirmées</span>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- ==========================================================
      COMMENT ÇA MARCHE
      ========================================================== -->
  <section class="section" id="comment-ca-marche">
    <div class="container">
      <div class="section-titre">
        <p class="hero-etiquette">Simple &amp; Efficace</p>
        <h2>Comment ça marche</h2>
        <p>Trois étapes simples pour trouver le bon professionnel.</p>
      </div>
  
      <div class="etapes">
        <div class="etape-carte">
          <div class="etape-numero">1</div>
          <h3>Choisissez un métier</h3>
          <p>Sélectionnez le corps de métier correspondant à votre besoin.</p>
        </div>
        <div class="etape-carte">
          <div class="etape-numero">2</div>
          <h3>Parcourez les profils</h3>
          <p>Consultez les artisans vérifiés près de chez vous, avec avis et expérience.</p>
        </div>
        <div class="etape-carte">
          <div class="etape-numero">3</div>
          <h3>Entrez en contact</h3>
          <p>Contactez directement le professionnel par téléphone ou WhatsApp.</p>
        </div>
      </div>
    </div>
  </section>
  
  <!-- ==========================================================
      CORPS DE MÉTIER
      ========================================================== -->
  <section class="section metiers">
    <div class="container">
      <div class="section-titre">
        <p class="hero-etiquette">Nos catégories</p>
        <h2>Quel est votre besoin ?</h2>
        <p>Sélectionnez le corps de métier correspondant à votre projet.</p>
      </div>
  
      <div class="metiers-grille">
        <a href="professionnels.php?metier=plomberie" class="metier-carte">
          <div class="metier-icone">🔧</div>
          <span>Plomberie</span>
        </a>
        <a href="professionnels.php?metier=electricite" class="metier-carte">
          <div class="metier-icone">⚡</div>
          <span>Électricité</span>
        </a>
        <a href="professionnels.php?metier=menuiserie" class="metier-carte">
          <div class="metier-icone">🪚</div>
          <span>Menuiserie</span>
        </a>
        <a href="professionnels.php?metier=peinture" class="metier-carte">
          <div class="metier-icone">🎨</div>
          <span>Peinture</span>
        </a>
        <a href="professionnels.php?metier=couverture" class="metier-carte">
          <div class="metier-icone">🏠</div>
          <span>Couverture</span>
        </a>
        <a href="professionnels.php" class="metier-carte">
          <div class="metier-icone">⋯</div>
          <span>Autre</span>
        </a>
      </div>
    </div>
  </section>
  
  <!-- ==========================================================
      CHARTE PROCATHO
      ========================================================== -->
  <section class="section charte" id="a-propos">
    <div class="container">
      <div class="section-titre">
        <p class="hero-etiquette">Confiance &amp; Charité</p>
        <h2>La Charte ProCatho</h2>
      </div>
  
      <p class="charte-citation">
        « Que tout ce que vous faites soit fait avec amour. »
      </p>
  
      <div class="valeurs-grille">
        <div class="valeur-carte">
          <div class="valeur-icone">✓</div>
          <h3>Professionnels Vérifiés</h3>
          <p>Chaque professionnel s'engage à respecter nos valeurs d'honnêteté et de travail bien fait.</p>
        </div>
        <div class="valeur-carte">
          <div class="valeur-icone">⚖</div>
          <h3>Prix Juste</h3>
          <p>Un engagement mutuel sur une tarification transparente et équitable.</p>
        </div>
        <div class="valeur-carte">
          <div class="valeur-icone">🤝</div>
          <h3>Bienveillance</h3>
          <p>Les échanges se font dans le respect et l'entraide fraternelle.</p>
        </div>
      </div>
    </div>
  </section>
  
  <!-- ==========================================================
      APPEL À L'ACTION
      ========================================================== -->
  <section class="section cta">
    <div class="container">
      <h2>Vous êtes un professionnel catholique ?</h2>
      <p>Rejoignez ProCatho et faites connaître votre savoir-faire.</p>
      <div class="cta-boutons">
        <a href="inscription.php" class="btn btn-primaire">Devenir Partenaire</a>
        <a href="professionnels.php" class="btn btn-or">Trouver un artisan</a>
      </div>
    </div>
  </section>
  
  <!-- ==========================================================
      PIED DE PAGE
      ========================================================== -->
  <footer class="pied">
    <div class="container pied-grille">
      <div class="pied-col">
        <div class="pied-logo">
          <img src="assets/images/logo.png" alt="Logo ChristianConnect">
          ChristianConnect
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
          <li><a href="#a-propos">Confiance Mutuelle</a></li>
          <li><a href="#a-propos">Prix Juste</a></li>
          <li><a href="#a-propos">Bienveillance</a></li>
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