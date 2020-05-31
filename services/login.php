<?php
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');


if ( ! isset($_SESSION['ident'])) {
  $args = new RequestParameters();
  $args->defineNonEmptyString('login');
  $args->defineNonEmptyString('password');

  if (! $args->isValid()){
   produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
   return;
  }

  try{
    $data = new DataLayer();
    $user = $data->login($args->login, $args->password);
    if(is_null($user)){
        produceError("login/password incorrects");
        return;
    }
    else{
        $_SESSION["ident"] = $user;
        produceResult($user);
  }
    }catch (PDOException $e){
        produceError($e->getMessage());
    }
    
} else {
   produceError("le client est déjà connecté");
   return;
}
?>
