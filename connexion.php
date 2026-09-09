<?php
 
  session_start();
  
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  
  require_once "app/Controllers/UtilisateurController.php";
  
  $controller = new UtilisateurController();
  
  $erreur = "";
  
  $email = "";
  
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
      $email = trim($_POST["email"] ?? "");
  
      $resultat = $controller->connecter($_POST);
  
      if ($resultat["succes"]) {
  
          header("Location: " . $resultat["redirection"]);
          exit;
  
      } else {
  
          $erreur = $resultat["message"];
      }
  }
  
?>
 
<!DOCTYPE html>
<html lang="fr">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion | ProCatho</title>
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
  
        <h2>Bienvenue</h2>
        <p>Connectez-vous à votre compte.</p>
  
  
        <?php if (!empty($erreur)): ?>
          <div class="message-erreur">
            <?= htmlspecialchars($erreur) ?>
          </div>
        <?php endif; ?>
  
  
        <form method="POST" action="">
  
          <!-- Email -->
          <div class="formulaire-groupe">
            <label for="email">Adresse email</label>
            <input
              type="email"
              id="email"
              name="email"
              value="<?= htmlspecialchars($email) ?>"
              placeholder="exemple@email.com"
              autocomplete="email"
              required
            >
          </div>
  
  
          <!-- Mot de passe -->
          <div class="formulaire-groupe">
            <label for="mot_de_passe">Mot de passe</label>
            <input
              type="password"
              id="mot_de_passe"
              name="mot_de_passe"
              placeholder="Votre mot de passe"
              autocomplete="current-password"
              required
            >
          </div>
  
  
          <!-- Bouton -->
          <button type="submit" class="btn btn-primaire">
            Se connecter
          </button>
  
        </form>
  
  
        <!-- Inscription -->
        <p class="lien-authentification">
          Vous n'avez pas encore de compte ? <a href="inscription.php">Créer un compte</a>
        </p>
  
      </div>
  
    </div>
  
  </main>
  
  </body>
</html>
 