<?php
    /**
     * service getUser.php, it take an argument userId, by get or post.
     * and send the user that correspond to that login.
     */

    set_include_path('..'.PATH_SEPARATOR);
    require_once('lib/common_service.php');

    $args = new RequestParameters();
    $args->defineNonEmptyString('userId');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    try{
        $data = new DataLayer();
        $user = $data->getUser($args->userId);
        // if we don't get result then user doesn't exist
        if(!$user){
            produceError("utilisateur userId n'existe pas");
        }else{
            produceResult($user);
        }
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>
