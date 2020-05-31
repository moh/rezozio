window.addEventListener("load", mainShowUpdate);
var showedFollower = false;
var showedFollowed = false;
var showedUser = null;
/*
----------------------------------------------------------------------------
Show the data of the connected user, and modify them
----------------------------------------------------------------------------
*/

// show the profile of the connected user
function showConnectedUser(){
    removeWritenFields();
    removeSearchArea(); // remove the search area
    removeErrorSuccesFields();
    document.getElementById("profileSection").style.display = "block";
    document.getElementById("messagesSection").style.display = "none";
    hideAllProfileSection();
    document.getElementById("updateProfileForm").style.display = "block";
    getConnectedUserInfo();
}

// get the profile of connected user
function getConnectedUserInfo(){
    showLoading(); // show loading box
    var userId = document.body.dataset.user;
    let url = 'services/getProfile.php?userId='+userId;
    fetchFromJson(url)
    .then(processAnswer)
    .then(successShowConnectedUserInfo, errorShowConnectedUser);
}

function processAnswer(answer){
    removeLoading();
    if (answer.status == "ok")
      return answer.result;
    else
      throw new Error(answer.message);
}
  
// show the information of the connected user
function successShowConnectedUserInfo(answer){
    // clean the error field
    document.getElementById("updateProfileMsg").innerHTML = "";
    document.getElementById("updateProfileMsg").className = "";
    // show the right pic
    document.getElementById("profilePicUser").src = 
    "services/getAvatar.php?userId=" + document.body.dataset.user + "&size=large"
    + "&random="+new Date().getTime();


    var pseudoField = document.querySelector("#updateProfileForm input[name='pseudo']");
    var descriptionField = document.querySelector("#updateProfileForm textarea[name='description']");

    pseudoField.value = answer.pseudo;
    descriptionField.value = answer.description;

}

// show the error produced
function errorShowConnectedUser(error){
    removeLoading();
    // in case we are in show profile section, write the error in the section
    if(document.getElementById("showProfile").style.display != "none"){
        document.getElementById("showProfileMsg").innerHTML = error.message;
        document.getElementById("showProfileMsg").className = "errors";
    }
    else{
        document.getElementById("updateProfileMsg").innerHTML = error.message;
        document.getElementById("updateProfileMsg").className = "errors";
    }
}

/*
----------------------------------------------------------------------------------
update user profile Section
----------------------------------------------------------------------------------
*/

// send the modified profile data
function updateUserProfile(ev){
    showLoading(); // show loading box
    ev.preventDefault();
    let url = 'services/setProfile.php';
    var args = new FormData(this);
    fetchFromJson(url, {method:'post', body:args, credentials: "same-origin"})
    .then(processAnswer)
    .then(successUpdateUserProfile, errorShowConnectedUser);
}

// show a message that profile has been updated successfully
function successUpdateUserProfile(answer){
    document.getElementById("updateProfileMsg").innerHTML = "Votre profil a été mis a jour !";
    document.getElementById("updateProfileMsg").className = "success";
}

// change the avatar
function changeAvatar(ev){
    showLoading();
    ev.preventDefault(); // empêche l'envoi 'normal' du formulaire
    let formData = new FormData(this); // objet contenant les données du formulaire
    let url = "services/uploadAvatar.php";
    fetchFromJson(url,
        {method : 'post', body : formData, 'credentials': 'same-origin'})
        .then(processAnswer)
        .then(showAvatar, errorShowConnectedUser);
}

// update the avatar after showing

function showAvatar(){
    document.getElementById("profilePicUser").src = 
    "services/getAvatar.php?userId=" + document.body.dataset.user + "&size=large"
    + "&random="+new Date().getTime(); // random to reload the photo properly

    document.getElementById("profilePicHead").src = "services/getAvatar.php?userId=" 
    + document.body.dataset.user + "&random="+new Date().getTime();
}

/*
-------------------------------------------------------------------------------
Show Follower and followed people of and by the current user
-------------------------------------------------------------------------------
*/

function actionFollowedPeople(){
    if(showedFollowed){
        showedFollowed = false;
        document.getElementById("listFollowedPeople").innerHTML = ""; // clear the area of display
        document.getElementById("listFollowedPeople").style.display = "none";
        document.getElementById("showFollowedPeople").style.backgroundColor = "#dfdddd";
        document.getElementById("showFollowedPeople").style.color = "black";
        
    }
    else{
        showedFollowed = true;
        document.getElementById("showFollowedPeople").style.backgroundColor = "gray";
        document.getElementById("showFollowedPeople").style.color = "white";
        
        getFollowedPeople();
    }
}

// get the user that are followed by the current user
function getFollowedPeople(){
    showLoading(); // show loading box
    let url = 'services/getSubscriptions.php';
    fetchFromJson(url)
    .then(processAnswer)
    .then(showFollowedPeople, errorShowConnectedUser);
}

