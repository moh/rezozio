<?php
    /**
     * Service to return the users followed by the connected user,
     * the result is an array of user, each user is an associated array 
     * of keys userId and pseudo.
     * 
     */
    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur
    
    try{
        $data = new DataLayer();
        $users = $data->getSubscriptions($current);
        produceResult($users);
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>