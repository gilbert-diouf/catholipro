<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../app/Controllers/CategorieController.php";
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
 
$controller = new CategorieController();
 
$erreur = "";
$succes = "";
 
 
/*
|--------------------------------------------------------------------------
| Ajouter une catégorie
|--------------------------------------------------------------------------
*/
 
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "ajouter") {
 
    if (!Csrf::verifier($_POST["csrf_token"] ?? null)) {
 
        $erreur = "Votre session a expiré, veuillez réessayer.";
 
    } else {
 
        $resultat = $controller->ajouter($_POST);
 
        if ($resultat["succes"]) {
            $succes = $resultat["message"];
        } else {
            $erreur = $resultat["message"];
        }
    }
}
 
 
/*
|--------------------------------------------------------------------------
| Activer / Désactiver une catégorie
|--------------------------------------------------------------------------
*/
 
if (isset($_GET["action"], $_GET["id"]) && $_GET["action"] === "basculer") {
 
    $categorie_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
 
    if ($categorie_id) {
        $controller->basculerStatut($categorie_id);
    }
 
    header("Location: categories.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Supprimer une catégorie
|--------------------------------------------------------------------------
*/
 
if (isset($_GET["action"], $_GET["id"]) && $_GET["action"] === "supprimer") {
 
    $categorie_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
 
    if ($categorie_id) {
 
        $resultat = $controller->supprimer($categorie_id);
 
        if ($resultat["succes"]) {
            $succes = $resultat["message"];
        } else {
            $erreur = $resultat["message"];
        }
    }
}
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les catégories
|--------------------------------------------------------------------------
*/
 
$categories = $controller->listerToutes();
 
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Catégories | ProCatho</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 
<div class="admin-layout">
 
  <aside class="admin-sidebar">
 
    <div class="admin-sidebar-logo">
      <img src="../assets/images/logo.jpeg" alt="Logo ProCatho">
      ProCatho
    </div>
 
    <ul class="admin-nav">
      <li><a href="index.php"><span class="admin-nav-icone">📊</span> Tableau de bord</a></li>
      <li><a href="index.php#professionnels-attente"><span class="admin-nav-icone">🛠️</span> Professionnels</a></li>
      <li><a href="utilisateurs.php"><span class="admin-nav-icone">👥</span> Utilisateurs</a></li>
      <li><a href="categories.php" class="actif"><span class="admin-nav-icone">🏷️</span> Catégories</a></li>
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
 
 
  <main class="admin-main">
 
    <div class="admin-en-tete">
      <div>
        <h2>Catégories de métiers</h2>
        <p>Gérez la liste des métiers proposés sur ProCatho.</p>
      </div>
      <span class="admin-compteur"><?= count($categories) ?> au total</span>
    </div>
 
 
    <?php if (!empty($erreur)): ?>
      <div class="message-erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
 
    <?php if (!empty($succes)): ?>
      <div class="message-succes"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
 
 
    <div class="admin-ajout-carte">
      <h3>Ajouter un métier</h3>
      <form method="POST" class="admin-ajout-ligne">
        <?= Csrf::champCache() ?>
        <input type="hidden" name="action" value="ajouter">
 
        <div class="admin-ajout-champ">
          <label for="nom">Nom du métier</label>
          <input type="text" id="nom" name="nom" placeholder="Exemple : Électricien" required>
        </div>
 
        <div class="admin-ajout-champ" style="max-width: 120px;">
          <label for="icone">Icône (emoji)</label>
          <input type="text" id="icone" name="icone" placeholder="⚡">
        </div>
 
        <button type="submit" class="btn btn-primaire">+ Ajouter</button>
      </form>
    </div>
 
 
    <?php if (empty($categories)): ?>
 
      <div class="carte-vide">
        <h3>Aucune catégorie</h3>
        <p>Ajoutez votre premier métier ci-dessus.</p>
      </div>
 
    <?php else: ?>
 
      <div class="tableau-carte">
        <table class="tableau-utilisateurs">
 
          <thead>
            <tr>
              <th></th>
              <th>Nom</th>
              <th>Statut</th>
              <th>Action</th>
            </tr>
          </thead>
 
          <tbody>
 
            <?php foreach ($categories as $categorie): ?>
 
              <tr>
 
                <td class="cellule-icone">
                  <?= !empty($categorie->getIcone()) ? htmlspecialchars($categorie->getIcone()) : "—" ?>
                </td>
 
                <td class="ligne-utilisateur-nom">
                  <?= htmlspecialchars($categorie->getNom()) ?>
                </td>
 
                <td>
                  <span class="badge-statut statut-<?= $categorie->getStatut() === "actif" ? "actif" : "suspendu" ?>">
                    <?= htmlspecialchars(ucfirst($categorie->getStatut())) ?>
                  </span>
                </td>
 
                <td>
                  <a
                    href="categories.php?action=basculer&id=<?= $categorie->getId() ?>"
                    class="<?= $categorie->getStatut() === "actif" ? "action-suspendre" : "action-activer" ?>"
                  >
                    <?= $categorie->getStatut() === "actif" ? "Désactiver" : "Activer" ?>
                  </a>
 
                  <a
                    href="categories.php?action=supprimer&id=<?= $categorie->getId() ?>"
                    class="action-supprimer"
                    onclick="return confirm('Supprimer définitivement ce métier ?');"
                  >
                    Supprimer
                  </a>
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