// show a list of user that are followed by the current user
function showFollowedPeople(answer){
    document.getElementById("listFollowedPeople").innerHTML = "";
    document.getElementById("listFollowedPeople").style.display = "block";
    if(answer.length == 0){
        document.getElementById("listFollowedPeople").innerHTML = "Pas des utilisateurs suivie";
    }
    else{
        for(var x = 0; x < answer.length; x++){
            document.getElementById("listFollowedPeople").innerHTML += 
            "<div class = 'followedUser'><img src = 'images/default_profile.png' class = 'userImage' data-user = '"
            + answer[x].userId +"'/><div class = 'pseudoName' data-user = '" + answer[x].userId + "'><span class = 'pseudoFollow'>"
            + answer[x].pseudo +"</span><span class = 'userIdFollow'>"+ answer[x].userId
            +"</span></div><span class = 'FollowStatus unfollow' data-target = '"+ answer[x].userId
            +"'>Unfollow</span>"+"</div>";
        }
    }
    bindNameAndImagesToProfile(); // enable click on their name and profile
    bindFollowUnfollowButton();
}

//--------------------
// show followers part
// -------------------

function actionFollowerPeople(){
    if(showedFollower){
        showedFollower = false;
        document.getElementById("listFollowerPeople").innerHTML = ""; // clear the area of display
        document.getElementById("listFollowerPeople").style.display = "none";
        document.getElementById("showFollowerPeople").style.backgroundColor = "#dfdddd";
        document.getElementById("showFollowerPeople").style.color = "black";
        
    }
    else{
        showedFollower = true;
        document.getElementById("showFollowerPeople").style.backgroundColor = "gray";
        document.getElementById("showFollowerPeople").style.color = "white";
        
        getFollowerPeople();
    }
}

// get the user that are followed by the current user
function getFollowerPeople(){
    showLoading(); // show loading box
    let url = 'services/getFollowers.php';
    fetchFromJson(url)
    .then(processAnswer)
    .then(showFollowerPeople, errorShowConnectedUser);
}

// show a list of user that are followed by the current user
function showFollowerPeople(answer){
    document.getElementById("listFollowerPeople").innerHTML = "";
    document.getElementById("listFollowerPeople").style.display = "block";
    if(answer.length == 0){
        document.getElementById("listFollowerPeople").innerHTML = "Pas des utilisateurs vous suit";
    }
    else{
        for(var x = 0; x < answer.length; x++){
            var spec_class = "follow";

            // if connected user follow this user too
            if(answer[x].mutual){
                spec_class = "unfollow";
            }
            document.getElementById("listFollowerPeople").innerHTML += 
            "<div class = 'followerUser'><img src = 'images/default_profile.png' class = 'userImage' data-user = '" 
            + answer[x].userId + "'/><div class = 'pseudoName' data-user = '" + answer[x].userId + "'><span class = 'pseudoFollow'>"
            + answer[x].pseudo +"</span><span class = 'userIdFollow'>"+ answer[x].userId
            +"</span></div><span class = 'FollowStatus "+spec_class+"' data-target = '"+ answer[x].userId
            +"'>"+spec_class+"</span>"+"</div>";
        }
    }
    bindNameAndImagesToProfile(); // enable click on their name and profile
    bindFollowUnfollowButton();
}


/*
-----------------------------------------------------------------
Follow and unfollow user 
-----------------------------------------------------------------
*/

function unfollow(){
    showLoading(); // show loading box
    unbindFollowUnfollowButton(); // disable click the button

    var target = this.dataset.target;
    let url = 'services/unfollow.php?target='+target;
    fetchFromJson(url)
    .then(processAnswer)
    .then(successUnfollowUser, errorShowConnectedUser);
}

function successUnfollowUser(answer){
    removeErrorSuccesFields();
    // if the unfollow resulted from show user profile
    if(document.getElementById("showProfile").style.display != "none"){
        getOtherProfile(showedUser);
        return;
    }
    
    // if the user is seeing the followed people then update it
    if(showedFollowed){
        getFollowedPeople(); // update the list of followed people
    }
    if(showedFollower){
        getFollowerPeople();
    }
}

function follow(){
    showLoading(); // show loading box
    unbindFollowUnfollowButton(); // disable click the button

    var target = this.dataset.target;
    let url = 'services/follow.php?target=' + target;
    fetchFromJson(url)
    .then(processAnswer)
    .then(successFollowUser, errorShowConnectedUser);
}

function successFollowUser(answer){
    removeErrorSuccesFields();
    // if the follow resulted from show user profile
    if(document.getElementById("showProfile").style.display != "none"){
        getOtherProfile(showedUser);
        return;
    }

    // if the user is seeing the followed people then update it
    if(showedFollowed){
        getFollowedPeople(); // update the list of followed people
    }
    if(showedFollower){
        getFollowerPeople();
    }
}

