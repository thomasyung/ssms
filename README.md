ssms
====

(S)ecure (S)ingle-use (M)essage (S)ystem 

"This message will self-destruct in 10 seconds..." The problem with sending messages via email is that messages are stored on email servers that have data retention policies in place. Even if messages are deleted by the sender and recipient, the message still persists on the server. 

If you need to send private electronic messages to others, you typically have to use a third party website like [OneShares](https://oneshar.es/) but the issue is trust. Do you trust that website? If you have control of your own website (one that only you have access to), you can install this script there, and get the same functionality as OneShares.

##Requirements:##

* MySQL database
* Web server capable of running PHP scripts
* A modern web browser with JavaScript enabled

##Installation:##

* Run the "create_database_tables.sql" on your MySQL server (i.e. PHPMyAdmin) to create the database and table.
* Change the variable declarations in "ssms.php" to point to the web server's hostname. Also, modify the database user and password that will have access to the database table you just created.
* Place the PHP script "ssms.php" on the web server.

##Usage:##

1. If you need to generate a private message, go to http://<servername>/ssms.php 
2. Fill out the message. **Hint:** If you don't want any identifying information, don't put your signature in the message. For example, simply write: 
> Hey Robert,
> 
> Let's meet by the entrance of the restaurant at noon. I have something I need to show you.
3. When you are ready to save the message, click the "Save message and generate link" button and a unique URL will be generated for you.
4. Copy and paste that URL to the messaging tool of your choice. Send the message to the recipient.
5. The recipient will open the URL in their browser and the message will be retrieved and decoded for them to view. 

**Note:** The message can only be read once. If the message is intercepted in transit, the recipient will see information about when it was read, and the IP address of the computer/device that was used to view it. Also, keep in mind that this method is not completely foolproof. If the recipient does a print screen or screen capture, then the contents of the message can be saved offline.
