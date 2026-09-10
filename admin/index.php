<?php
 
  session_start();
  
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  
  require_once "../app/Controllers/UtilisateurController.php";
  require_once "../app/Controllers/ProfilProfessionnelController.php";
  
  
  /*
  |--------------------------------------------------------------------------
  | PROTECTION DE L'ADMINISTRATION
  |--------------------------------------------------------------------------
  */
  
  if (
      !isset($_SESSION["utilisateur_id"]) ||
      !isset($_SESSION["role"]) ||
      $_SESSION["role"] !== "administrateur"
  ) {
  
      header("Location: ../connexion.php");
      exit;
  }
  
  $utilisateurController = new UtilisateurController();
  $profilProfessionnelController = new ProfilProfessionnelController();
  
  
  /*
  |--------------------------------------------------------------------------
  | Statistiques du tableau de bord
  |--------------------------------------------------------------------------
  */
  
  $total_utilisateurs = $utilisateurController->compterTous();
  $total_professionnels = $utilisateurController->compterParRole("professionnel");
  $total_en_attente = $profilProfessionnelController->compterEnAttente();
  
  
  /*
  |--------------------------------------------------------------------------
  | Récupérer les professionnels en attente
  |--------------------------------------------------------------------------
  */
  
  $professionnels = $profilProfessionnelController->listerEnAttente();
  
?>
 
