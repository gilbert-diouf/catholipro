<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| Récupérer l'identifiant du profil
|--------------------------------------------------------------------------
*/

$profil_id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


/*
|--------------------------------------------------------------------------
| Vérifier l'identifiant
|--------------------------------------------------------------------------
*/

if (!$profil_id) {

    header("Location: professionnels.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer le professionnel
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
        u.email,
        u.telephone,
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

    WHERE pp.id = ?
    AND pp.statut_verification = 'verifie'
    AND u.statut = 'actif'

    LIMIT 1
";


$stmt = $connexion->prepare($sql);
$stmt->execute([$profil_id]);

$professionnel = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Vérifier que le professionnel existe
|--------------------------------------------------------------------------
*/

if (!$professionnel) {

    header("Location: professionnels.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Préparer le numéro WhatsApp
|--------------------------------------------------------------------------
*/

$whatsapp = $professionnel["whatsapp"];

$whatsapp_nettoye = preg_replace(
    "/[^0-9+]/",
    "",
    $whatsapp
);


/*
|--------------------------------------------------------------------------
| Préparer le message WhatsApp
|--------------------------------------------------------------------------
*/

$message = urlencode(
    "Bonjour " .
    $professionnel["prenom"] .
    ", je vous contacte via CatholiPro concernant vos services de " .
    $professionnel["categorie_nom"] .
    "."
);

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

        <?= htmlspecialchars(
            $professionnel["prenom"]
        ) ?>

        <?= htmlspecialchars(
            $professionnel["nom"]
        ) ?>

        | CatholiPro

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

        <span class="croix">
            ✝
        </span>

        <h1>
            CatholiPro
        </h1>

        <div class="separateur"></div>

        <p>
            La foi au service des talents
        </p>

    </div>

</header>



<!-- =====================================================
     PROFIL PROFESSIONNEL
     ===================================================== -->

<main>

    <section class="conteneur">


        <!-- Retour -->

        <div
            style="
                padding-top: 30px;
                margin-bottom: 20px;
            "
        >

            <a
                href="professionnels.php"
                class="lien"
            >
                ← Retour aux professionnels
            </a>

        </div>



        <!-- =================================================
             CARTE PRINCIPALE
             ================================================= -->

        <div
            class="carte"
            style="
                max-width: 850px;
                margin: 0 auto 50px;
            "
        >


            <!-- =================================================
                 PHOTO
                 ================================================= -->

            <div
                style="
                    text-align: center;
                    margin-bottom: 25px;
                "
            >

                <?php if (
                    !empty(
                        $professionnel["photo_profil"]
                    )
                ): ?>

                    <img
                        src="<?= htmlspecialchars(
                            $professionnel["photo_profil"]
                        ) ?>"
                        alt="Photo de <?= htmlspecialchars(
                            $professionnel["prenom"]
                        ) ?>"
                        style="
                            width: 150px;
                            height: 150px;
                            object-fit: cover;
                            border-radius: 50%;
                        "
                    >

                <?php else: ?>

                    <div
                        style="
                            width: 150px;
                            height: 150px;
                            border-radius: 50%;
                            margin: 0 auto;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 60px;
                            background: #f1f1f1;
                        "
                    >

                        👤

                    </div>

                <?php endif; ?>

            </div>



            <!-- =================================================
                 IDENTITÉ
                 ================================================= -->

            <div
                style="
                    text-align: center;
                "
            >

                <h2>

                    <?= htmlspecialchars(
                        $professionnel["prenom"]
                    ) ?>

                    <?= htmlspecialchars(
                        $professionnel["nom"]
                    ) ?>

                    ✓

                </h2>


                <p
                    style="
                        font-size: 20px;
                        font-weight: 600;
                    "
                >

                    <?php if (
                        !empty(
                            $professionnel["categorie_icone"]
                        )
                    ): ?>

                        <?= htmlspecialchars(
                            $professionnel["categorie_icone"]
                        ) ?>

                    <?php endif; ?>


                    <?= htmlspecialchars(
                        $professionnel["categorie_nom"]
                    ) ?>

                </p>


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


                <p>

                    ✓

                    <strong>
                        Professionnel vérifié
                    </strong>

                </p>

            </div>



            <hr style="margin: 30px 0;">



            <!-- =================================================
                 EXPÉRIENCE
                 ================================================= -->

            <div>

                <h3>
                    Expérience professionnelle
                </h3>

                <p>

                    ⭐

                    <strong>

                        <?= (int)
                            $professionnel[
                                "annees_experience"
                            ]
                        ?>

                        an(s)

                    </strong>

                    d'expérience

                </p>

            </div>



            <!-- =================================================
                 DESCRIPTION
                 ================================================= -->

            <?php if (
                !empty(
                    $professionnel["description"]
                )
            ): ?>

                <div style="margin-top: 30px;">

                    <h3>
                        À propos
                    </h3>

                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $professionnel[
                                    "description"
                                ]
                            )
                        ) ?>

                    </p>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 SPÉCIALITÉS
                 ================================================= -->

            <?php if (
                !empty(
                    $professionnel["specialites"]
                )
            ): ?>

                <div style="margin-top: 30px;">

                    <h3>
                        Mes spécialités
                    </h3>

                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $professionnel[
                                    "specialites"
                                ]
                            )
                        ) ?>

                    </p>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 LOCALISATION
                 ================================================= -->

            <div style="margin-top: 30px;">

                <h3>
                    Zone d'activité
                </h3>

                <p>

                    📍

                    <?= htmlspecialchars(
                        $professionnel["region"]
                    ) ?>

                    <?php if (
                        !empty(
                            $professionnel["departement"]
                        )
                    ): ?>

                        — 

                        <?= htmlspecialchars(
                            $professionnel["departement"]
                        ) ?>

                    <?php endif; ?>

                </p>

                <p>

                    <?= htmlspecialchars(
                        $professionnel["ville"]
                    ) ?>


                    <?php if (
                        !empty(
                            $professionnel["quartier"]
                        )
                    ): ?>

                        — 

                        <?= htmlspecialchars(
                            $professionnel["quartier"]
                        ) ?>

                    <?php endif; ?>

                </p>


                <?php if (
                    !empty(
                        $professionnel["adresse"]
                    )
                ): ?>

                    <p>

                        <?= htmlspecialchars(
                            $professionnel["adresse"]
                        ) ?>

                    </p>

                <?php endif; ?>

            </div>



            <!-- =================================================
                 DISPONIBILITÉ
                 ================================================= -->

            <?php if (
                !empty(
                    $professionnel["disponibilite"]
                )
            ): ?>

                <div style="margin-top: 30px;">

                    <h3>
                        Disponibilité
                    </h3>

                    <p>

                        🟢

                        <?= htmlspecialchars(
                            $professionnel[
                                "disponibilite"
                            ]
                        ) ?>

                    </p>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 CONTACT
                 ================================================= -->

            <div
                style="
                    margin-top: 35px;
                    text-align: center;
                "
            >

                <?php if (!empty($whatsapp_nettoye)): ?>

                    <a
                        href="https://wa.me/<?= htmlspecialchars(
                            ltrim(
                                $whatsapp_nettoye,
                                "+"
                            )
                        ) ?>?text=<?= $message ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bouton bouton-principal"
                    >

                        📱 Contacter sur WhatsApp

                    </a>

                <?php endif; ?>

            </div>


        </div>

    </section>

</main>


</body>

</html>
