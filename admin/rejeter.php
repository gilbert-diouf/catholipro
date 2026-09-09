<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../config/database.php";
 
 
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
 
    header("Location: index.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Vérifier que le profil existe et est en attente
|--------------------------------------------------------------------------
*/
 
$sql = "
    SELECT id
    FROM profils_professionnels
    WHERE id = ?
    AND statut_verification = 'en_attente'
    LIMIT 1
";
 
$stmt = $connexion->prepare($sql);
$stmt->execute([$profil_id]);
 
$profil = $stmt->fetch();
 
 
/*
|--------------------------------------------------------------------------
| Si le profil n'existe pas ou n'est plus en attente
|--------------------------------------------------------------------------
*/
 
if (!$profil) {
 
    header("Location: index.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Rejeter le professionnel
|--------------------------------------------------------------------------
*/
 
$sql = "
    UPDATE profils_professionnels
 
    SET statut_verification = 'rejete'
 
    WHERE id = ?
    AND statut_verification = 'en_attente'
";
 
$stmt = $connexion->prepare($sql);
$stmt->execute([$profil_id]);
 
 
/*
|--------------------------------------------------------------------------
| Retour vers l'administration
|--------------------------------------------------------------------------
*/
 
header("Location: index.php");
exit;
 