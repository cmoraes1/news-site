<!DOCTYPE html>
<html>
<head>
	<title>Notifications</title>
	<link rel="stylesheet" type="text/css" href="notifications.css">
</head>
<body>

<div id="banner"</div>
<h1>News Website</h1>
<p id="notif_header">Notifications:</p>
<br>

<!-- logout button -->
<div id="logoutuser">
	<a href="logout.php">Log Out</a>
</div>

<!-- button back to homepage -->
<div id="back">
	<a href='homepage.php'>Back to Homepage</a>
</div>

<br>
<?php
session_start();
require 'database.php';
$command = "select users.first_name, users.last_name, articles.title, notifications.article_id, notifications.notification_id from notifications join articles on (articles.article_id= notifications.article_id) join users on (notifications.commenter_id=users.user_id) where articles.owner_id=".$_SESSION['user_id'];
	$stmt=$mysqli->prepare($command);
	if(!$stmt) {
		printf("Query Prep Failed: :(, $mysqli->error");
		exit;
	}
	$stmt->execute();
	$stmt->bind_result($commenter_first_name, $commenter_last_name,$title,$article_id, $notification_id);
	while($stmt->fetch()){
		echo '<div id="notif_info">';
        		printf('User %s %s commented on your article ', htmlentities($commenter_first_name), htmlentities($commenter_last_name));
			printf("<a href = ./read_article?id=\"%s\">%s</a>", $article_id, htmlentities($title));
		echo '</div>';
		printf("<form action='delete_notification.php' method=POST>");
			printf("<input type= 'hidden' name= 'token' value= %s>", $_SESSION['token']);
			printf("<input type = 'hidden' name= 'notification_id' value=%s>", $notification_id);
			echo '<div id="notif_button">';
				printf("<input type= submit name=delete_nofication value='Delete'>");
			echo '</div>';
		printf("</form>");
		printf("<br>");
	}	
?>
</body>
</html>
