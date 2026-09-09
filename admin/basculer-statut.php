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
| Récupérer l'identifiant de l'utilisateur
|--------------------------------------------------------------------------
*/
 
$utilisateur_id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);
 
if (!$utilisateur_id) {
 
    header("Location: utilisateurs.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Empêcher un administrateur de se suspendre lui-même
|--------------------------------------------------------------------------
*/
 
if ($utilisateur_id === (int) $_SESSION["utilisateur_id"]) {
 
    header("Location: utilisateurs.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Récupérer le statut actuel
|--------------------------------------------------------------------------
*/
 
$sql = "
    SELECT id, statut
    FROM utilisateurs
    WHERE id = ?
    LIMIT 1
";
 
$stmt = $connexion->prepare($sql);
$stmt->execute([$utilisateur_id]);
 
$utilisateur = $stmt->fetch();
 
if (!$utilisateur) {
 
    header("Location: utilisateurs.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Basculer le statut
|--------------------------------------------------------------------------
*/
 
$nouveau_statut = $utilisateur["statut"] === "actif" ? "suspendu" : "actif";
 
$sql = "
    UPDATE utilisateurs
    SET statut = ?
    WHERE id = ?
";
 
$stmt = $connexion->prepare($sql);
$stmt->execute([$nouveau_statut, $utilisateur_id]);
 
 
/*
|--------------------------------------------------------------------------
| Retour vers la liste des utilisateurs
|--------------------------------------------------------------------------
*/
 
header("Location: utilisateurs.php");
exit;
 