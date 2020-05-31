window.addEventListener("load", mainLog);

/**
 * show only the login form from the profile section.
 * 
 */
function showLogIn(){
    document.getElementById("profileSection").style.display = "block";
    document.getElementById("messagesSection").style.display = "none";
    hideAllProfileSection();
    removeWritenFields();
    removeSearchArea();
    document.getElementById("loginForm").style.display = "block";
}


/**
 * Show creat account part
 * 
 */
function showCreatAccount(){
    document.getElementById("profileSection").style.display = "block";
    document.getElementById("messagesSection").style.display = "none";
    hideAllProfileSection();
    removeWritenFields();
    removeSearchArea();
    document.getElementById("creatUserForm").style.display = "block";
}


/**
 * -----------------------------------------------------------------------------
 * Creat user part 
 * -----------------------------------------------------------------------------
 * 
 */

// send the user data to creat an account
function creatUser(ev){
    showLoading(); // show loading box
    ev.preventDefault();
    let url = 'services/createUser.php';
    var args = new FormData(this);
    fetchFromJson(url, {method:'post', body:args, credentials: "same-origin"})
    .then(processAnswer)
    .then(successCreatUser, errorCreateUser);
}

function successCreatUser(answer){
    document.getElementById("creatUserMsg").className = "success";
    document.getElementById("creatUserMsg").innerHTML = "votre compte est bien créé !";

    var inputsText = document.querySelectorAll("#creatUserForm input");
    for(var x = 0; x < inputsText.length; x++){
        inputsText[x].value = "";
    }
}

function errorCreateUser(error){
    document.getElementById("creatUserMsg").className = "errors";
    document.getElementById("creatUserMsg").innerHTML = error.message;    
}

/**
 * ------------------------------------------------------------------------------
 * send login data, and login the user
 * ------------------------------------------------------------------------------
 * 
 */

function sendLogin(ev){ // gestionnaire de l'évènement submit sur le formulaire de login
  showLoading(); // show loading box
  ev.preventDefault();
  let url = 'services/login.php';
  var args = new FormData(this);
  fetchFromJson(url, {method:'post', body:args, credentials: "same-origin"})
  .then(processAnswer)
  .then(connecteUser, errorLogin);
}

function processAnswer(answer){
  removeLoading(); // remove loading box
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}

// update the page after successful login
function connecteUser(user){
    document.getElementById("loginError").innerHTML = "";
    document.getElementById("loginError").className = "";
    document.getElementById("profilePicHead").src = "services/getAvatar.php?userId="+user; // update the pic in the header
    
    removeSearchArea(true); // remove totally the search area

    // delete the input field
    var inputTexts = document.querySelectorAll("#form_login input");
    for(var x = 0; x < inputTexts.length; x++){
        inputTexts[x].value = "";
    }

    document.body.setAttribute("data-user", user); // add user name in body attribute.
    toHome(); // go to the home page, function in init_page.js
    document.getElementById("userName").innerHTML = user;
}

// show the login error.
function errorLogin(error){
    document.getElementById("loginError").innerHTML = error.message;
    document.getElementById("loginError").className = "errors";
}

/**
 * ---------------------------------------------------------------------------
 * logout section
 * ---------------------------------------------------------------------------
 */

function sendLogout(){ // gestionnaire de l'évènement click sur le bouton logout
  showLoading(); // show loading box
  let url = 'services/logout.php';
  fetchFromJson(url, {method:'post', credentials: "same-origin"})
  .then(processAnswer)
  .then(deconnectUser, deconnectUser); // init the page after deconnecting
  // in case of error ( as user not connecting ) we go to the home page 
}

// deconnect user after successful logout
function deconnectUser(){
    document.body.removeAttribute("data-user"); // remove the data-user attribute 
    toHome();
}


function mainLog(){
    document.getElementById("deConnectedHeader").addEventListener("click", showLogIn);
    document.getElementById("inscrir").addEventListener("click", showCreatAccount);
    document.getElementById("logout").addEventListener("click", sendLogout);
    
    document.forms.form_login.addEventListener('submit',sendLogin);
    document.forms.form_creat.addEventListener('submit',creatUser);
    
}