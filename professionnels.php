<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| Récupération des filtres
|--------------------------------------------------------------------------
*/

$categorie_id = $_GET["categorie_id"] ?? "";
$localisation_id = $_GET["localisation_id"] ?? "";


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


/*
|--------------------------------------------------------------------------
| Recherche des professionnels
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        pp.id,
        pp.utilisateur_id,
        pp.description,
        pp.specialites,
        pp.annees_experience,
        pp.adresse,
        pp.whatsapp,
        pp.disponibilite,
        pp.statut_verification,

        u.prenom,
        u.nom,
        u.photo_profil,

        c.nom AS categorie_nom,
        c.icone AS categorie_icone,

        l.region,
        l.departement,
        l.ville,
        l.quartier

    FROM profils_professionnels pp

    INNER JOIN utilisateurs u
        ON u.id = pp.utilisateur_id

    INNER JOIN categories c
        ON c.id = pp.categorie_id

    INNER JOIN localisations l
        ON l.id = pp.localisation_id

    WHERE pp.statut_verification = 'verifie'
    AND u.statut = 'actif'
";


$params = [];


/*
|--------------------------------------------------------------------------
| Filtre métier
|--------------------------------------------------------------------------
*/

if (!empty($categorie_id)) {

    $sql .= "
        AND pp.categorie_id = ?
    ";

    $params[] = $categorie_id;
}


/*
|--------------------------------------------------------------------------
| Filtre localisation
|--------------------------------------------------------------------------
*/

if (!empty($localisation_id)) {

    $sql .= "
        AND pp.localisation_id = ?
    ";

    $params[] = $localisation_id;
}


/*
|--------------------------------------------------------------------------
| Tri
|--------------------------------------------------------------------------
*/

$sql .= "
    ORDER BY pp.date_creation DESC
";


/*
|--------------------------------------------------------------------------
| Exécution
|--------------------------------------------------------------------------
*/

$stmt = $connexion->prepare($sql);
$stmt->execute($params);

$professionnels = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Professionnels | CatholiPro
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<!-- =====================================================
     IDENTITÉ CATHOLIPRO
     ===================================================== -->

<header>

    <div class="authentification-logo">

        <!-- Croix -->

        <span class="croix">
            ✝
        </span>


        <!-- Nom -->

        <h1>
            CatholiPro
        </h1>


        <!-- Séparateur -->

        <div class="separateur"></div>


        <!-- Slogan -->

        <p>
            La foi au service des talents
        </p>

    </div>

</header>



<!-- =====================================================
     CONTENU PRINCIPAL
     ===================================================== -->