<!DOCTYPE html>
<html lang="fr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration | ProCatho</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  </head>
  <body>
  
  <!-- ==========================================================
      MISE EN PAGE ADMIN AVEC SIDEBAR
      ========================================================== -->
  <div class="admin-layout">
  
    <!-- ================= Sidebar ================= -->
    <aside class="admin-sidebar">
  
      <div class="admin-sidebar-logo">
        <img src="../assets/images/logo.jpeg" alt="Logo ProCatho">
        ProCatho
      </div>
  
      <ul class="admin-nav" id="admin-nav">
        <li><a href="index.php" data-cible="dashboard"><span class="admin-nav-icone">📊</span> Tableau de bord</a></li>
        <li><a href="index.php#professionnels-attente" data-cible="professionnels-attente"><span class="admin-nav-icone">🛠️</span> Professionnels</a></li>
        <li><a href="utilisateurs.php"><span class="admin-nav-icone">👥</span> Utilisateurs</a></li>
        <li><a href="categories.php"><span class="admin-nav-icone">🏷️</span> Catégories</a></li>
        <li><a href="localisations.php"><span class="admin-nav-icone">📍</span> Localisations</a></li>
      </ul>
  
      <div class="admin-sidebar-bas">
        <a href="../profil.php" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:var(--rayon); color:rgba(255,255,255,0.75); font-size:0.9rem;">
          <span class="admin-nav-icone">👤</span> Mon espace
        </a>
        <a href="../deconnexion.php" style="display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:var(--rayon); color:rgba(255,255,255,0.75); font-size:0.9rem;">
          <span class="admin-nav-icone">🚪</span> Déconnexion
        </a>
      </div>
  
    </aside>
  
  
    <!-- ================= Contenu ================= -->
    <main class="admin-main">
  
      <div class="admin-en-tete">
        <div>
          <h2>Tableau de bord</h2>
          <p>Vue d'ensemble et gestion des professionnels de ProCatho.</p>
        </div>
      </div>
  
  
      <div class="admin-stats">
  
        <div class="stat-carte">
          <div class="stat-icone">👥</div>
          <div>
            <div class="stat-valeur"><?= $total_utilisateurs ?></div>
            <div class="stat-libelle">Utilisateurs</div>
          </div>
        </div>
  
        <div class="stat-carte stat-vert">
          <div class="stat-icone">🛠️</div>
          <div>
            <div class="stat-valeur"><?= $total_professionnels ?></div>
            <div class="stat-libelle">Professionnels</div>
          </div>
        </div>
  
        <div class="stat-carte stat-or">
          <div class="stat-icone">⏳</div>
          <div>
            <div class="stat-valeur"><?= $total_en_attente ?></div>
            <div class="stat-libelle">En attente</div>
          </div>
        </div>
  
      </div>
  
  
      <div class="admin-en-tete" id="professionnels-attente">
        <div>
          <h3>Professionnels en attente</h3>
          <p>Vérifiez et validez les nouveaux profils avant leur publication.</p>
        </div>
        <span class="admin-compteur"><?= count($professionnels) ?> en attente</span>
      </div>
  
  
      <?php if (empty($professionnels)): ?>
  
        <div class="carte-vide">
          <h3>Aucun profil en attente</h3>
          <p>Tous les professionnels ont été traités.</p>
        </div>
  
      <?php else: ?>
  
        <div class="liste-attente">
  
          <?php foreach ($professionnels as $professionnel): ?>
  
            <article class="attente-carte">
  
              <div class="attente-entete">
  
                <?php if (!empty($professionnel->getPhotoProfil())): ?>
                  <img
                    class="attente-photo"
                    src="<?= htmlspecialchars($professionnel->getPhotoProfil()) ?>"
                    alt="Photo de <?= htmlspecialchars($professionnel->getPrenom()) ?>"
                  >
                <?php else: ?>
                  <div class="attente-photo">👤</div>
                <?php endif; ?>
  
                <div class="attente-identite">
                  <h3>
                    <?= htmlspecialchars($professionnel->getPrenom()) ?>
                    <?= htmlspecialchars($professionnel->getNom()) ?>
                  </h3>
                  <div class="attente-metier">
                    <?php if (!empty($professionnel->getCategorieIcone())): ?>
                      <?= htmlspecialchars($professionnel->getCategorieIcone()) ?>
                    <?php endif; ?>
                    <?= htmlspecialchars($professionnel->getCategorieNom()) ?>
                  </div>
                  <div class="attente-lieu">
                    📍 <?= htmlspecialchars($professionnel->getVille()) ?>
                    <?php if (!empty($professionnel->getQuartier())): ?>
                      - <?= htmlspecialchars($professionnel->getQuartier()) ?>
                    <?php endif; ?>
                  </div>
                </div>
  
              </div>
  
  
              <div class="attente-infos">
  
                <p><strong>Expérience :</strong> <?= $professionnel->getAnneesExperience() ?> an(s)</p>
  
                <?php if (!empty($professionnel->getDescription())): ?>
                  <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($professionnel->getDescription())) ?></p>
                <?php endif; ?>
  
                <?php if (!empty($professionnel->getSpecialites())): ?>
                  <p><strong>Spécialités :</strong> <?= nl2br(htmlspecialchars($professionnel->getSpecialites())) ?></p>
                <?php endif; ?>
  
                <?php if (!empty($professionnel->getWhatsapp())): ?>
                  <p><strong>WhatsApp :</strong> <?= htmlspecialchars($professionnel->getWhatsapp()) ?></p>
                <?php endif; ?>
  
                <?php if (!empty($professionnel->getDisponibilite())): ?>
                  <p><strong>Disponibilité :</strong> <?= htmlspecialchars($professionnel->getDisponibilite()) ?></p>
                <?php endif; ?>
  
              </div>
  
  
              <div class="attente-badge">
                🕐 En attente de vérification
              </div>
  
  
              <div class="attente-actions">
                <a href="verifier.php?id=<?= $professionnel->getId() ?>" class="btn btn-primaire">
                  ✓ Vérifier
                </a>
                <a href="rejeter.php?id=<?= $professionnel->getId() ?>" class="btn btn-rejeter">
                  ✕ Rejeter
                </a>
              </div>
  
            </article>
  
          <?php endforeach; ?>
  
        </div>
  
      <?php endif; ?>
  
    </main>
  
  </div>
  
  <script>
    // Surligne le bon lien de la sidebar selon l'ancre de l'URL
    var cible = window.location.hash === "#professionnels-attente" ? "professionnels-attente" : "dashboard";
    var lien = document.querySelector('#admin-nav a[data-cible="' + cible + '"]');
    if (lien) {
      lien.classList.add("actif");
    }
  </script>
  
  </body>
</html>