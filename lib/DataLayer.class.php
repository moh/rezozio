<?php
require_once("lib/db_parms.php");
   
Class DataLayer{
    private $connexion;
    public function __construct(){

            $this->connexion = new PDO(
                       DB_DSN, DB_USER, DB_PASSWORD,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                       ]
                     );

    }

    /*
    --------------------------------
    Profile related functions 
    --------------------------------
    */

    /**
     * getUser take the login of the user in parameter ($userId)
     * and return the login and the pseudo that correpsond to the user.
     */
    function getUser($userId){
      $sql = <<<EOD
      SELECT users.login as "userId", users.pseudo 
      FROM rezozio.users
      WHERE  login=:userId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId', $userId);
      $stmt->execute();
      return $stmt->fetch();
    }

    /**
     * getProfileConnected take the login of the user ($userId),
     * and the login of the current user ($current) 
     * 
     * and it returns the users profile, login, pseudo and description,
     * plus a boolean that indicates if he is followed by current user,
     * and a second bool that indicates if he follow the current user.
     * 
     * Function will be used when a user is coonnected
     */
    function getProfileConnected($userId, $current){
      $sql = <<<EOD
      select
      users.login as "userId", users.pseudo, users.description,
      s1.target is not null as "followed",

      s2.target is not null as "isFollower"
      from rezozio.users
      left join rezozio.subscriptions as s1 on users.login = s1.target and s1.follower = :current

      left join rezozio.subscriptions as s2 on users.login = s2.follower and s2.target = :current
      where users.login = :userId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId', $userId);
      $stmt->bindValue(':current', $current);
      $stmt->execute();
      return $stmt->fetch();
    }

    /**
     * getProfileNotConnected take the login of the user ($userId),
     * and the login of the current user ($current) 
     * 
     * and it returns the users profile, login, pseudo and description,
     * 
     * Function will be used when not coonnected
     */
    function getProfileNotConnected($userId){
      $sql = <<<EOD
      select
      users.login as "userId", users.pseudo, users.description
      from rezozio.users
      where users.login = :userId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId', $userId);
      $stmt->execute();
      return $stmt->fetch();
    }


    /**
     * setProfile takes an associative array and a string as parameter.
     * the associative array has the password pseudo and description keys.
     * if any value of these fields is empty then we don't change its value.
     * 
     * The second parameter represent the login of the current user.
     * 
     * return the userId and the pseudo of the modified user.
     */
    function setProfile($data, $login){
      $query = "";
      foreach($data as $key=>$val){
            if(!empty($val)){
                  $query .= $key."=:".$key.",";
            }
      }
      $query = substr($query, 0, -1);

      $sql = <<<EOD
      update rezozio.users 
      set 
      $query
      where login= :login
      returning login as userId, pseudo
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      
      foreach($data as $key=>$val){
            if(!empty($val)){
                  if($key == "password"){
                        $stmt->bindValue(":password",password_hash($val, CRYPT_BLOWFISH));
                  } else{
                  $stmt->bindValue(':'.$key, $val);
                  }
            }
      }
      $stmt->execute();
      return $stmt->fetch();
    }

    /*
    ----------------------
    GET messages part
    ----------------------
    */


    /**
     * getMessage take the messageId in parameter, 
     * and return the informations that correpsond to that message.
     * 
     */
    function getMessage($messageId){
      $sql = <<<EOD
      select
      messages.id as "messageId", messages.author, users.pseudo, messages.content, messages.datetime
      from rezozio.messages 
      left join rezozio.users on rezozio.messages.author = rezozio.users.login
      where messages.id=:messageId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':messageId', $messageId);
      $stmt->execute();
      return $stmt->fetch();
    }

    /**
     * findMessages takes in parameter the name of the author, 
     * the max id of the message, and the maximum number of result.
     * 
     * if the author is empty, we don't include his name in the filter.
     * 
     * return an array of messageId, author,pseudo, content and datetime.
     * 
     */
    function findMessages($author, $before, $count){
      $filter = " and messages.author = :author ";
      if(empty($author)){$filter = " ";} // if author is empry so no filter
      $sql = <<<EOD
      select
      messages.id as "messageId", messages.author, users.pseudo, messages.content, messages.datetime
      from rezozio.messages 
      left join rezozio.users on rezozio.messages.author = rezozio.users.login
      where messages.id < :before $filter
      limit :count
EOD;
      $stmt = $this->connexion->prepare($sql);
      if(!empty($author)){ 
            $stmt->bindValue(':author', $author);
      }
      $stmt->bindValue(':before', $before);
      $stmt->bindValue(':count', $count);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /**
     * this function return the messages of the authors that are followed by 
     *  current user, where the id is less then before.
     */
    function findFollowedMessages($current, $before, $count){
      $sql = <<<EOD
      select
      messages.id as "messageId", messages.author, users.pseudo, messages.content, messages.datetime
      from rezozio.messages 
      left join rezozio.users on rezozio.messages.author = rezozio.users.login
      where messages.id < :before 
      and 
      messages.author in (select target from rezozio.subscriptions where follower= :current) 
      limit :count
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':before', $before);
      $stmt->bindValue(':count', $count);
      $stmt->bindValue(':current', $current);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /*
    --------------------
    Add message part
    --------------------
    */

    /**
     * add a message to the database, the author is the 
     * current user, and the description is the source.
     * 
     */
    function postMessage($current, $source){
      $sql = <<<EOD
      insert into rezozio.messages
      (author, content)
      values
      (:current, :source)
      returning id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current', $current);
      $stmt->bindValue(':source', $source);
      $stmt->execute();
      return $stmt->fetch();
    }

    /*
    ----------------------
    Search data part
    ----------------------
    */

    /**
     * find a user that have the searchedString in his login
     * or in his pseudo.
     * 
     */
    function findUsers($searchedString){
      $sql = <<<EOD
      select
      login as "userId", pseudo
      from rezozio.users
      where login like :searchedString
      or pseudo like :searchedString
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':searchedString', "%".$searchedString."%");
      $stmt->execute();
      return $stmt->fetchAll();

    }

