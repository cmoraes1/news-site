<!DOCTYPE html>
<html>
<head>
	<title>Favorites</title>
	<link rel="stylesheet" type="text/css" href="favorites.css">
</head>
<body>

<div id="banner"</div>
<h1>News Website</h1>
<br>

<div id="logoutuser">
	<a href="logout.php">Log Out</a>
</div>

<p id="fav_header">Favorites:</p>

<?php
session_start();
require 'database.php';

//back button to return to homepage
echo '<div id="back">';
	$location = "homepage.php";
	echo "<a href='".$location."'>Back to Homepage</a>";
echo '</div>';

$command = 'select articles.title, articles.article_id, users.first_name, users.last_name, favorites.favorite_id from favorites join articles on (articles.article_id = favorites.article_id) JOIN users on (users.user_id=favorites.user_id) WHERE favorites.user_id=' . $_SESSION['user_id'];
$stmt = $mysqli->prepare($command);
	if(!$stmt) {
		printf("Query Prep Failed: :(, $mysqli->error");
		exit;
	}

$stmt->execute();
$stmt->bind_result($title,$article_id, $first_name, $last_name, $favorite_id);
while($stmt->fetch()){
	echo '<div id="fav_info">';
		printf("<a href = ./read_article?id=\"%s\">%s</a> by %s %s", $article_id, htmlentities($title), htmlentities($first_name), htmlentities($last_name));
	echo '</div>';
	printf("<form action='delete_favorite.php' method=POST>");
        printf("<input type= 'hidden' name= 'token' value= %s>", $_SESSION['token']);
        printf("<input type = 'hidden' name= 'favorite_id' value=%s>", $favorite_id);
	printf("<input type = 'hidden' name= 'article_id' value=%s>", $article_id);
	echo '<div id="fav_button">';
        	printf("<input type= submit name=delete_favorite value='Unfavorite'>");
		echo '<br>';
		echo '<br>';
	echo '</div>';
        printf("</form>");
	printf("<br>");
}
?>
</body>
</html>
