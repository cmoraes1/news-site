<?php
require 'database.php';
session_start();

if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
}

//checks if comment is valid
if(isset($_POST['submitcomment'])){
        $error=array();
        $valid_entry=true;

        if(empty($_POST['comments'])) {
                $error[] = '<li>You did not enter a valid comment</li>';
                $valid_entry=false;
        }
        //put dealy towards article header
        if(!$valid_entry){
        	echo "not valid entry";
 	        echo"<ul>";
        	foreach($error as $error){
        		echo $error;
	        }
        	echo "</ul>";
		header("refresh:1 url=read_article.php?id=". $article_id);
	}

        else {
        	$comment=$mysqli->real_escape_string($_POST['comments']);
 		$command="insert into comments (article_id, owner_id, content) values (" . $_POST['article_id'] . "," . $_SESSION['user_id'] .",'" . $comment . "')";	 
	
		$stmt = $mysqli->prepare($command);
	        if(!$stmt) {
                	printf("Query Prep Failed: :(, $mysqli->error");
                	exit;
        }

	$owner_id=$_SESSION['user_id'];
	$article_id=$_POST['article_id'];
	$comment=$_POST['comments'];

	$stmt->execute();
	$stmt->close();
	$header_location = "read_article.php?id=" . $article_id;
	header("Location: $header_location");

	$command="insert into notifications (article_id, commenter_id) values (" . $article_id . "," . $_SESSION['user_id'] . ")";
	$stmt = $mysqli->prepare($command);
	if(!$stmt) {
        	printf("Query Prep Failed: :(, $mysqli->error");
        	exit;
	}
	$stmt->execute();
	$stmt->close();
}

}
?>
