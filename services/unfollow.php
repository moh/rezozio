<?php
    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur
    $args = new RequestParameters();
    
    // les parametre pour ce service
    $args->defineNonEmptyString('target');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    try{
        $data = new DataLayer();
        if($data->unfollow($current, $args->target)){
            produceResult(true);
        }
        else{
            produceError("Cet abonnement n'existait pas");
        }
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>