// add event listener to the follow and unfollow buttons
function bindFollowUnfollowButton(){
    var unfollowButtons = document.getElementsByClassName("unfollow");
    var followButtons = document.getElementsByClassName("follow");
    for(var x = 0; x < unfollowButtons.length; x++){
        unfollowButtons[x].addEventListener("click", unfollow);
    }
    for(var x = 0; x < followButtons.length; x++){
        followButtons[x].addEventListener("click", follow);

    }
}


/* this function will be used to disable instant multiple click on the same button
   to avoid clicking before updating the status of the user
   Remove event listener from the follow and unfollow buttons
 */
function unbindFollowUnfollowButton(){
    var unfollowButtons = document.getElementsByClassName("unfollow");
    var followButtons = document.getElementsByClassName("follow");
    for(var x = 0; x < unfollowButtons.length; x++){
        unfollowButtons[x].removeEventListener("click", unfollow);
    }
    for(var x = 0; x < followButtons.length; x++){
        followButtons[x].removeEventListener("click", follow);

    }
}


/*
----------------------------------------------------------------------------------
Show the profile of other users
----------------------------------------------------------------------------------
*/

function showOtherUser(){
    removeFollowList();
    removeSearchArea();
    var userId = this.dataset.user; // get the user of the clicked name or image
    showedUser = userId;
    removeWritenFields();
    removeErrorSuccesFields();
    document.getElementById("profileSection").style.display = "block";
    document.getElementById("messagesSection").style.display = "none";
    hideAllProfileSection();
    document.getElementById("showProfile").style.display = "block";

    getOtherProfile(userId);
}


// get profile of other user
function getOtherProfile(userId){
    showLoading(); // show loading box
    let url = 'services/getProfile.php?userId='+userId;
    fetchFromJson(url)
    .then(processAnswer)
    .then(successGetOtherProfile, errorGetOtherProfile);
}

// show the other profile
function successGetOtherProfile(answer){
    removeErrorSuccesFields();
    unbindFollowUnfollowButton();

    // display the user information
    document.getElementById("showPseudo").innerHTML = answer.pseudo;
    document.getElementById("showUserId").innerHTML = answer.userId;
    document.getElementById("showDescription").innerHTML = answer.description;
    document.getElementById("profilePicOther").src = "services/getAvatar.php?userId=" + answer.userId + "&size=large"
    + "&random="+new Date().getTime();
    
    if(answer.followed != null){
        // set attribute with the user displayed
        document.getElementById("showFollowed").setAttribute("data-target", answer.userId);

        // the user is followed by connected user
        if(answer.followed){
            document.getElementById("showFollowed").innerHTML = "Unfollow";
            document.getElementById("showFollowed").className = "FollowStatus unfollow";
        }
        else{
            document.getElementById("showFollowed").innerHTML = "Follow";
            document.getElementById("showFollowed").className = "FollowStatus follow";
        }
        // the user follow the current user.
        if(answer.isFollower){
            document.getElementById("showIsFollower").innerHTML = answer.userId + " vous suit :)";
        }
        else{
            document.getElementById("showIsFollower").innerHTML = answer.userId + " ne vous suit pas :(";    
        }
    }
    bindFollowUnfollowButton();
}

// show the error while getting the other profile
function errorGetOtherProfile(error){
    removeLoading();
    document.getElementById("showProfileMsg").innerHTML = error.message;
    document.getElementById("showProfileMsg").className = "errors";    
}


// make the name and the image clickable and show the user profile
// update the image of each user
function bindNameAndImagesToProfile(){
    var userPics = document.getElementsByClassName("userImage");
    var userPseudoName = document.getElementsByClassName("pseudoName");

    for(var x = 0; x < userPics.length; x++){
        // update the image of the user
        userPics[x].src = "services/getAvatar.php?userId=" + userPics[x].dataset.user + "&size=small"
        + "&random="+new Date().getTime();
        // add event listener for click
        userPics[x].addEventListener("click", showOtherUser);
    }

    for(var x = 0; x < userPseudoName.length; x++){
        userPseudoName[x].addEventListener("click", showOtherUser);
    }
    unbindShowProfileUserShowOptions(); // unbind the click on the author in choise list
}

function mainShowUpdate(){
    document.getElementById("userName").addEventListener("click", showConnectedUser);
    document.forms.form_update_profile.addEventListener("submit", updateUserProfile);
    document.forms.form_update_avatar.addEventListener("submit", changeAvatar);
    document.getElementById("showFollowedPeople").addEventListener("click", actionFollowedPeople);
    document.getElementById("showFollowerPeople").addEventListener("click", actionFollowerPeople);
    
}