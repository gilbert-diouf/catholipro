<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../config/database.php";
 
 
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
 
/*
|--------------------------------------------------------------------------
| Statistiques du tableau de bord
|--------------------------------------------------------------------------
*/
 
$sql = "SELECT COUNT(*) AS total FROM utilisateurs";
$stmt = $connexion->prepare($sql);
$stmt->execute();
$total_utilisateurs = $stmt->fetch()["total"];
 
$sql = "SELECT COUNT(*) AS total FROM utilisateurs WHERE role = 'professionnel'";
$stmt = $connexion->prepare($sql);
$stmt->execute();
$total_professionnels = $stmt->fetch()["total"];
 
$sql = "SELECT COUNT(*) AS total FROM profils_professionnels WHERE statut_verification = 'en_attente'";
$stmt = $connexion->prepare($sql);
$stmt->execute();
$total_en_attente = $stmt->fetch()["total"];
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les professionnels en attente
|--------------------------------------------------------------------------
*/
 
$sql = "
    SELECT
        pp.id,
        pp.utilisateur_id,
        pp.description,
        pp.specialites,
        pp.annees_experience,
        pp.adresse,
        pp.whatsapp,
        pp.disponibilite,
        pp.statut_verification,
        pp.date_creation,
 
        u.prenom,
        u.nom,
        u.email,
        u.telephone,
        u.photo_profil,
 
        c.nom AS categorie_nom,
        c.icone AS categorie_icone,
 
        l.region,
        l.departement,
        l.ville,
        l.quartier
 
    FROM profils_professionnels pp
 
    INNER JOIN utilisateurs u
        ON u.id = pp.utilisateur_id
 
    INNER JOIN categories c
        ON c.id = pp.categorie_id
 
    INNER JOIN localisations l
        ON l.id = pp.localisation_id
 
    WHERE pp.statut_verification = 'en_attente'
 
    ORDER BY pp.date_creation ASC
";
 
 
$stmt = $connexion->prepare($sql);
$stmt->execute();
 
$professionnels = $stmt->fetchAll();
 
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
          <div class="stat-valeur"><?= (int) $total_utilisateurs ?></div>
          <div class="stat-libelle">Utilisateurs</div>
        </div>
      </div>
 
      <div class="stat-carte stat-vert">
        <div class="stat-icone">🛠️</div>
        <div>
          <div class="stat-valeur"><?= (int) $total_professionnels ?></div>
          <div class="stat-libelle">Professionnels</div>
        </div>
      </div>
 
      <div class="stat-carte stat-or">
        <div class="stat-icone">⏳</div>
        <div>
          <div class="stat-valeur"><?= (int) $total_en_attente ?></div>
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
 
              <?php if (!empty($professionnel["photo_profil"])): ?>
                <img
                  class="attente-photo"
                  src="<?= htmlspecialchars($professionnel["photo_profil"]) ?>"
                  alt="Photo de <?= htmlspecialchars($professionnel["prenom"]) ?>"
                >
              <?php else: ?>
                <div class="attente-photo">👤</div>
              <?php endif; ?>
 
              <div class="attente-identite">
                <h3>
                  <?= htmlspecialchars($professionnel["prenom"]) ?>
                  <?= htmlspecialchars($professionnel["nom"]) ?>
                </h3>
                <div class="attente-metier">
                  <?php if (!empty($professionnel["categorie_icone"])): ?>
                    <?= htmlspecialchars($professionnel["categorie_icone"]) ?>
                  <?php endif; ?>
                  <?= htmlspecialchars($professionnel["categorie_nom"]) ?>
                </div>
                <div class="attente-lieu">
                  📍 <?= htmlspecialchars($professionnel["ville"]) ?>
                  <?php if (!empty($professionnel["quartier"])): ?>
                    - <?= htmlspecialchars($professionnel["quartier"]) ?>
                  <?php endif; ?>
                </div>
              </div>
 
            </div>
 
 
            <div class="attente-infos">
 
              <p><strong>Expérience :</strong> <?= (int) $professionnel["annees_experience"] ?> an(s)</p>
 
              <?php if (!empty($professionnel["description"])): ?>
                <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($professionnel["description"])) ?></p>
              <?php endif; ?>
 
              <?php if (!empty($professionnel["specialites"])): ?>
                <p><strong>Spécialités :</strong> <?= nl2br(htmlspecialchars($professionnel["specialites"])) ?></p>
              <?php endif; ?>
 
              <?php if (!empty($professionnel["whatsapp"])): ?>
                <p><strong>WhatsApp :</strong> <?= htmlspecialchars($professionnel["whatsapp"]) ?></p>
              <?php endif; ?>
 
              <?php if (!empty($professionnel["disponibilite"])): ?>
                <p><strong>Disponibilité :</strong> <?= htmlspecialchars($professionnel["disponibilite"]) ?></p>
              <?php endif; ?>
 
            </div>
 
 
            <div class="attente-badge">
              🕐 En attente de vérification
            </div>
 
 
            <div class="attente-actions">
              <a href="verifier.php?id=<?= (int) $professionnel["id"] ?>" class="btn btn-primaire">
                ✓ Vérifier
              </a>
              <a href="rejeter.php?id=<?= (int) $professionnel["id"] ?>" class="btn btn-rejeter">
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
 