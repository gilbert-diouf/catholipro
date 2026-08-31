<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "config/database.php";

$erreur = "";
$succes = "";

$prenom = "";
$nom = "";
$email = "";
$telephone = "";
$role = "client";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupération des données
    $prenom = trim($_POST["prenom"] ?? "");
    $nom = trim($_POST["nom"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telephone = trim($_POST["telephone"] ?? "");
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";
    $confirmation_mot_de_passe = $_POST["confirmation_mot_de_passe"] ?? "";
    $role = $_POST["role"] ?? "";

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        empty($prenom) ||
        empty($nom) ||
        empty($email) ||
        empty($mot_de_passe) ||
        empty($confirmation_mot_de_passe) ||
        empty($role)
    ) {

        $erreur = "Veuillez remplir tous les champs obligatoires.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erreur = "Veuillez saisir une adresse email valide.";

    } elseif (strlen($mot_de_passe) < 8) {

        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";

    } elseif ($mot_de_passe !== $confirmation_mot_de_passe) {

        $erreur = "Les mots de passe ne correspondent pas.";

    } elseif (!in_array($role, ["client", "professionnel"], true)) {

        $erreur = "Le type de compte sélectionné est invalide.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION DE L'EMAIL
        |--------------------------------------------------------------------------
        */

        $sql = "SELECT id FROM utilisateurs WHERE email = ?";

        $stmt = $connexion->prepare($sql);
        $stmt->execute([$email]);

        $utilisateur_existant = $stmt->fetch();

        if ($utilisateur_existant) {

            $erreur = "Cette adresse email est déjà utilisée.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | HASHAGE DU MOT DE PASSE
            |--------------------------------------------------------------------------
            */

            $mot_de_passe_hash = password_hash(
                $mot_de_passe,
                PASSWORD_DEFAULT
            );

            /*
            |--------------------------------------------------------------------------
            | INSERTION
            |--------------------------------------------------------------------------
            */

            $sql = "
                INSERT INTO utilisateurs
                (
                    prenom,
                    nom,
                    email,
                    telephone,
                    mot_de_passe,
                    role
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ";

            $stmt = $connexion->prepare($sql);

            $stmt->execute([
                $prenom,
                $nom,
                $email,
                $telephone,
                $mot_de_passe_hash,
                $role
            ]);

            $succes = "Votre compte a été créé avec succès.";

            // Vider les champs après une inscription réussie
            $prenom = "";
            $nom = "";
            $email = "";
            $telephone = "";
            $role = "client";
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

    <title>Inscription | CatholiPro</title>

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

                <h2>Créer un compte</h2>

                <p>
                    Rejoignez la communauté CatholiPro.
                </p>


                <!-- Message d'erreur -->
                <?php if (!empty($erreur)): ?>

                    <div class="message-erreur">
                        <?= htmlspecialchars($erreur) ?>
                    </div>

                <?php endif; ?>


                <!-- Message de succès -->
                <?php if (!empty($succes)): ?>

                    <div class="message-succes">
                        <?= htmlspecialchars($succes) ?>
                    </div>

                <?php endif; ?>


                <form method="POST" action="">

                    <!-- Prénom -->
                    <div class="formulaire-groupe">

                        <label for="prenom">
                            Prénom
                        </label>

                        <input
                            type="text"
                            id="prenom"
                            name="prenom"
                            value="<?= htmlspecialchars($prenom) ?>"
                            placeholder="Votre prénom"
                            autocomplete="given-name"
                            required
                        >

                    </div>


                    <!-- Nom -->
                    <div class="formulaire-groupe">

                        <label for="nom">
                            Nom
                        </label>

                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            value="<?= htmlspecialchars($nom) ?>"
                            placeholder="Votre nom"
                            autocomplete="family-name"
                            required
                        >

                    </div>


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


                    <!-- Téléphone -->
                    <div class="formulaire-groupe">

                        <label for="telephone">
                            Téléphone
                        </label>

                        <input
                            type="tel"
                            id="telephone"
                            name="telephone"
                            value="<?= htmlspecialchars($telephone) ?>"
                            placeholder="77 000 00 00"
                            autocomplete="tel"
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
                            placeholder="Minimum 8 caractères"
                            autocomplete="new-password"
                            required
                        >

                    </div>


                    <!-- Confirmation mot de passe -->
                    <div class="formulaire-groupe">

                        <label for="confirmation_mot_de_passe">
                            Confirmer le mot de passe
                        </label>

                        <input
                            type="password"
                            id="confirmation_mot_de_passe"
                            name="confirmation_mot_de_passe"
                            placeholder="Confirmer votre mot de passe"
                            autocomplete="new-password"
                            required
                        >

                    </div>


                    <!-- Type de compte -->
                    <div class="formulaire-groupe">

                        <label for="role">
                            Type de compte
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                        >

                            <option
                                value="client"
                                <?= $role === "client" ? "selected" : "" ?>
                            >
                                Je recherche un professionnel
                            </option>

                            <option
                                value="professionnel"
                                <?= $role === "professionnel" ? "selected" : "" ?>
                            >
                                Je suis un professionnel
                            </option>

                        </select>

                    </div>


                    <!-- Bouton -->
                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >
                        Créer mon compte
                    </button>

                </form>


                <!-- Connexion -->
                <p style="text-align: center; margin-top: 20px;">

                    Vous avez déjà un compte ?

                    <a
                        href="connexion.php"
                        class="lien"
                    >
                        Se connecter
                    </a>

                </p>

            </div>

        </div>

    </main>

</body>

</html>

