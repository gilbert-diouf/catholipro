<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| Récupérer les professionnels en attente
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
        pp.date_creation,

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

    WHERE pp.statut_verification = 'en_attente'

    ORDER BY pp.date_creation ASC
";


$stmt = $connexion->prepare($sql);
$stmt->execute();

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
        Administration | CatholiPro
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
     CONTENU ADMINISTRATION
     ===================================================== -->

<main>

    <section class="conteneur">


        <!-- Titre -->

        <div
            style="
                text-align: center;
                padding: 45px 0 30px;
            "
        >

            <h2>
                Administration
            </h2>

            <p>
                Gestion des professionnels
            </p>

        </div>



        <!-- =================================================
             PROFESSIONNELS EN ATTENTE
             ================================================= -->

        <div
            style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
            "
        >

            <h3>
                Professionnels en attente
            </h3>

            <span>
                <?= count($professionnels) ?>
                en attente
            </span>

        </div>



        <?php if (empty($professionnels)): ?>

            <div class="carte">

                <h3>
                    Aucun profil en attente
                </h3>

                <p>
                    Tous les professionnels ont été traités.
                </p>

            </div>


        <?php else: ?>


            <!-- =================================================
                 LISTE DES PROFESSIONNELS
                 ================================================= -->

            <div
                style="
                    display: grid;
                    gap: 25px;
                    padding-bottom: 50px;
                "
            >


                <?php foreach ($professionnels as $professionnel): ?>


                    <article class="carte">


                        <!-- =====================================
                             EN-TÊTE DU PROFIL
                             ===================================== -->

                        <div
                            style="
                                display: flex;
                                gap: 20px;
                                align-items: center;
                                margin-bottom: 20px;
                            "
                        >


                            <!-- Photo -->

                            <?php if (
                                !empty(
                                    $professionnel["photo_profil"]
                                )
                            ): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $professionnel["photo_profil"]
                                    ) ?>"
                                    alt="Photo"
                                    style="
                                        width: 90px;
                                        height: 90px;
                                        object-fit: cover;
                                        border-radius: 50%;
                                    "
                                >

                            <?php else: ?>

                                <div
                                    style="
                                        width: 90px;
                                        height: 90px;
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        background: #f1f1f1;
                                        font-size: 35px;
                                    "
                                >
                                    👤
                                </div>

                            <?php endif; ?>



                            <!-- Identité -->

                            <div>

                                <h3>

                                    <?= htmlspecialchars(
                                        $professionnel["prenom"]
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $professionnel["nom"]
                                    ) ?>

                                </h3>


                                <p>

                                    <?php if (
                                        !empty(
                                            $professionnel[
                                                "categorie_icone"
                                            ]
                                        )
                                    ): ?>

                                        <?= htmlspecialchars(
                                            $professionnel[
                                                "categorie_icone"
                                            ]
                                        ) ?>

                                    <?php endif; ?>


                                    <strong>

                                        <?= htmlspecialchars(
                                            $professionnel[
                                                "categorie_nom"
                                            ]
                                        ) ?>

                                    </strong>

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
                                            $professionnel[
                                                "quartier"
                                            ]
                                        ) ?>

                                    <?php endif; ?>

                                </p>

                            </div>

                        </div>



                        <!-- =====================================
                             INFORMATIONS
                             ===================================== -->

                        <div>

                            <p>

                                <strong>
                                    Expérience :
                                </strong>

                                <?= (int)
                                    $professionnel[
                                        "annees_experience"
                                    ]
                                ?>

                                an(s)

                            </p>


                            <?php if (
                                !empty(
                                    $professionnel["description"]
                                )
                            ): ?>

                                <p>

                                    <strong>
                                        Description :
                                    </strong>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $professionnel[
                                                "description"
                                            ]
                                        )
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (
                                !empty(
                                    $professionnel["specialites"]
                                )
                            ): ?>

                                <p>

                                    <strong>
                                        Spécialités :
                                    </strong>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $professionnel[
                                                "specialites"
                                            ]
                                        )
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (
                                !empty(
                                    $professionnel["whatsapp"]
                                )
                            ): ?>

                                <p>

                                    <strong>
                                        WhatsApp :
                                    </strong>

                                    <?= htmlspecialchars(
                                        $professionnel[
                                            "whatsapp"
                                        ]
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (
                                !empty(
                                    $professionnel["disponibilite"]
                                )
                            ): ?>

                                <p>

                                    <strong>
                                        Disponibilité :
                                    </strong>

                                    <?= htmlspecialchars(
                                        $professionnel[
                                            "disponibilite"
                                        ]
                                    ) ?>

                                </p>

                            <?php endif; ?>

                        </div>



                        <!-- =====================================
                             STATUT
                             ===================================== -->

                        <div
                            style="
                                margin-top: 25px;
                                padding: 12px;
                                background: #fff8e1;
                                border-radius: 8px;
                            "
                        >

                            🕐

                            <strong>
                                En attente de vérification
                            </strong>

                        </div>



                        <!-- =====================================
                             ACTIONS
                             ===================================== -->

                        <div
                            style="
                                display: flex;
                                gap: 15px;
                                margin-top: 25px;
                                flex-wrap: wrap;
                            "
                        >

                            <a
                                href="admin-verifier.php?id=<?= (int) $professionnel["id"] ?>"
                                class="bouton bouton-principal"
                            >

                                ✓ Vérifier

                            </a>


                            <a
                                href="admin-rejeter.php?id=<?= (int) $professionnel["id"] ?>"
                                class="bouton"
                            >

                                ✕ Rejeter

                            </a>

                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php endif; ?>


    </section>

</main>


</body>

</html>
