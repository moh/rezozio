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
        if($data->follow($current, $args->target)){
            produceResult(true);
        }
        else{
            produceError("abonnement n'est pas bien cree");
        }
    }
    catch (PDOException $e){
        // l'abonnement existe deja
        if($e->getCode() == "23505"){
            produceError("cet abonnement existait déjà");
            return;
        }
        elseif($e->getCode() == "23503"){
            produceError("l'utilisateur target n'existe pas");
            return;
        }

        produceError($e->getMessage());
    }
?>