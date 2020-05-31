<?php
    /**
     * service to creat a user, it take the argument only from POST request.
     * the arguments are : userId, password, pseudo
     * those arguments are all required and non empty
     * the length of userId and pseudo should be less then 25
     */

    set_include_path('..'.PATH_SEPARATOR);
    require_once('lib/common_service.php');
    require_once('lib/session_start.php');



    $args = new RequestParameters(); // new RequestParameters("post");
    $args->defineNonEmptyString('userId');
    $args->defineNonEmptyString('password');
    $args->defineNonEmptyString('pseudo');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }
    else{
        if(strlen($args->pseudo) > 25){
            produceError("pseudo n'est pas valable");
            return;
        }
        if(! preg_match('/^[a-z\d_]{0,25}$/i', $args->userId)){
            produceError("login n'est pas valable");
            return;
        }
    }

    try{
        $data = new DataLayer();
        $profile = $data->creatUser($args->userId, $args->password, $args->pseudo); 
        if(! $profile){
            produceError("compte n'est pas bien enregistre");
        } else{
            produceResult($profile);
        }
    }
    catch (PDOException $e){
        if($e->getCode() == "23505"){
            produceError("utilisateur $args->userId existe deja");  
        }
        else{
            produceError($e->getMessage());
        }
    }


?>