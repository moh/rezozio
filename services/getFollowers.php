<?php

    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur
    
    try{
        $data = new DataLayer();
        $users = $data->getFollowers($current);
        produceResult($users);
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>