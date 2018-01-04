<?php
session_start();
require 'database.php';

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

//insert information into table
$command='insert into favorites(article_id, user_id) values(?,?)';
$stmt=$mysqli->prepare($command);

if(!$stmt) {
		printf("Query Prep Failed: :(, $mysqli->error");
		exit;
}

$stmt->bind_param('ii', $_POST['article_id'], $_SESSION['user_id']);
$stmt->execute();
echo $_POST['article_id'];
$header_location = './read_article.php?id='. $_POST['article_id'];
header("Location: $header_location");
?>
