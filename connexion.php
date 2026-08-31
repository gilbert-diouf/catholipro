<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "config/database.php";

$erreur = "";

$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupération des données
    $email = trim($_POST["email"] ?? "");
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (empty($email) || empty($mot_de_passe)) {

        $erreur = "Veuillez remplir tous les champs.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur = "Veuillez saisir une adresse email valide.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE DE L'UTILISATEUR
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT
                id,
                prenom,
                nom,
                email,
                telephone,
                mot_de_passe,
                role,
                photo_profil,
                statut
            FROM utilisateurs
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $connexion->prepare($sql);
        $stmt->execute([$email]);

        $utilisateur = $stmt->fetch();

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION DU COMPTE
        |--------------------------------------------------------------------------
        */

        if (!$utilisateur) {

            $erreur = "Email ou mot de passe incorrect.";

        } elseif ($utilisateur["statut"] !== "actif") {

            $erreur = "Votre compte n'est pas actif.";

        } elseif (
            !password_verify(
                $mot_de_passe,
                $utilisateur["mot_de_passe"]
            )
        ) {

            $erreur = "Email ou mot de passe incorrect.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | CONNEXION RÉUSSIE
            |--------------------------------------------------------------------------
            */

            session_regenerate_id(true);

            $_SESSION["utilisateur_id"] = $utilisateur["id"];
            $_SESSION["prenom"] = $utilisateur["prenom"];
            $_SESSION["nom"] = $utilisateur["nom"];
            $_SESSION["email"] = $utilisateur["email"];
            $_SESSION["role"] = $utilisateur["role"];
            $_SESSION["photo_profil"] = $utilisateur["photo_profil"];

            /*
            |--------------------------------------------------------------------------
            | REDIRECTION
            |--------------------------------------------------------------------------
            */

            header("Location: profil.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Connexion | CatholiPro</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

    <main class="page-authentification">

        <div class="authentification">

            <!-- Logo -->
            <div class="authentification-logo">

                <span class="croix">✝</span>

                <h1>CatholiPro</h1>

                <div class="separateur"></div>

                <p>
                    La foi au service des talents
                </p>

            </div>


            <!-- Formulaire -->
            <div class="carte">

                <h2>Bienvenue</h2>

                <p>
                    Connectez-vous à votre compte.
                </p>


                <?php if (!empty($erreur)): ?>

                    <div class="message-erreur">
                        <?= htmlspecialchars($erreur) ?>
                    </div>

                <?php endif; ?>


                <form method="POST" action="">

                    <!-- Email -->
                    <div class="formulaire-groupe">

                        <label for="email">
                            Adresse email
                        </label>

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

                        <label for="mot_de_passe">
                            Mot de passe
                        </label>

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
                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Se connecter
                    </button>

                </form>


                <!-- Inscription -->
                <p style="text-align: center; margin-top: 20px;">

                    Vous n'avez pas encore de compte ?

                    <a
                        href="inscription.php"
                        class="lien"
                    >
                        Créer un compte
                    </a>

                </p>

            </div>

        </div>

    </main>

</body>

</html>