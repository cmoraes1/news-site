<!DOCTYPE html>
<head>
	<title> Home Page </title>
	<meta charset = "utf-8">
	<link rel="stylesheet" type="text/css" href="homepage.css">
</head>

<body>
<?php
require 'database.php';
session_start();

if(isset($_SESSION['user_id']))
{
        //create logout button
        echo '<div id="rect"></div>';
        echo '<a href="create_article.php" id="postarticle">Post an Article</a>';
        echo '<div id="logoutuser">';
        	echo '<a href="logout.php">Log Out</a>';
        echo '</div>'; 
}

echo '<div id="banner"></div>';
	echo '<h1>News Website</h1>';
	echo '<div id="show_articles">';
	echo '<p>Articles</p>';
echo '</div>';

$command = 'select users.first_name, users.last_name, articles.article_id, articles.title from articles join users on (users.user_id=articles.owner_id);';

$stmt = $mysqli->prepare($command);
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->execute();
$stmt->bind_result($first_name, $last_name, $article_id, $title);
while($stmt->fetch()){
	echo '<div class="article_list">';
		printf("<a href = ./read_article?id=%s>%s</a> by %s %s", $article_id, htmlentities($title), htmlentities($first_name), htmlentities($last_name));
		printf("<br>");
	echo '</div>';
}

//if user is logged in
if(isset($_SESSION['user_id']))
{
	//create articles, see favorites, see notifications	
	echo '<div id="favorites">';
		echo '<a href="favorites.php">Favorites</a>';
	echo '</div>';
	echo '<div id="notifications">';
		echo '<a href="notifications.php">Notifications</a>';
	echo '</div>';
}

//if user is not logged in
else {
	echo '<div id="rect2"></div>';

	//login form
	echo '<div id="rect_homepage"></div>';
	echo '<p id="login">Login Here:</p>';
	echo '<div id="attemptlogin">';
		echo '<form action="checkuser.php" method="POST">';
			echo '<label for="email_address">Email: <input type="text" name="email_address"></label><br>';
			echo '<br>';
			echo '<label for="password">Password: <input type="password" name="pswd"></label><br>';
			echo '<div id="user_but">';
				echo '<input type="submit" name="submit" value="Submit">';
			echo '</div>';
		echo '</form>';
	echo '</div>';

	//register user button
	echo '<div id="register">';
		echo '<form action="createuser.php" method="POST">';
			echo '<p id="new_register">Make account:</p>';
			echo '<div id="register_button">';
				echo '<input type="submit" name="register" value="Register Here">';
			echo '</div>';
		echo '</form>';
	echo '</div>';
}
?>
</body>
</html>
