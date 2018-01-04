<?php

session_start();
require 'database.php';

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$command='delete from favorites where favorite_id=' . $_POST['favorite_id'];
$stmt=$mysqli->prepare($command);
if(!$stmt) {
		printf("Query Prep Failed: :(, $mysqli->error");
		exit;
}

$stmt->execute();
$stmt->close();
$header_location = './read_article.php?id="'. $_POST['article_id'] . "\"";
header("Location: $header_location");
?>
