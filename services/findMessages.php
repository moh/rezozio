<?php
    /**
     * Service to find messages based on their id and the author.
     * if the author is empty, then this filter is disabled.
     */

    set_include_path('..'.PATH_SEPARATOR);
    require_once('lib/common_service.php');
    require_once('lib/session_start.php');



    $args = new RequestParameters();
    // les parametre pour ce service
    $args->defineString('author',['default'=>'']);
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
            $msgs = $data->findMessages($args->author, $maxId + 1, $args->count);
        }
        else{
            $msgs = $data->findMessages($args->author, $args->before, $args->count);
        }
        // check if the author existe
        $user = $data->getUser($args->author);
        $author = $args->author;
        
        if(!$user && !empty($author)){
            produceError("l'utilisateur author n'existe pas");
            return;
        }
        produceResult($msgs);
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }

?>