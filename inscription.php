<?php

  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  require_once "app/Controllers/UtilisateurController.php";
  require_once "app/Security/Csrf.php";

  $controller = new UtilisateurController();

  $erreur = "";
  $succes = "";

  // Initialisation des variables pour conserver les saisies en cas d'erreur
  $prenom = "";
  $nom = "";
  $email = "";
  $telephone = "";
  $role = "professionnel"; // Rôle par défaut (ou 'client')

  if ($_SERVER["REQUEST_METHOD"] === "POST") {

      // Conserver les saisies en cas d'erreur (affichage uniquement)
      $prenom = trim($_POST["prenom"] ?? "");
      $nom = trim($_POST["nom"] ?? "");
      $email = trim($_POST["email"] ?? "");
      $telephone = trim($_POST["telephone"] ?? "");
      $role = $_POST["role"] ?? "professionnel";

      if (!Csrf::verifier($_POST["csrf_token"] ?? null)) {

          $erreur = "Votre session a expiré, veuillez réessayer.";

      } else {

          $resultat = $controller->inscrire($_POST);

          if ($resultat["succes"]) {
              $succes = $resultat["message"];
              // Réinitialisation du formulaire
              $prenom = $nom = $email = $telephone = "";
          } else {
              $erreur = $resultat["message"];
          }
      }
  }
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Inscription | ProCatho</title>
      <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>

  <main class="page-authentification">
    <div class="authentification">

      <!-- Logo -->
      <div class="authentification-logo">
        <a href="index.php">
          <img src="assets/images/logo.jpeg" alt="Logo ProCatho">
        </a>
        <h1>ProCatho</h1>
        <div class="separateur"></div>
        <p>La foi au service des talents</p>
      </div>

      <!-- Formulaire -->
      <div class="authentification-carte">
        <h2>Créer un compte</h2>
        <p>Rejoignez la communauté des professionnels au Sénégal.</p>

        <!-- Messages d'alerte -->
        <?php if (!empty($erreur)): ?>
          <div class="message-erreur" style="color: red; margin-bottom: 15px;">
            <?= htmlspecialchars($erreur) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
          <div class="message-succes" style="color: green; margin-bottom: 15px;">
            <?= htmlspecialchars($succes) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
  
          <?= Csrf::champCache() ?>

          <!-- Prénom -->
          <div class="formulaire-groupe">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" placeholder="Votre prénom" required>
          </div>

          <!-- Nom -->
          <div class="formulaire-groupe">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" placeholder="Votre nom" required>
          </div>

          <!-- Téléphone -->
          <div class="formulaire-groupe">
            <label for="telephone">Numéro de téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone) ?>" placeholder="Ex: 77 XXX XX XX" required>
          </div>

          <!-- Email -->
          <div class="formulaire-groupe">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="exemple@email.com" required>
          </div>

          <!-- Profil / Rôle -->
          <div class="formulaire-groupe">
            <label for="role">Je m'inscris en tant que :</label>
            <select id="role" name="role">
              <option value="professionnel" <?= $role === 'professionnel' ? 'selected' : '' ?>>Professionnel (Maçon, mécanicien...)</option>
              <option value="client" <?= $role === 'client' ? 'selected' : '' ?>>Client (Je cherche un professionnel)</option>
            </select>
          </div>

          <!-- Mot de passe -->
          <div class="formulaire-groupe">
            <label for="mot_de_passe">Mot de passe (8 caractères min.)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Créez votre mot de passe" required>
          </div>

          <!-- Bouton -->
          <button type="submit" class="btn btn-primaire">
            S'inscrire
          </button>

        </form>

        <!-- Connexion -->
        <p class="lien-authentification">
          Vous avez déjà un compte ? <a href="connexion.php">Se connecter</a>
        </p>

      </div>
    </div>
  </main>

  </body>
</html>
