<?php
session_start();
include 'database.php';

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$comment_id=$_POST['comment_id'];

$command = 'select article_id from comments where comment_id=' . $comment_id;
$stmt2 = $mysqli->prepare($command);

if(!$stmt2) {
	printf("Query Failed: :(", $mysqli->error);
	exit;
}

$stmt2->execute();
$stmt2->bind_result($article_id);
$stmt2->fetch();
$stmt2->close();

//deletes comment
$stmt = $mysqli->prepare("delete from comments where comment_id=?");
if(!$stmt) {
        printf("Query Prep Failed: :(", $mysqli->error);
        exit;
}

$stmt->bind_param('s', $comment_id);
$stmt->execute();
$stmt->close();

$header_location = "read_article.php?id=" . $article_id;
header("Location: $header_location");

?>
