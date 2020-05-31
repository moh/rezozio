window.addEventListener("load", main_init);

var number_msg = 0; // number of shown message
var add_number_msg = 10; // number of message to add each time.
var messagesList = [];

function init_content(){
    // hide the profile section 
    document.getElementById("profileSection").style.display = "none";

    document.getElementById("messagesSection").style.display = "";
    
    // show all section related to message page
    var messages_sect = document.getElementsByClassName("messagePage");
    for(var x = 0; x < messages_sect.length; x++){
        messages_sect[x].style.display = "";
    }

}

function init_connected(){
    var connected = document.getElementsByClassName("connected");
    var deConnected = document.getElementsByClassName("deConnected");
    if(document.body.dataset.user != null){
        for(var x = 0; x < connected.length; x++){
            connected[x].style.display = "";
        }
        for(var x = 0; x < deConnected.length; x++){
            deConnected[x].style.display = "none";
        }
    }
}

function init_deconnected(){
    var connected = document.getElementsByClassName("connected");
    var deConnected = document.getElementsByClassName("deConnected");
    if(document.body.dataset.user == null){
        for(var x = 0; x < connected.length; x++){
            connected[x].style.display = "none";
        }
        for(var x = 0; x < deConnected.length; x++){
            deConnected[x].style.display = "";
        }
    }
}

// go to home page
function toHome(){
    removeWritenFields(); // remove texts in input field
    removeErrorSuccesFields(); // remove the error or success msgs
    removeFollowList(); // remove the following and follower list
    removeLoading();
    init_connected();
    init_deconnected();
    init_content();
    displayMessages();

    document.getElementById("leftSectionContent").style.display = "block";
}


/**
 * hide all the sections inside the profile section.
 * 
 */
function hideAllProfileSection(){
    var profileSections = document.getElementById("profileSection");
    var childrens = profileSections.children;
    for(var x = 0; x < childrens.length; x++){
        childrens[x].style.display = "none";
    }
}

/**
 * clear what was writen inside the input boxes
 */
function removeWritenFields(){
    var inputs = document.querySelectorAll("input");
    var textareas = document.querySelectorAll("textarea");
    for(var x = 0; x < inputs.length; x++){
        // don't remove the text in search field
        if(inputs[x].id != "authorSubName"){
            inputs[x].value = "";
        }
    }
    for(var x = 0; x < textareas.length; x++){
        textareas[x].value = "";
    }
}

/**
 * remove the field that displays the error and success messages 
 */
function removeErrorSuccesFields(){
    var errorField = document.getElementsByClassName("errors");
    var successField = document.getElementsByClassName("success");

    for(var x = 0; x < errorField.length; x++){
        errorField[x].innerHTML = "";
        errorField[x].className = "";
    }

    for(var x = 0; x < successField.length; x++){
        successField[x].innerHTML = "";
        successField[x].className = "";
    }
}

// remove the list of follwing and followers
function removeFollowList(){
    document.getElementById("listFollowerPeople").innerHTML = ""; // clear the area of display
    document.getElementById("listFollowerPeople").style.display = "none";
    document.getElementById("listFollowedPeople").innerHTML = ""; // clear the area of display
    document.getElementById("listFollowedPeople").style.display = "none";

    document.getElementById("showFollowerPeople").style.backgroundColor = "#dfdddd";
    document.getElementById("showFollowerPeople").style.color = "black";
    document.getElementById("showFollowedPeople").style.backgroundColor = "#dfdddd";
    document.getElementById("showFollowedPeople").style.color = "black";

    showedFollower = false;
    showedFollowed = false;
}

function showLoading(){
    document.getElementById("loading").style.display = "block";
}

function removeLoading(){
    document.getElementById("loading").style.display = "none";
}

/*
=================================================
Part for left section
=================================================
*/
function searchAuthors(name){
    name = name.split(' ').join('+');
    let url = "services/findUsers.php?searchedString=" + name;
    fetchFromJson(url)
    .then(processAnswer)
    .then(showAuthorsList);
}

function showAuthorsList(answer){
    document.getElementById("authorOptions").innerHTML = "";
    document.getElementById("authorOptions").style.height = "150px";

    if(answer.length == 0){
        document.getElementById("authorOptions").innerHTML = "Pas des auteurs !";
        return;
    }
    for(var x = 0; x < answer.length; x++){
        user = answer[x];
        document.getElementById("authorOptions").innerHTML += 
        `<div class = "rezozioUser" data-user = "${user.userId}">
            <img class = "userImage" src = "services/getAvatar.php?userId=${user.userId + "&size=small"
            + "&random="+new Date().getTime()}" data-user="${user.userId}" />
            <div class = "pseudoName">
                <span class = "pseudoFollow">${user.pseudo}</span>
                <span class = "userIdFollow">${user.userId}</span>
            </div>
        </div>
        `;
    }

    bindAuthorsSearch();
}


function showOptions(){
    name = this.value;
    document.getElementById("checkByFollowed").checked = false;
    if(name.length >= 3){
        searchAuthors(name);
    }
    else if (name.length == 0) {
        number_msg -= add_number_msg; // keep the number of search as it was before
        getLastMessages("");
    }
    else{
        document.getElementById("authorOptions").innerHTML = "";
        document.getElementById("authorOptions").style.height = "0px";
    }
}

/*
    Bind the click on the author to do a search about 
    the messages published by those users.
*/
function bindAuthorsSearch(){
    var users = document.querySelectorAll("#authorOptions .rezozioUser");
    for(var x = 0; x < users.length; x++){
        users[x].addEventListener("click", function(){
            document.getElementById("authorSubName").value = this.dataset.user;
            document.getElementById("checkByFollowed").checked = false;
            getLastMessages(this.dataset.user);
        });
    }
}

/*
    Unbind the click on the name and image of the list of authors choices.

*/
function unbindShowProfileUserShowOptions(){
    var users = document.querySelectorAll("#authorOptions .pseudoName");
    for(var x = 0; x < users.length; x++){
        users[x].removeEventListener("click", showOtherUser);
    }
    users = document.querySelectorAll("#authorOptions .userImage");
    for(var x = 0; x < users.length; x++){
        users[x].removeEventListener("click", showOtherUser);
    }
}

// remove the search area in left section
function removeSearchArea(removeAll = false){
    document.getElementById("leftSectionContent").style.display = "none";
    if(removeAll){
        author_filter = "";
        document.getElementById("authorSubName").value = "";
        document.getElementById("authorOptions").innerHTML = "";
        document.getElementById("checkByFollowed").checked = true;
    }
}


function byFollowedChecked(){
    // if it is checked then remove the people
    if(this.checked){
        document.getElementById("authorSubName").value = "";
        document.getElementById("authorOptions").innerHTML = "";
        getLastMessagesFollowed();
    }
    else{
        getLastMessages(author_filter);
    }
}


function main_init(){
    document.body.style.display = "block";
    document.getElementById("authorSubName").addEventListener("keypress", showOptions);
    document.getElementById("authorSubName").addEventListener("keydown", showOptions);
    document.getElementById("authorSubName").addEventListener("keyup", showOptions);
    document.getElementById("checkByFollowed").addEventListener("click", byFollowedChecked);

    document.getElementById("homePict").addEventListener("click", toHome);
    toHome();
}


