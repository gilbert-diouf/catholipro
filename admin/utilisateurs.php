<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../app/Controllers/UtilisateurController.php";
require_once "../app/Security/Csrf.php";
 
 
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
 
$controller = new UtilisateurController();
 
 
/*
|--------------------------------------------------------------------------
| Filtre par rôle
|--------------------------------------------------------------------------
*/
 
$role_filtre = $_GET["role"] ?? "";
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les utilisateurs
|--------------------------------------------------------------------------
*/
 
$utilisateurs = $controller->lister($_GET);
 
 
/*
|--------------------------------------------------------------------------
| Compter le total pour le badge de la sidebar
|--------------------------------------------------------------------------
*/
 
$total_utilisateurs = $controller->compterTous();
 
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Utilisateurs | ProCatho</title>
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
 
    <ul class="admin-nav">
      <li><a href="index.php"><span class="admin-nav-icone">📊</span> Tableau de bord</a></li>
      <li><a href="index.php#professionnels-attente"><span class="admin-nav-icone">🛠️</span> Professionnels</a></li>
      <li><a href="utilisateurs.php" class="actif"><span class="admin-nav-icone">👥</span> Utilisateurs</a></li>
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
        <h2>Utilisateurs</h2>
        <p>Gérez les comptes clients, professionnels et administrateurs.</p>
      </div>
      <span class="admin-compteur"><?= $total_utilisateurs ?> au total</span>
    </div>
 
 
    <div class="admin-filtres">
      <a href="utilisateurs.php" class="<?= $role_filtre === "" ? "actif" : "" ?>">Tous</a>
      <a href="utilisateurs.php?role=client" class="<?= $role_filtre === "client" ? "actif" : "" ?>">Clients</a>
      <a href="utilisateurs.php?role=professionnel" class="<?= $role_filtre === "professionnel" ? "actif" : "" ?>">Professionnels</a>
      <a href="utilisateurs.php?role=administrateur" class="<?= $role_filtre === "administrateur" ? "actif" : "" ?>">Administrateurs</a>
    </div>
 
 
    <?php if (empty($utilisateurs)): ?>
 
      <div class="carte-vide">
        <h3>Aucun utilisateur trouvé</h3>
        <p>Aucun compte ne correspond à ce filtre.</p>
      </div>
 
    <?php else: ?>
 
      <div class="tableau-carte">
        <table class="tableau-utilisateurs">
 
          <thead>
            <tr>
              <th>Utilisateur</th>
              <th>Téléphone</th>
              <th>Rôle</th>
              <th>Statut</th>
              <th>Inscrit le</th>
              <th>Action</th>
            </tr>
          </thead>
 
          <tbody>
 
            <?php foreach ($utilisateurs as $utilisateur): ?>
 
              <?php
 
              $initiales = mb_strtoupper(
                  mb_substr($utilisateur->getPrenom(), 0, 1) . mb_substr($utilisateur->getNom(), 0, 1)
              );
 
              ?>
 
              <tr>
 
                <td>
                  <div class="ligne-utilisateur-identite">
 
                    <?php if (!empty($utilisateur->getPhotoProfil())): ?>
                      <img
                        class="mini-avatar"
                        src="<?= htmlspecialchars($utilisateur->getPhotoProfil()) ?>"
                        alt="Photo de <?= htmlspecialchars($utilisateur->getPrenom()) ?>"
                      >
                    <?php else: ?>
                      <div class="mini-avatar"><?= htmlspecialchars($initiales) ?></div>
                    <?php endif; ?>
 
                    <div>
                      <div class="ligne-utilisateur-nom">
                        <?= htmlspecialchars($utilisateur->getPrenom()) ?>
                        <?= htmlspecialchars($utilisateur->getNom()) ?>
                      </div>
                      <div class="ligne-utilisateur-email">
                        <?= htmlspecialchars($utilisateur->getEmail()) ?>
                      </div>
                    </div>
 
                  </div>
                </td>
 
                <td>
                  <?= !empty($utilisateur->getTelephone())
                      ? htmlspecialchars($utilisateur->getTelephone())
                      : "—"
                  ?>
                </td>
 
                <td>
                  <span class="badge-role role-<?= htmlspecialchars($utilisateur->getRole()) ?>">
                    <?= htmlspecialchars(ucfirst($utilisateur->getRole())) ?>
                  </span>
                </td>
 
                <td>
                  <span class="badge-statut statut-<?= htmlspecialchars($utilisateur->getStatut()) ?>">
                    <?= htmlspecialchars(ucfirst($utilisateur->getStatut())) ?>
                  </span>
                </td>
 
                <td>
                  <?= htmlspecialchars(date("d/m/Y", strtotime($utilisateur->getDateCreation()))) ?>
                </td>
 
                <td>
 
                  <?php if ($utilisateur->getId() === (int) $_SESSION["utilisateur_id"]): ?>
 
                    <span style="color: var(--gris-texte); font-size: 0.85rem;">—</span>
 
                  <?php elseif ($utilisateur->getStatut() === "actif"): ?>
 
                    <form method="POST" action="basculer-statut.php">
                      <?= Csrf::champCache() ?>
                      <input type="hidden" name="id" value="<?= $utilisateur->getId() ?>">
                      <button
                        type="submit"
                        class="bouton-texte action-suspendre"
                        onclick="return confirm('Suspendre ce compte ?');"
                      >
                        Suspendre
                      </button>
                    </form>
 
                  <?php else: ?>
 
                    <form method="POST" action="basculer-statut.php">
                      <?= Csrf::champCache() ?>
                      <input type="hidden" name="id" value="<?= $utilisateur->getId() ?>">
                      <button
                        type="submit"
                        class="bouton-texte action-activer"
                        onclick="return confirm('Réactiver ce compte ?');"
                      >
                        Activer
                      </button>
                    </form>
 
                  <?php endif; ?>
 
                </td>
 
              </tr>
 
            <?php endforeach; ?>
 
          </tbody>
 
        </table>
      </div>
 
    <?php endif; ?>
 
  </main>
 
</div>
 
</body>
</html>