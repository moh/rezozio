<?php

    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur
    $args = new RequestParameters();
    
    // les parametre pour ce service
    $args->defineNonEmptyString('source');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    if(strlen($args->source) > 280){
        produceError("parametre incorrect: plus que 280 charactere");
        return;
    }

    try{
        $data = new DataLayer();
        $id = $data->postMessage($current, $args->source);
        produceResult($id);
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }



?>