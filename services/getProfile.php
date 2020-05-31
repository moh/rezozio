<?php
    set_include_path('..'.PATH_SEPARATOR);
    require_once('lib/common_service.php');
    require_once('lib/session_start.php');

    // if we are in connected mode
    if(isset($_SESSION["ident"])){
        $current = $_SESSION["ident"];
    }


    $args = new RequestParameters();
    $args->defineNonEmptyString('userId');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    try{
        $data = new DataLayer();
        
        // if we are in connected mode
        if(isset($current)){
            $profile = $data->getProfileConnected($args->userId, $current);
        } else{
            $profile = $data->getProfileNotConnected($args->userId);    
        }

        // if we don't get result then user doesn't exist
        if(!$profile){
            produceError("utilisateur userId n'existe pas");
        } else{
            produceResult($profile);
        }
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }



?>