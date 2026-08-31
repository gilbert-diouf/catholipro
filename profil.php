<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Vérifier si l'utilisateur est connecté
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["utilisateur_id"])) {

    header("Location: connexion.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer l'utilisateur connecté
|--------------------------------------------------------------------------
*/

$utilisateur_id = $_SESSION["utilisateur_id"];

$sql = "
    SELECT
        id,
        prenom,
        nom,
        email,
        telephone,
        role,
        photo_profil,
        statut,
        date_creation
    FROM utilisateurs
    WHERE id = ?
    LIMIT 1
";

$stmt = $connexion->prepare($sql);
$stmt->execute([$utilisateur_id]);

$utilisateur = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Vérifier que l'utilisateur existe toujours
|--------------------------------------------------------------------------
*/

if (!$utilisateur) {

    session_unset();
    session_destroy();

    header("Location: connexion.php");
    exit;
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

    <title>Mon profil | CatholiPro</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

    <main class="page-authentification">

        <div class="authentification">

            <div class="authentification-logo">

                <span class="croix">✝</span>

                <h1>CatholiPro</h1>

                <div class="separateur"></div>

                <p>
                    La foi au service des talents
                </p>

            </div>


            <div class="carte">

                <h2>
                    Bienvenue,
                    <?= htmlspecialchars($utilisateur["prenom"]) ?>
                </h2>

                <p>
                    Voici les informations de votre compte.
                </p>


                <hr style="margin: 25px 0;">


                <div class="formulaire-groupe">

                    <strong>Prénom</strong>

                    <p>
                        <?= htmlspecialchars($utilisateur["prenom"]) ?>
                    </p>

                </div>


                <div class="formulaire-groupe">

                    <strong>Nom</strong>

                    <p>
                        <?= htmlspecialchars($utilisateur["nom"]) ?>
                    </p>

                </div>


                <div class="formulaire-groupe">

                    <strong>Email</strong>

                    <p>
                        <?= htmlspecialchars($utilisateur["email"]) ?>
                    </p>

                </div>


                <div class="formulaire-groupe">

                    <strong>Téléphone</strong>

                    <p>
                        <?= !empty($utilisateur["telephone"])
                            ? htmlspecialchars($utilisateur["telephone"])
                            : "Non renseigné"
                        ?>
                    </p>

                </div>


                <div class="formulaire-groupe">

                    <strong>Type de compte</strong>

                    <p>
                        <?= htmlspecialchars($utilisateur["role"]) ?>
                    </p>

                </div>


                <div class="formulaire-groupe">

                    <strong>Statut</strong>

                    <p>
                        <?= htmlspecialchars($utilisateur["statut"]) ?>
                    </p>

                </div>


                <hr style="margin: 25px 0;">


                <a
                    href="deconnexion.php"
                    class="bouton bouton-principal"
                >
                    Se déconnecter
                </a>

            </div>

        </div>

    </main>

</body>

</html>