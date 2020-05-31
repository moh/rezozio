<?php

    set_include_path('..'.PATH_SEPARATOR);
    require_once("lib/watchdog_service.php");
    $current = $_SESSION["ident"]; // l'utilisateur
    $args = new RequestParameters();// post
    
    // les parametre pour ce service
    $args->defineString('password', ['default'=>'']);
    $args->defineString('pseudo', ['default'=>'']);
    $args->defineString('description', ['default'=>'']);

    if (! $args->isValid()){
        produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
        return;
    }

    try{
        $data = new DataLayer();
        $data_arg = ["password" => $args->password, "pseudo" => $args->pseudo, "description" => $args->description];
        $user = $data->setProfile($data_arg, $current);
        produceResult($user);
    }
    catch (PDOException $e){
        produceError($e->getMessage());
    }
?>