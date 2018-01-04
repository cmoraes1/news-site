<!DOCTYPE html>
<head>
	<title> Edit Comment</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="edit_comment.css">
</head>

<body>

<div id="banner"></div>
<h1>News Website:</h1>

<div id="logoutuser">
	<a href="logout.php">Log Out</a>
</div>

<?php
session_start();
include 'database.php';

if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$command = 'select comments.content, comments.article_id, comments.comment_id, comments.owner_id from comments where comment_id=?';
$comment_id = $_POST['comment_id'];

$stmt = $mysqli->prepare($command);
if(!$stmt){
    printf("Query Prep Failed: Failed", $mysqli->error);
    exit;
}

$stmt->bind_param('s', $comment_id);
$stmt->execute();
$stmt->bind_result($comments, $article_id, $comment_id, $owner_id);
$stmt->fetch();
$stmt->close();

//back button to return to page with article
echo '<div id="back">';
	$location = "read_article.php?id=".$article_id;
	echo "<a href='".$location."'>Back to Article</a>";
echo '</div>';

?>

<!-- edit comment form -->
<p id="leave_comment">Edit comment: <p>
<div id="comment">
	<form action="edit_comment.php" method="POST">
		<textarea name="comments" id="com_text"><?php echo $comments; ?></textarea><br>
		<?php 
		printf("<input type= 'hidden' name='comment_id' value= %s >", $comment_id);
		printf("<input type= \"hidden\" name= \"token\" value= %s >", $_SESSION['token']);
		?>
		<div id="sub_but">
			<input type="submit" name="submitcomment" value="Submit"/><br>
		</div>
	</form>
</div>

<?php
if(isset($_POST['submitcomment'])){
	$error=array();
	$valid_entry=true;

	//if comment isn't valid	
	if(empty($_POST['comments'])) {
		$error[] = '<li>You did not enter a valid comment</li>';
		$valid_entry=false;
	}
	if(!$valid_entry){
		echo "not valid entry";
		echo"<ul>";
		foreach($error as $error){
		echo $error;
		}
	echo "</ul>";
	}
	else {
		$owner_id=$_SESSION['user_id'];
		$comments= $mysqli->real_escape_string($_POST['comments']);
		$command="update comments set content='" . $comments .  "' where comment_id = " . $comment_id;
		echo $command;

		$stmt2=$mysqli->prepare($command);
		if(!$stmt2) {
			printf("Query Prep Failed: :(, $mysqli->error");
			exit;
		}

		$stmt2->execute();
		$stmt2->close();
		$header_location = "read_article.php?id=".$article_id;
		header("Location: $header_location");
	}	
}

?>
</body>
</html>
