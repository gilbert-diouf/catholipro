<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| Vérifier la connexion
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["utilisateur_id"])) {

    header("Location: ../connexion.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Vérifier le rôle
|--------------------------------------------------------------------------
*/

if ($_SESSION["role"] !== "professionnel") {

    header("Location: ../profil.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Variables
|--------------------------------------------------------------------------
*/

$erreur = "";
$succes = "";

$utilisateur_id = $_SESSION["utilisateur_id"];

$categorie_id = "";
$localisation_id = "";
$annees_experience = "";
$description = "";
$specialites = "";
$adresse = "";
$whatsapp = "";
$disponibilite = "";


/*
|--------------------------------------------------------------------------
| Vérifier si un profil existe déjà
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        categorie_id,
        localisation_id,
        annees_experience,
        description,
        specialites,
        adresse,
        whatsapp,
        disponibilite
    FROM profils_professionnels
    WHERE utilisateur_id = ?
    LIMIT 1
";

$stmt = $connexion->prepare($sql);
$stmt->execute([$utilisateur_id]);

$profil = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Charger les données existantes
|--------------------------------------------------------------------------
*/

if ($profil) {

    $categorie_id = $profil["categorie_id"];
    $localisation_id = $profil["localisation_id"];
    $annees_experience = $profil["annees_experience"];
    $description = $profil["description"];
    $specialites = $profil["specialites"];
    $adresse = $profil["adresse"];
    $whatsapp = $profil["whatsapp"];
    $disponibilite = $profil["disponibilite"];
}


/*
|--------------------------------------------------------------------------
| Traitement du formulaire
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $categorie_id = $_POST["categorie_id"] ?? "";
    $localisation_id = $_POST["localisation_id"] ?? "";
    $annees_experience = $_POST["annees_experience"] ?? "";
    $description = trim($_POST["description"] ?? "");
    $specialites = trim($_POST["specialites"] ?? "");
    $adresse = trim($_POST["adresse"] ?? "");
    $whatsapp = trim($_POST["whatsapp"] ?? "");
    $disponibilite = trim($_POST["disponibilite"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        empty($categorie_id) ||
        empty($localisation_id) ||
        $annees_experience === ""
    ) {

        $erreur = "Veuillez remplir tous les champs obligatoires.";

    } elseif (
        !is_numeric($categorie_id) ||
        !is_numeric($localisation_id)
    ) {

        $erreur = "Les informations sélectionnées sont invalides.";

    } elseif (
        !is_numeric($annees_experience) ||
        $annees_experience < 0 ||
        $annees_experience > 60
    ) {

        $erreur = "Le nombre d'années d'expérience est invalide.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Vérifier la catégorie
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT id
            FROM categories
            WHERE id = ?
            AND statut = 'actif'
            LIMIT 1
        ";

        $stmt = $connexion->prepare($sql);
        $stmt->execute([$categorie_id]);

        $categorie_valide = $stmt->fetch();


        /*
        |--------------------------------------------------------------------------
        | Vérifier la localisation
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT id
            FROM localisations
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $connexion->prepare($sql);
        $stmt->execute([$localisation_id]);

        $localisation_valide = $stmt->fetch();


        if (!$categorie_valide) {

            $erreur = "Le métier sélectionné est invalide.";

        } elseif (!$localisation_valide) {

            $erreur = "La localisation sélectionnée est invalide.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | INSERTION OU MODIFICATION
            |--------------------------------------------------------------------------
            */

            if ($profil) {

                /*
                |------------------------------------------------------------------
                | Modification
                |------------------------------------------------------------------
                */

                $sql = "
                    UPDATE profils_professionnels
                    SET
                        categorie_id = ?,
                        localisation_id = ?,
                        annees_experience = ?,
                        description = ?,
                        specialites = ?,
                        adresse = ?,
                        whatsapp = ?,
                        disponibilite = ?
                    WHERE utilisateur_id = ?
                ";

                $stmt = $connexion->prepare($sql);

                $stmt->execute([
                    $categorie_id,
                    $localisation_id,
                    $annees_experience,
                    $description,
                    $specialites,
                    $adresse,
                    $whatsapp,
                    $disponibilite,
                    $utilisateur_id
                ]);

                $succes = "Votre profil professionnel a été mis à jour.";

            } else {

                /*
                |------------------------------------------------------------------
                | Création
                |------------------------------------------------------------------
                */

                $sql = "
                    INSERT INTO profils_professionnels
                    (
                        utilisateur_id,
                        categorie_id,
                        localisation_id,
                        annees_experience,
                        description,
                        specialites,
                        adresse,
                        whatsapp,
                        disponibilite
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";

                $stmt = $connexion->prepare($sql);

                $stmt->execute([
                    $utilisateur_id,
                    $categorie_id,
                    $localisation_id,
                    $annees_experience,
                    $description,
                    $specialites,
                    $adresse,
                    $whatsapp,
                    $disponibilite
                ]);

                $succes = "Votre profil professionnel a été créé avec succès.";

                /*
                |------------------------------------------------------------------
                | Récupérer le nouveau profil
                |------------------------------------------------------------------
                */

                $profil_id = $connexion->lastInsertId();

                $profil = [
                    "id" => $profil_id,
                    "categorie_id" => $categorie_id,
                    "localisation_id" => $localisation_id,
                    "annees_experience" => $annees_experience,
                    "description" => $description,
                    "specialites" => $specialites,
                    "adresse" => $adresse,
                    "whatsapp" => $whatsapp,
                    "disponibilite" => $disponibilite
                ];
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Récupérer les catégories
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        nom
    FROM categories
    WHERE statut = 'actif'
    ORDER BY nom ASC
";

$stmt = $connexion->prepare($sql);
$stmt->execute();

$categories = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Récupérer les localisations
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        region,
        departement,
        ville,
        quartier
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Mon profil professionnel | CatholiPro</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
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


            <!-- Carte -->

            <div class="carte">

                <h2>
                    Mon profil professionnel
                </h2>

                <p>
                    Présentez votre activité à la communauté.
                </p>


                <!-- Message erreur -->

                <?php if (!empty($erreur)): ?>

                    <div class="message-erreur">

                        <?= htmlspecialchars($erreur) ?>

                    </div>

                <?php endif; ?>


                <!-- Message succès -->

                <?php if (!empty($succes)): ?>

                    <div class="message-succes">

                        <?= htmlspecialchars($succes) ?>

                    </div>

                <?php endif; ?>


                <form method="POST">


                    <!-- Métier -->

                    <div class="formulaire-groupe">

                        <label for="categorie_id">
                            Métier
                        </label>

                        <select
                            id="categorie_id"
                            name="categorie_id"
                            required
                        >

                            <option value="">
                                Sélectionnez votre métier
                            </option>

                            <?php foreach ($categories as $categorie): ?>

                                <option
                                    value="<?= $categorie["id"] ?>"
                                    <?= $categorie_id == $categorie["id"]
                                        ? "selected"
                                        : "" ?>
                                >

                                    <?= htmlspecialchars($categorie["nom"]) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Localisation -->

                    <div class="formulaire-groupe">

                        <label for="localisation_id">
                            Localisation
                        </label>

                        <select
                            id="localisation_id"
                            name="localisation_id"
                            required
                        >

                            <option value="">
                                Sélectionnez votre localisation
                            </option>

                            <?php foreach ($localisations as $localisation): ?>

                                <option
                                    value="<?= $localisation["id"] ?>"
                                    <?= $localisation_id == $localisation["id"]
                                        ? "selected"
                                        : "" ?>
                                >

                                    <?= htmlspecialchars(
                                        $localisation["ville"]
                                        . " - "
                                        . $localisation["quartier"]
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- Expérience -->

                    <div class="formulaire-groupe">

                        <label for="annees_experience">
                            Années d'expérience
                        </label>

                        <input
                            type="number"
                            id="annees_experience"
                            name="annees_experience"
                            min="0"
                            max="60"
                            value="<?= htmlspecialchars($annees_experience) ?>"
                            placeholder="Exemple : 5"
                            required
                        >

                    </div>


                    <!-- Description -->

                    <div class="formulaire-groupe">

                        <label for="description">
                            Présentez votre activité
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Décrivez votre activité, votre expérience et les services que vous proposez..."
                        ><?= htmlspecialchars($description) ?></textarea>

                    </div>


                    <!-- Spécialités -->

                    <div class="formulaire-groupe">

                        <label for="specialites">
                            Spécialités
                        </label>

                        <textarea
                            id="specialites"
                            name="specialites"
                            rows="4"
                            placeholder="Exemple : construction, rénovation, carrelage..."
                        ><?= htmlspecialchars($specialites) ?></textarea>

                    </div>


                    <!-- Adresse -->

                    <div class="formulaire-groupe">

                        <label for="adresse">
                            Adresse
                        </label>

                        <input
                            type="text"
                            id="adresse"
                            name="adresse"
                            value="<?= htmlspecialchars($adresse) ?>"
                            placeholder="Votre adresse ou zone d'activité"
                        >

                    </div>


                    <!-- WhatsApp -->

                    <div class="formulaire-groupe">

                        <label for="whatsapp">
                            Numéro WhatsApp
                        </label>

                        <input
                            type="tel"
                            id="whatsapp"
                            name="whatsapp"
                            value="<?= htmlspecialchars($whatsapp) ?>"
                            placeholder="77 000 00 00"
                        >

                    </div>


                    <!-- Disponibilité -->

                    <div class="formulaire-groupe">

                        <label for="disponibilite">
                            Disponibilité
                        </label>

                        <input
                            type="text"
                            id="disponibilite"
                            name="disponibilite"
                            value="<?= htmlspecialchars($disponibilite) ?>"
                            placeholder="Exemple : Disponible du lundi au samedi"
                        >

                    </div>


                    <!-- Bouton -->

                    <button
                        type="submit"
                        class="bouton bouton-principal"
                    >

                        <?= $profil
                            ? "Mettre à jour mon profil"
                            : "Créer mon profil"
                        ?>

                    </button>

                </form>

            </div>

        </div>

    </main>

</body>

</html>

