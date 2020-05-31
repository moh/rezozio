<?php
    /**
     * Service to find a user based on a substring of its login or the pseudo.
     * the service take an argument searchedString by post or get request, that 
     * should have a length of minimum 3.
     * the servies return the users profile that correspond to that search.
     */

    set_include_path('..'.PATH_SEPARATOR);
    require_once('lib/common_service.php');
    require_once('lib/session_start.php');



    $args = new RequestParameters();
    $args->defineNonEmptyString('searchedString');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }
    if(strlen($args->searchedString) < 3){
        produceError("parametre incorrecte: longueur moin que 3");
        return;
    }

    try{
        $data = new DataLayer();
        $profiles = $data->findUsers($args->searchedString);
        if(empty($profiles)){
            produceResult(array());
        }
        else{
            produceResult($profiles);
        }
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }

?>