<?php
    $data_user = "";
    if(isset($user)){
        $data_user = 'data-user = "'.$user.'"';
    }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Rezozio</title>
    <link rel="stylesheet" href="css/index.css" />
    <script src="js/fetchUtils.js"></script>
    <script src = "js/init_page.js"></script>
    <script src="js/gestion_log.js"></script>
    <script src="js/show_update_profile.js"></script>
    <script src="js/publish_show_message.js"></script>

</head>

<body <?php echo $data_user; ?> >
    <header>
        <div id = "logoName">
            <img src="images/logo.png" alt="logo" id = "logo" />
            <span id = "brandName">Rezozio</span>
        </div>
        <div id = "accountPart">
            <img src = "images/home.png" alt = "home" id = "homePict" title="home"/>
            <div class = "connected" id = "connectedHeader">
                <img src = "images/sendMessage.png" id = "sendMessage" alt = "message" title = "publier un message"/>
                <p>Hello, <span id = "userName"><?php echo $user; ?> </span> </p>
                <img id = "profilePicHead" src = "services/getAvatar.php?userId=<?php echo $user; ?>"/>
                <img src="images/logout.png" alt="logout" id = "logout" title = "logout"/>
            </div>

            <div class = "deConnected" id = "deConnectedHeader">
                <span>Me connecter</span>
            </div>

        </div>
    </header>
    <div id = "contentPart">
        <span id = "loading">Loading ... </span>
        <div id = "leftSection">
            <div class = "messagePage" id = "leftSectionContent">
                <span>Auteur : </span>
                <input type = "text" id = "authorSubName" />
                <div id = "authorOptions"></div>
                <span class = "connected">Par abonnements : </span>
                <input class = "connected" type = "checkbox" id = "checkByFollowed" checked />
            </div>
            <a href = "credit.php" id = "credit">Credit</a>
            <p id = "webCreator">Par Mohamad Ammar Said</p>
        </div>

        <div id = "middleSection">
            <div id = "profileSection">

                <div class = "deConnected" id = "loginForm">
                    <form action = "" method="post" id = "form_login">
                        <label for = "login">Identifiant : </label>
                        <input type = "text" name="login" maxlength="25" required />
                        <label for = "password">Mot de passe : </label>
                        <input type = "password" name = "password" required />
                        <button type="submit" name="valid" value="envoyer">Envoyer</button>
                    </form>
                    <div>Vous avez pas un compte ? <span id = "inscrir">M'inscrire</span> </div>
                    <div id = "loginError"></div>
                </div>

                <div class = "deConnected" id = "creatUserForm">
                    <form action = "" method = "post" id = "form_creat">
                        <label for = "userId">Identifiant : </label>
                        <input type = "text" name="userId" maxlength="25" required/>
                        <label for = "password">Mot de passe : </label>
                        <input type = "password" name = "password" required />
                        <label for = "pseudo">Pseudo : </label>
                        <input type="text" name="pseudo" maxlength="25" required/>
                        <button type="submit" name="valid" value="envoyer">Envoyer</button>
                    </form>

                    <div id = "creatUserMsg"></div>
                </div>

                <div class = "connected" id = "updateProfileForm">
                    <div id = "changeImage">
                        <img src = "images/default_profile.png" alt="profile" id = "profilePicUser"/>
                        <form name="upload_image" action="" method = "post" enctype="multipart/form-data" id = "form_update_avatar">
                            
                            <input type="file" name="image" required="required" id = "inputFile"/>
                            <label for="inputFile">Changer l'Avatar</label>
                            <button type="submit" name="valid" value="envoyer">Envoyer</button>
                            
                        </form>
                    </div>
                    <form action = "" method = "post" id = "form_update_profile">
                        <label for = "password">Mot de passe : </label>
                        <input type = "password" name = "password" />
                        <label for = "pseudo">Pseudo : </label>
                        <input type="text" name="pseudo" maxlength="25" />
                        <label for = "description">Description</label>
                        <textarea type = "text" name = "description" maxlength = "1024"></textarea>
                        <button type="submit" name="valid" value="envoyer">Modifier</button>
                    </form>
                    <div id = "currentUserSubscriptions">
                        <span id = "showFollowedPeople">Votre Followings</span>
                        <div id = "listFollowedPeople"></div>
                        <span id = "showFollowerPeople">Votre Followers</span>
                        <div id = "listFollowerPeople"></div>
                    </div>
                    <div id = "updateProfileMsg"></div>
                </div>

                <div id = "showProfile">
                    <div id = "profileHead">
                        <img src = "images/default_profile.png" alt="profile" id = "profilePicOther" />
                        <div id = "rightHeadSection">
                            <div id = "profileNames">
                                <span id = "showPseudo"></span>
                                <span id = "showUserId"></span>
                            </div>
                            <div id = "followRelation" class = "connected">
                                <p id = "showIsFollower"></p>
                                <span id = "showFollowed" class = "FollowStatus"></span>
                            </div>
                        </div>
                    </div>

                    <span id = "showDescription"></span>

                    <span id = "showProfileMsg"></span>
                    
                </div>

                <div id = "publishMessage" class = "connected">
                    <p>Cette message va être publiée en public</p>
                    <form action = "" method = "post" id = "form_publish_message">
                        <label for = "message">Votre message</label>
                        <textarea type = "text" name = "source" maxlength = "280"></textarea>
                        <button type="submit" name="valid" value="envoyer">Publier</button>    
                    </form>
                    <div id = "publishMessageMsg"></div>
                </div>

            </div>
            

            <div id = "messagesSection" class = "messagePage">
                <div id = "showMessagesMsg"></div>
                <div id = "listMessages"></div>

            </div>
        </div>

        <div id = "rightSection">
        </div>
    </div>



</body>



</html>
