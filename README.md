# rezozio
Rezozio is a university project, it is a website where you can create an account and publish messages.
Users in Rezozio can follow each others.

This website uses HTML, Css, Javascript, Php and sql.

the in the sql databse "rezozio", we have three tables : 
1- messages: it contains 4 columns to store the messages published by the user.
	1- id : id of messages
	2- author: the login of the author of message
	3- content: content of the message.
	4- datetime: the date of publishing this message

2- subscriptions: contains 2 columns to store the subscriptions between users
	1- follower: the login of the user that is following
	2- target: the login of the user that is followed

3- users: contains 7 columns to store the data of the user
	1- login: the user id 
	2- pseudo: the pseudo name of the user
	3- description: the description of the user
	4- password: the password of the user ( encrypted )
	5- avatar_type: the type of the profile pic of the user
	6- avatar_small: the profile pic of the user of size 48x48
	7- avatar_large: the profile pic of the user of size 256x256
	
