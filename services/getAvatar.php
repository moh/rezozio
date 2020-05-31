<?php
  set_include_path('..'.PATH_SEPARATOR);
  require_once("lib/common_service.php");
  $args = new RequestParameters();

  // les parametre pour ce service
  $args->defineNonEmptyString('userId');
  $args->defineString('size', ['default'=>'small']);

  if (! $args->isValid()){
      produceError('argument invalide --> '.implode(', ',$args->getErrorMessages()));
      return;
  }


try{
  $data = new DataLayer();
  if($args->size == "small"){
    $descFile = $data->getAvatarSmall($args->userId);
  }
  elseif($args->size == "large"){
    $descFile = $data->getAvatarLarge($args->userId);
  }

  if ($descFile){ // l'utilisateur existe
    // si l'avatar est NULL, renvoyer l'avatar par défaut :
    $flux = is_null($descFile['data']) ? fopen('../images/default_profile.png','r') : $descFile['data'];
    $mimeType = is_null($descFile['data']) ? 'image/png' : $descFile['mimetype'];
    
    header("Content-type: $mimeType");
    fpassthru($flux);
    exit();
  }
  else
    produceError('Utilisateur inexistant');
}
catch (PDOException $e){
  produceError($e->getMessage());
}

?>
