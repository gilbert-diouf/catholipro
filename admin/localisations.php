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
 
 
$erreur = "";
$succes = "";
 
 
/*
|--------------------------------------------------------------------------
| Ajouter une localisation
|--------------------------------------------------------------------------
*/
 
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "ajouter") {
 
    $region = trim($_POST["region"] ?? "");
    $departement = trim($_POST["departement"] ?? "");
    $ville = trim($_POST["ville"] ?? "");
    $quartier = trim($_POST["quartier"] ?? "");
 
    if (empty($region) || empty($ville)) {
 
        $erreur = "La région et la ville sont obligatoires.";
 
    } else {
 
        $sql = "
            INSERT INTO localisations (region, departement, ville, quartier)
            VALUES (?, ?, ?, ?)
        ";
 
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$region, $departement, $ville, $quartier]);
 
        $succes = "La localisation a été ajoutée avec succès.";
    }
}
 
 
/*
|--------------------------------------------------------------------------
| Supprimer une localisation
|--------------------------------------------------------------------------
*/
 
if (isset($_GET["action"], $_GET["id"]) && $_GET["action"] === "supprimer") {
 
    $localisation_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
 
    if ($localisation_id) {
 
        try {
 
            $sql = "DELETE FROM localisations WHERE id = ?";
            $stmt = $connexion->prepare($sql);
            $stmt->execute([$localisation_id]);
 
            $succes = "La localisation a été supprimée.";
 
        } catch (PDOException $e) {
 
            $erreur = "Impossible de supprimer cette localisation : des professionnels y sont encore rattachés.";
        }
    }
}
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les localisations
|--------------------------------------------------------------------------
*/
 
$sql = "
    SELECT id, region, departement, ville, quartier
    FROM localisations
    ORDER BY region, ville, quartier
";
$stmt = $connexion->prepare($sql);
$stmt->execute();
$localisations = $stmt->fetchAll();
 
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Localisations | ProCatho</title>
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
      <li><a href="categories.php"><span class="admin-nav-icone">🏷️</span> Catégories</a></li>
      <li><a href="localisations.php" class="actif"><span class="admin-nav-icone">📍</span> Localisations</a></li>
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
        <h2>Localisations</h2>
        <p>Gérez les régions, départements, villes et quartiers couverts.</p>
      </div>
      <span class="admin-compteur"><?= count($localisations) ?> au total</span>
    </div>
 
 
    <?php if (!empty($erreur)): ?>
      <div class="message-erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
 
    <?php if (!empty($succes)): ?>
      <div class="message-succes"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
 
 
    <div class="admin-ajout-carte">
      <h3>Ajouter une localisation</h3>
      <form method="POST" class="admin-ajout-ligne">
        <input type="hidden" name="action" value="ajouter">
 
        <div class="admin-ajout-champ">
          <label for="region">Région</label>
          <input type="text" id="region" name="region" placeholder="Exemple : Dakar" required>
        </div>
 
        <div class="admin-ajout-champ">
          <label for="departement">Département</label>
          <input type="text" id="departement" name="departement" placeholder="Exemple : Pikine">
        </div>
 
        <div class="admin-ajout-champ">
          <label for="ville">Ville</label>
          <input type="text" id="ville" name="ville" placeholder="Exemple : Pikine" required>
        </div>
 
        <div class="admin-ajout-champ">
          <label for="quartier">Quartier</label>
          <input type="text" id="quartier" name="quartier" placeholder="Exemple : Guinaw Rails">
        </div>
 
        <button type="submit" class="btn btn-primaire">+ Ajouter</button>
      </form>
    </div>
 
 
    <?php if (empty($localisations)): ?>
 
      <div class="carte-vide">
        <h3>Aucune localisation</h3>
        <p>Ajoutez votre première localisation ci-dessus.</p>
      </div>
 
    <?php else: ?>
 
      <div class="tableau-carte">
        <table class="tableau-utilisateurs">
 
          <thead>
            <tr>
              <th>Région</th>
              <th>Département</th>
              <th>Ville</th>
              <th>Quartier</th>
              <th>Action</th>
            </tr>
          </thead>
 
          <tbody>
 
            <?php foreach ($localisations as $localisation): ?>
 
              <tr>
 
                <td><?= htmlspecialchars($localisation["region"]) ?></td>
                <td><?= !empty($localisation["departement"]) ? htmlspecialchars($localisation["departement"]) : "—" ?></td>
                <td class="ligne-utilisateur-nom"><?= htmlspecialchars($localisation["ville"]) ?></td>
                <td><?= !empty($localisation["quartier"]) ? htmlspecialchars($localisation["quartier"]) : "—" ?></td>
 
                <td>
                  <a
                    href="localisations.php?action=supprimer&id=<?= (int) $localisation["id"] ?>"
                    class="action-supprimer"
                    onclick="return confirm('Supprimer définitivement cette localisation ?');"
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
 