<?php
 
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
require_once "../app/Controllers/ProfilProfessionnelController.php";
require_once "../app/Security/Csrf.php";
 
 
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
| Vérifier la méthode et le jeton CSRF
|--------------------------------------------------------------------------
*/
 
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !Csrf::verifier($_POST["csrf_token"] ?? null)) {
    header("Location: index.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Récupérer l'identifiant du profil
|--------------------------------------------------------------------------
*/
 
$profil_id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);
 
if (!$profil_id) {
    header("Location: index.php");
    exit;
}
 
 
/*
|--------------------------------------------------------------------------
| Rejeter le professionnel
|--------------------------------------------------------------------------
*/
 
$controller = new ProfilProfessionnelController();
$controller->rejeter($profil_id);
 
 
/*
|--------------------------------------------------------------------------
| Retour vers l'administration
|--------------------------------------------------------------------------
*/
 
header("Location: index.php");
exit;