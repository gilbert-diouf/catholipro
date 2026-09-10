<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../app/Controllers/UtilisateurController.php";
 
 
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
| Basculer le statut
|--------------------------------------------------------------------------
*/
 
$controller = new UtilisateurController();
$controller->basculerStatut($utilisateur_id, (int) $_SESSION["utilisateur_id"]);
 
 
/*
|--------------------------------------------------------------------------
| Retour vers la liste des utilisateurs
|--------------------------------------------------------------------------
*/
 
header("Location: utilisateurs.php");
exit;
 