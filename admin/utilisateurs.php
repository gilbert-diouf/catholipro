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
| Filtre par rôle
|--------------------------------------------------------------------------
*/
 
$role_filtre = $_GET["role"] ?? "";
 
$roles_valides = ["client", "professionnel", "administrateur"];
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les utilisateurs
|--------------------------------------------------------------------------
*/
 
$sql = "
    SELECT
        id,
        prenom,
        nom,
        email,
        telephone,
        role,
        statut,
        photo_profil,
        date_creation
    FROM utilisateurs
";
 
$params = [];
 
if (in_array($role_filtre, $roles_valides, true)) {
 
    $sql .= " WHERE role = ? ";
 
    $params[] = $role_filtre;
}
 
$sql .= " ORDER BY date_creation DESC ";
 
$stmt = $connexion->prepare($sql);
$stmt->execute($params);
 
$utilisateurs = $stmt->fetchAll();
 
 
/*
|--------------------------------------------------------------------------
| Compter le total pour le badge de la sidebar
|--------------------------------------------------------------------------
*/
 
$sql = "SELECT COUNT(*) AS total FROM utilisateurs";
$stmt = $connexion->prepare($sql);
$stmt->execute();
$total_utilisateurs = $stmt->fetch()["total"];
 
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
      <span class="admin-compteur"><?= (int) $total_utilisateurs ?> au total</span>
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
                  mb_substr($utilisateur["prenom"], 0, 1) . mb_substr($utilisateur["nom"], 0, 1)
              );
 
              ?>
 
              <tr>
 
                <td>
                  <div class="ligne-utilisateur-identite">
 
                    <?php if (!empty($utilisateur["photo_profil"])): ?>
                      <img
                        class="mini-avatar"
                        src="<?= htmlspecialchars($utilisateur["photo_profil"]) ?>"
                        alt="Photo de <?= htmlspecialchars($utilisateur["prenom"]) ?>"
                      >
                    <?php else: ?>
                      <div class="mini-avatar"><?= htmlspecialchars($initiales) ?></div>
                    <?php endif; ?>
 
                    <div>
                      <div class="ligne-utilisateur-nom">
                        <?= htmlspecialchars($utilisateur["prenom"]) ?>
                        <?= htmlspecialchars($utilisateur["nom"]) ?>
                      </div>
                      <div class="ligne-utilisateur-email">
                        <?= htmlspecialchars($utilisateur["email"]) ?>
                      </div>
                    </div>
 
                  </div>
                </td>
 
                <td>
                  <?= !empty($utilisateur["telephone"])
                      ? htmlspecialchars($utilisateur["telephone"])
                      : "—"
                  ?>
                </td>
 
                <td>
                  <span class="badge-role role-<?= htmlspecialchars($utilisateur["role"]) ?>">
                    <?= htmlspecialchars(ucfirst($utilisateur["role"])) ?>
                  </span>
                </td>
 
                <td>
                  <span class="badge-statut statut-<?= htmlspecialchars($utilisateur["statut"]) ?>">
                    <?= htmlspecialchars(ucfirst($utilisateur["statut"])) ?>
                  </span>
                </td>
 
                <td>
                  <?= htmlspecialchars(date("d/m/Y", strtotime($utilisateur["date_creation"]))) ?>
                </td>
 
                <td>
 
                  <?php if ((int) $utilisateur["id"] === (int) $_SESSION["utilisateur_id"]): ?>
 
                    <span style="color: var(--gris-texte); font-size: 0.85rem;">—</span>
 
                  <?php elseif ($utilisateur["statut"] === "actif"): ?>
 
                    <a
                      href="basculer-statut.php?id=<?= (int) $utilisateur["id"] ?>"
                      class="action-suspendre"
                      onclick="return confirm('Suspendre ce compte ?');"
                    >
                      Suspendre
                    </a>
 
                  <?php else: ?>
 
                    <a
                      href="basculer-statut.php?id=<?= (int) $utilisateur["id"] ?>"
                      class="action-activer"
                      onclick="return confirm('Réactiver ce compte ?');"
                    >
                      Activer
                    </a>
 
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
 