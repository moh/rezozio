<?php
    /**
     * service getUser.php, it take an argument userId, by get or post.
     * and send the user that correspond to that login.
     */

    set_include_path('..'.PATH_SEPARATOR);
    require_once('lib/common_service.php');

    $args = new RequestParameters();
    $args->defineInt('messageId');

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    try{
        $data = new DataLayer();
        $message = $data->getMessage($args->messageId);
        
        // if we don't get result then message doesn't exist
        if(!$message){
            produceError("message messageId n'existe pas");
        }else{
            produceResult($message);
        }
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>
