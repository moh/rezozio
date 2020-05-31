<?php

    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur
    $args = new RequestParameters();
    
    // les parametre pour ce service
    $args->defineInt('before',['default'=>0]); // the id of the massage ??
    $args->defineInt('count',['default'=>15, 'min_range'=>1]);

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    try{
        $data = new DataLayer();
        $maxId = $data->getMaxMessageId()["max"]; // max id of messages
        
        // if before is not given, then choose from maxId
        if($args->before == 0){
            $msgs = $data->findFollowedMessages($current, $maxId + 1, $args->count);
        }
        else{
            $msgs = $data->findFollowedMessages($current, $args->before, $args->count);
        }
        produceResult($msgs);
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>