//concat("%",:searchedString,"%")

    /*
    ---------------------
    User account part
    ---------------------
    */

    /**
     * Create a user with given userId (login),
     * password and the pseudo of the new user.
     * 
     */
    function creatUser($userId, $password, $pseudo){
      $sql = <<<EOD
      insert 
      into rezozio.users 
      (login, password, pseudo)
      values 
      (:userId, :password, :pseudo)
      returning login as "userId", pseudo
EOD;
      $stmt = $this->connexion->prepare($sql); // préparation de la requête
      $stmt->bindValue(":userId",$userId);
      $stmt->bindValue(":password",password_hash($password, CRYPT_BLOWFISH));
      $stmt->bindValue(":pseudo",$pseudo);
      $stmt->execute();
      return $stmt->fetch();
  }

  /**
    * Test d'authentification
    * $login, $password : authentifiants
    * résultat :
    *    Instance de Personne représentant l'utilsateur authentifié, en cas de succès
    *    NULL en cas d'échec
    */
    function login($login, $password){ // version password hash
      $sql = <<<EOD
      select
      login, password, pseudo
      from rezozio.users
      where login = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      $stmt->execute();
      $info = $stmt->fetch();
      if ($info && crypt($password, $info['password']) == $info['password'])
            return $info["login"];
      else
        return NULL;
  }

  /*
  -----------------------------
  Following part
  -----------------------------
  */
  /**
   * this function take two users login as parameter,
   * where the current user will follow the target user.
   * 
   * It creates this relation between the two users
   */
  function follow($current, $target){
      $sql = <<<EOD
      insert 
      into rezozio.subscriptions 
      (follower, target)
      values 
      (:current, :target)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current', $current);
      $stmt->bindValue(':target', $target);
      $stmt->execute();
      return $stmt->rowCount() == 1;
  }

  /**
   * the function unfollow takes two users login as parameters.
   * It removes a following relation between two users.
   * 
   */
  function unfollow($current, $target){
      $sql = <<<EOD
      delete 
      from rezozio.subscriptions 
      where
      follower = :current and target = :target
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current', $current);
      $stmt->bindValue(':target', $target);
      $stmt->execute();
      return $stmt->rowCount() == 1;
  }

  /**
   * getFollowers takes a user login in parameter,
   * and return an array of users following the current user,
   * and a boolean value that indicate if the current user 
   * follow also the other user.
   * 
   */
  function getFollowers($current){
      $sql = <<<EOD
      select
      users.login as "userId", users.pseudo,
      exists(select * from rezozio.subscriptions 
      where target = users.login and follower = :current) as mutual
      from rezozio.users
      where 
      users.login in 
      (select follower from rezozio.subscriptions where target = :current)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current', $current);
      $stmt->execute();
      return $stmt->fetchAll();
  }

  /**
   * getSubscriptions takes a user login in parameter,
   * and return an array of users that are followed by 
   * the current user.
   * 
   */
  function getSubscriptions($current){
      $sql = <<<EOD
      select
      users.login as "userId", users.pseudo
      from rezozio.users
      where 
      users.login in 
      (select target from rezozio.subscriptions where follower = :current)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':current', $current);
      $stmt->execute();
      return $stmt->fetchAll();
  }


  /*
  ----------------------------------------------------------------------------
  Get avatar and upload avatar Part
  ----------------------------------------------------------------------------
  */

  /**
   * uploadAvatarSmall will upload the given image to the column that is specified to 
   * the avatar of size 48x48 
   * 
   */
  function uploadAvatarSmall($imageSpec, $login){
      
          $sql = <<<EOD
          update rezozio.users 
          set 
          avatar_small=:avatar, avatar_type=:mimetype 
          where login = :login;
EOD;
          $stmt = $this->connexion->prepare($sql); // préparation de la requête
          $stmt->bindValue(":login",$login, PDO::PARAM_STR);
          $stmt->bindValue(":avatar", $imageSpec["data"], PDO::PARAM_LOB);
          $stmt->bindValue(":mimetype", $imageSpec["mimetype"]);
          $stmt->execute();
          
  }

  /**
   * uploadAvatarLarge will upload the given image to the column that is specified to
   * the avatar of size 256x256
   */
  function uploadAvatarLarge($imageSpec, $login){
          $sql = <<<EOD
          update rezozio.users 
          set 
          avatar_large=:avatar, avatar_type=:mimetype 
          where login = :login;
EOD;
          $stmt = $this->connexion->prepare($sql); // préparation de la requête
          $stmt->bindValue(":login",$login, PDO::PARAM_STR);
          $stmt->bindValue(":avatar", $imageSpec["data"], PDO::PARAM_LOB);
          $stmt->bindValue(":mimetype", $imageSpec["mimetype"]);
          $stmt->execute();
          
  }

  /**
   * getAvatarSmall, get the small avatar of the user $login
   * 
   */
  function getAvatarSmall($login){
      $sql = <<<EOD
          select 
          avatar_small, avatar_type 
          from 
          rezozio.users where login=:login;
EOD;
      $stmt = $this->connexion->prepare($sql); // préparation de la requête
      $stmt->bindValue(':login',$login, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->bindColumn('avatar_type', $mimetype);
      $stmt->bindColumn('avatar_small', $flux, PDO::PARAM_LOB);
      $res = $stmt->fetch();
      if($res ){
          return ["data"=>$flux, "mimetype"=> $mimetype];
      }
      return false;
  }

  /**
   * getAvatarLarge, get the large avatar of the user login
   * 
   */
  function getAvatarLarge($login){   
      $sql = <<<EOD
          select 
          avatar_large, avatar_type 
          from 
          rezozio.users where login=:login;
EOD;
      $stmt = $this->connexion->prepare($sql); // préparation de la requête
      $stmt->bindValue(':login',$login, PDO::PARAM_STR);
      $stmt->execute();
      $stmt->bindColumn('avatar_type', $mimetype);
      $stmt->bindColumn('avatar_large', $flux, PDO::PARAM_LOB);
      $res = $stmt->fetch();
      if($res ){
          return ["data"=>$flux, "mimetype"=> $mimetype];
      }
      return false;
  }


  // ------------------------------------------
  // Extra
  // ------------------------------------------
  
  /**
   * get the max id of the table messages
   * 
   */
  function getMaxMessageId(){
      $sql = <<<EOD
        select max(id) from rezozio.messages
EOD;
      $stmt = $this->connexion->prepare($sql); // préparation de la requête
      $stmt->execute();
      return $stmt->fetch();
  }

}
?>
