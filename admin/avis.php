<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../app/Controllers/AvisController.php";
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
 
$controller = new AvisController();
 
$erreur = "";
$succes = "";
 
 
/*
|--------------------------------------------------------------------------
| Publier / Rejeter un avis
|--------------------------------------------------------------------------
*/
 
if ($_SERVER["REQUEST_METHOD"] === "POST" && in_array($_POST["action"] ?? "", ["publier", "rejeter"], true)) {
 
    if (Csrf::verifier($_POST["csrf_token"] ?? null)) {
 
        $avis_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
 
        if ($avis_id) {
 
            if ($_POST["action"] === "publier") {
                $controller->publier($avis_id);
            } else {
                $controller->rejeter($avis_id);
            }
        }
    }
 
    header("Location: avis.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Supprimer un avis
|--------------------------------------------------------------------------
*/
 
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "supprimer") {
 
    if (!Csrf::verifier($_POST["csrf_token"] ?? null)) {
 
        $erreur = "Votre session a expiré, veuillez réessayer.";
 
    } else {
 
        $avis_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
 
        if ($avis_id) {
 
            $resultat = $controller->supprimer($avis_id);
 
            if ($resultat["succes"]) {
                $succes = $resultat["message"];
            } else {
                $erreur = $resultat["message"];
            }
        }
    }
}
 
 
/*
|--------------------------------------------------------------------------
| Récupérer les avis
|--------------------------------------------------------------------------
*/
 
$avis_en_attente = $controller->listerEnAttente();
$avis_publies = $controller->listerTousPublies();
 
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Avis | ProCatho</title>
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
      <li><a href="localisations.php"><span class="admin-nav-icone">📍</span> Localisations</a></li>
      <li><a href="avis.php" class="actif"><span class="admin-nav-icone">⭐</span> Avis</a></li>
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
        <h2>Avis clients</h2>
        <p>Modérez les avis laissés sur les professionnels.</p>
      </div>
      <span class="admin-compteur"><?= count($avis_en_attente) ?> en attente</span>
    </div>
 
 
    <?php if (!empty($erreur)): ?>
      <div class="message-erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
 
    <?php if (!empty($succes)): ?>
      <div class="message-succes"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>
 
 
    <!-- ================= En attente de modération ================= -->
    <?php if (empty($avis_en_attente)): ?>
 
      <div class="carte-vide">
        <h3>Aucun avis en attente</h3>
        <p>Tous les avis ont été traités.</p>
      </div>
 
    <?php else: ?>
 
      <div class="liste-attente">
 
        <?php foreach ($avis_en_attente as $avis): ?>
 
          <article class="attente-carte">
 
            <div class="attente-entete">
              <div class="attente-identite">
                <h3><?= str_repeat("⭐", $avis->getNote()) ?> (<?= $avis->getNote() ?>/5)</h3>
                <div class="attente-metier">
                  Par <?= htmlspecialchars($avis->getAuteurPrenom()) ?> <?= htmlspecialchars($avis->getAuteurNom()) ?>
                </div>
                <div class="attente-lieu">
                  À propos de <?= htmlspecialchars($avis->getProfessionnelPrenom()) ?> <?= htmlspecialchars($avis->getProfessionnelNom()) ?>
                </div>
              </div>
            </div>
 
            <?php if (!empty($avis->getCommentaire())): ?>
              <div class="attente-infos">
                <p><?= nl2br(htmlspecialchars($avis->getCommentaire())) ?></p>
              </div>
            <?php endif; ?>
 
            <div class="attente-badge">
              🕐 En attente de modération
            </div>
 
            <div class="attente-actions">
 
              <form method="POST" action="avis.php">
                <?= Csrf::champCache() ?>
                <input type="hidden" name="action" value="publier">
                <input type="hidden" name="id" value="<?= $avis->getId() ?>">
                <button type="submit" class="btn btn-primaire">✓ Publier</button>
              </form>
 
              <form method="POST" action="avis.php">
                <?= Csrf::champCache() ?>
                <input type="hidden" name="action" value="rejeter">
                <input type="hidden" name="id" value="<?= $avis->getId() ?>">
                <button type="submit" class="btn btn-rejeter">✕ Rejeter</button>
              </form>
 
            </div>
 
          </article>
 
        <?php endforeach; ?>
 
      </div>
 
    <?php endif; ?>
 
 
    <!-- ================= Avis publiés ================= -->
    <div class="admin-en-tete" style="margin-top: 40px;">
      <div>
        <h3>Avis publiés</h3>
        <p>Consultez et modérez les avis déjà visibles publiquement.</p>
      </div>
      <span class="admin-compteur"><?= count($avis_publies) ?> au total</span>
    </div>
 
    <?php if (empty($avis_publies)): ?>
 
      <div class="carte-vide">
        <h3>Aucun avis publié</h3>
        <p>Aucun avis n'est encore visible publiquement.</p>
      </div>
 
    <?php else: ?>
 
      <div class="tableau-carte">
        <table class="tableau-utilisateurs">
 
          <thead>
            <tr>
              <th>Auteur</th>
              <th>Professionnel</th>
              <th>Note</th>
              <th>Commentaire</th>
              <th>Action</th>
            </tr>
          </thead>
 
          <tbody>
 
            <?php foreach ($avis_publies as $avis): ?>
 
              <tr>
                <td class="ligne-utilisateur-nom">
                  <?= htmlspecialchars($avis->getAuteurPrenom()) ?> <?= htmlspecialchars($avis->getAuteurNom()) ?>
                </td>
                <td><?= htmlspecialchars($avis->getProfessionnelPrenom()) ?> <?= htmlspecialchars($avis->getProfessionnelNom()) ?></td>
                <td><?= str_repeat("⭐", $avis->getNote()) ?></td>
                <td><?= !empty($avis->getCommentaire()) ? htmlspecialchars(mb_substr($avis->getCommentaire(), 0, 60)) . (mb_strlen($avis->getCommentaire()) > 60 ? "…" : "") : "—" ?></td>
                <td>
                  <form method="POST" action="avis.php" style="display: inline;">
                    <?= Csrf::champCache() ?>
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="id" value="<?= $avis->getId() ?>">
                    <button
                      type="submit"
                      class="bouton-texte action-supprimer"
                      onclick="return confirm('Supprimer définitivement cet avis ?');"
                    >
                      Supprimer
                    </button>
                  </form>
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