<main>

    <section class="conteneur">


        <!-- =================================================
             TITRE
             ================================================= -->

        <div
            style="
                text-align: center;
                padding: 45px 0 30px;
            "
        >

            <h2>
                Trouver un professionnel
            </h2>

            <p>
                Découvrez les professionnels catholiques
                disponibles près de chez vous.
            </p>

        </div>



        <!-- =================================================
             FORMULAIRE DE RECHERCHE
             ================================================= -->

        <div class="carte">

            <form method="GET">


                <!-- Métier -->

                <div class="formulaire-groupe">

                    <label for="categorie_id">

                        Quel métier recherchez-vous ?

                    </label>


                    <select
                        id="categorie_id"
                        name="categorie_id"
                    >

                        <option value="">

                            Tous les métiers

                        </option>


                        <?php foreach ($categories as $categorie): ?>

                            <option
                                value="<?= $categorie["id"] ?>"
                                <?= $categorie_id == $categorie["id"]
                                    ? "selected"
                                    : "" ?>
                            >

                                <?= htmlspecialchars(
                                    $categorie["nom"]
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>



                <!-- Localisation -->

                <div class="formulaire-groupe">

                    <label for="localisation_id">

                        Où recherchez-vous ?

                    </label>


                    <select
                        id="localisation_id"
                        name="localisation_id"
                    >

                        <option value="">

                            Toutes les localisations

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



                <!-- Bouton -->

                <button
                    type="submit"
                    class="bouton bouton-principal"
                >

                    🔎 Rechercher

                </button>

            </form>

        </div>



        <!-- =================================================
             RÉSULTATS
             ================================================= -->

        <div
            style="
                text-align: center;
                padding: 40px 0 25px;
            "
        >

            <h2>
                Professionnels disponibles
            </h2>

            <p>

                <?= count($professionnels) ?>

                professionnel(s) trouvé(s)

            </p>

        </div>



        <!-- =================================================
             AUCUN RÉSULTAT
             ================================================= -->

        <?php if (empty($professionnels)): ?>

            <div class="carte">

                <h3>
                    Aucun professionnel trouvé
                </h3>

                <p>
                    Aucun professionnel vérifié ne correspond
                    actuellement à votre recherche.
                </p>

            </div>


        <?php else: ?>



            <!-- =================================================
                 LISTE
                 ================================================= -->

            <div
                style="
                    display: grid;
                    grid-template-columns:
                        repeat(auto-fit, minmax(280px, 1fr));
                    gap: 25px;
                    padding-bottom: 50px;
                "
            >


                <?php foreach ($professionnels as $professionnel): ?>

                    <article class="carte">


                        <!-- Photo -->

                        <?php if (
                            !empty(
                                $professionnel["photo_profil"]
                            )
                        ): ?>

                            <div
                                style="
                                    text-align: center;
                                    margin-bottom: 20px;
                                "
                            >

                                <img
                                    src="<?= htmlspecialchars(
                                        $professionnel["photo_profil"]
                                    ) ?>"
                                    alt="Photo de profil"
                                    style="
                                        width: 100px;
                                        height: 100px;
                                        object-fit: cover;
                                        border-radius: 50%;
                                    "
                                >

                            </div>

                        <?php endif; ?>



                        <!-- Nom -->

                        <h3>

                            <?= htmlspecialchars(
                                $professionnel["prenom"]
                            ) ?>

                            <?= htmlspecialchars(
                                $professionnel["nom"]
                            ) ?>

                        </h3>



                        <!-- Métier -->

                        <p>

                            <?php if (
                                !empty(
                                    $professionnel["categorie_icone"]
                                )
                            ): ?>

                                <?= htmlspecialchars(
                                    $professionnel["categorie_icone"]
                                ) ?>

                            <?php endif; ?>


                            <strong>

                                <?= htmlspecialchars(
                                    $professionnel["categorie_nom"]
                                ) ?>

                            </strong>

                        </p>



                        <!-- Localisation -->

                        <p>

                            📍

                            <?= htmlspecialchars(
                                $professionnel["ville"]
                            ) ?>


                            <?php if (
                                !empty(
                                    $professionnel["quartier"]
                                )
                            ): ?>

                                -

                                <?= htmlspecialchars(
                                    $professionnel["quartier"]
                                ) ?>

                            <?php endif; ?>

                        </p>



                        <!-- Expérience -->

                        <p>

                            ⭐

                            <?= (int)
                                $professionnel[
                                    "annees_experience"
                                ]
                            ?>

                            an(s) d'expérience

                        </p>



                        <!-- Description -->

                        <?php if (
                            !empty(
                                $professionnel["description"]
                            )
                        ): ?>

                            <p>

                                <?= htmlspecialchars(
                                    $professionnel["description"]
                                ) ?>

                            </p>

                        <?php endif; ?>



                        <!-- Spécialités -->

                        <?php if (
                            !empty(
                                $professionnel["specialites"]
                            )
                        ): ?>

                            <p>

                                <strong>
                                    Spécialités :
                                </strong>

                                <?= htmlspecialchars(
                                    $professionnel["specialites"]
                                ) ?>

                            </p>

                        <?php endif; ?>



                        <!-- Vérification -->

                        <p>

                            ✓

                            <strong>
                                Professionnel vérifié
                            </strong>

                        </p>



                        <!-- Profil -->

                        <a
                            href="profil-professionnel.php?id=<?= (int) $professionnel["id"] ?>"
                            class="bouton bouton-principal"
                        >
                            Voir le profil
                        </a>


                    </article>

                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>

</main>


</body>

</html>
