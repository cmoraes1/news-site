<?php
session_start();
include 'database.php';

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$article_id=$_POST['article_id'];

//detlete favorites
$stmt = $mysqli->prepare("delete from favorites where article_id=?");
if(!$stmt) {
        printf("Query Prep Failed: :(", $mysqli->error);
        exit;
}

$stmt->bind_param('s', $article_id);
$stmt->execute();
$stmt->close();

//delete notifications
$stmt = $mysqli->prepare("delete from notifications where article_id=?");
if(!$stmt) {
        printf("Query Prep Failed: :(", $mysqli->error);
        exit;
}

$stmt->bind_param('s', $article_id);
$stmt->execute();
$stmt->close();

//delete comments
$stmt = $mysqli->prepare("delete from comments where article_id=?");
if(!$stmt) {
        printf("Query Prep Failed: :(", $mysqli->error);
        exit;
}

$stmt->bind_param('s', $article_id);
$stmt->execute();
$stmt->close();

//delete from table
$stmt = $mysqli->prepare("delete from articles where article_id=?");
if(!$stmt) {
	printf("Query Prep Failed: :(", $mysqli->error);
	exit;
}

$stmt->bind_param('s', $article_id);
$stmt->execute();
$stmt->close();

header("Location: homepage.php");

?>
