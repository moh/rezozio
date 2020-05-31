<?php
spl_autoload_register(function ($className) {
    include ("lib/{$className}.class.php");
});
include ("lib/session_start.php");

if (isset($_SESSION['ident'])){
    $user = $_SESSION['ident'];    
}

date_default_timezone_set ('Europe/Paris');
try{
    $data = new DataLayer();
    require ('views/allForm.php');
} catch (PDOException $e){
    $errorMessage = $e->getMessage();
    require("views/pageErreur.php");
}

?>
