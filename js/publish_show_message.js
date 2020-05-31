window.addEventListener("load", mainMessage);

// global variable

var author_filter = ""; // the author filter selected

/*
=====================================================================
    -------------------------------------------------
    Publish message part 
    -------------------------------------------------
=====================================================================
*/

// show the form to publish a message
function showPublishMessagePart(){
    removeSearchArea();
    removeWritenFields();
    removeFollowList();
    removeErrorSuccesFields();
    
    document.getElementById("profileSection").style.display = "block";
    document.getElementById("messagesSection").style.display = "none";
    hideAllProfileSection();
    document.getElementById("publishMessage").style.display = "block";
}

// send the message to the service
function publishMessage(ev){
    showLoading(); // show loading box
    ev.preventDefault();
    let url = 'services/postMessage.php';
    var args = new FormData(this);
    fetchFromJson(url, {method:'post', body:args, credentials: "same-origin"})
    .then(processAnswer)
    .then(successPublishMessage, errorPublishMessage);
}

// write that message has been successfully sent
function successPublishMessage(answer){
    document.getElementById("publishMessageMsg").innerHTML = "Votre message était bien publiée";
    document.getElementById("publishMessageMsg").className = "success";
}

// show the error in case message has not been published successfully
function errorPublishMessage(error){
    document.getElementById("publishMessageMsg").innerHTML = error.message;
    document.getElementById("publishMessageMsg").className = "errors";
}


/*
=====================================================================
 --------------------------------------------------
 Show the message part ( In main page )
 --------------------------------------------------
=====================================================================
*/

/**
 * get the messages of the specified author, if author is empty 
 * then the filter is disabled.
 * at each call of the function we will add the number 
 */
function getLastMessages(author){
    author_filter = author; // save the name of author in author_filter
    number_msg += add_number_msg; // increment the number of message to show
    showLoading(); // show loading box
    let url = `services/findMessages.php?author=${author}&count=${number_msg}` ; // filter author
    fetchFromJson(url)
    .then(processAnswer)
    .then(showMessageList, errorGetMessage);
}

/**
 * get the messages of the user that are followed by the current user.
 * 
 */
function getLastMessagesFollowed(){
    number_msg += add_number_msg; // increment the number of message to show
    showLoading(); // show loading box
    let url = `services/findFollowedMessages.php?count=${number_msg}` ; // filter author
    fetchFromJson(url)
    .then(processAnswer)
    .then(showMessageList, errorGetMessage);
}



// in case of error show the error 
function errorGetMessage(error){
    removeLoading();
    document.getElementById("showMessagesMsg").className = "errors";
    document.getElementById("showMessagesMsg").innerHTML = "Errors : " + error.message;
}

// show the list of message in the screen from newest to oldest
function showMessageList(answer){
    removeLoading();
    removeErrorSuccesFields();
    document.getElementById("showMessagesMsg").class = "";
    document.getElementById("showMessagesMsg").innerHTML = "";
    document.getElementById("listMessages").innerHTML = "";

    if(answer.length == 0){
        document.getElementById("listMessages").innerHTML = "Pas des messages publies :/";
        return;
    }

    for(var x = answer.length - 1; x >= 0; x--){
        msg = answer[x];
        document.getElementById("listMessages").innerHTML += 
        `<div class = "message">
            <div class = "rezozioUser">
                <img class = "userImage" data-user="${msg.author}" />
                <div class = "pseudoName" data-user = "${msg.author}">
                    <span class = "pseudoFollow">${msg.pseudo}</span>
                    <span class = "userIdFollow">${msg.author}</span>
                </div>
            </div>
            <span class = "publishInfo">${msg.author} a publié ce message à ${msg.datetime.substring(0,19)}</span>
            <span class = "messageContent">${msg.content}</span>
        </div>
        `;
    }

    document.getElementById("listMessages").innerHTML += "<span id = 'showMore'>Afficher plus ...</span>";
    

    bindNameAndImagesToProfile(); // bind the name and pic to profile
    bindShowMore();
}

// display the messages based on connected or not
function displayMessages(){
    // not connected show last messages without filter
    if(document.body.dataset.user == null){
        getLastMessages(author_filter);
    }
    else{
        if(author_filter == ""){
            getLastMessagesFollowed();
        }
        else{
            getLastMessages(author_filter);
        }
    }
}

// bind the text showMore to show more messages
function bindShowMore(){
    if(document.body.dataset.user == null){
        document.getElementById("showMore").addEventListener("click", function(){getLastMessages(author_filter);}, false);
    }
    else{
        if(author_filter != ""){
            document.getElementById("showMore").addEventListener("click", function(){getLastMessages(author_filter);}, false);
        }
        else{
            document.getElementById("showMore").addEventListener("click", getLastMessagesFollowed);    
        }
    }
}



function mainMessage(){
    displayMessages();
    document.getElementById("sendMessage").addEventListener("click", showPublishMessagePart);
    document.forms.form_publish_message.addEventListener("submit", publishMessage);
}