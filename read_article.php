<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="read_article.css">
</head>
<body>

<div id="banner"></div>
<h1>News Website</h1>

<!-- link to return to homepage -->
<div id="return">
	<a href="homepage.php">Back to Homepage</a>
</div>

<?php
session_start();
include 'database.php';

if(isset($_SESSION['user_id'])){
//logout button
        echo '<div id="logout">';
        	echo '<a href="logout.php">Log Out</a>';
        echo '</div>';
}

$article_id= str_replace("\"", "",$_GET['id']);
$article_id= $mysqli->real_escape_string($article_id);

if(isset($_SESSION['user_id'])){
	$favorites= 'select count(*), favorites.favorite_id from favorites join users on (favorites.user_id=users.user_id) join articles on (articles.article_id=favorites.article_id) where articles.article_id='. $article_id .' and favorites.user_id='.$_SESSION['user_id'];
	$stmt=$mysqli->prepare($favorites);
	if(!$stmt) {
		printf("Query Prep Failed: :(, $mysqli->error");
		exit;
	}

	$stmt->execute();
	$stmt->bind_result($count, $favorite_id);
	$stmt->fetch();
	
	//show unfavorite buttons
	if($count == 1){
		printf('<form action="delete_favorite.php" method="POST">');
			printf("<input type= \"hidden\" name= \"favorite_id\" value= %s >", $favorite_id);
			printf("<input type= \"hidden\" name= \"article_id\" value= %s >", $article_id);
			printf("<input type= \"hidden\" name= \"token\" value= %s >", $_SESSION['token']);
			echo '<div id="submit_delete">';
				echo '<input type="submit" name="submit_delete" value="Unfavorite"/><br>';
			echo '</div>';
		printf('</form>');
	}
	//show favorite button
	else{
		printf('<form action="add_favorite.php" method="POST">');
			printf("<input type= \"hidden\" name= \"article_id\" value= %s >", $article_id);
			printf("<input type= \"hidden\" name= \"token\" value= %s >", $_SESSION['token']);
			echo '<div id="submit_add">';
				echo '<input type="submit" name="submit_add" value="Favorite"/><br>';
			echo '</div>';
		printf('</form>');
	}
	$stmt->close();	
}

//need to get for article id instead of selecting first article
$getArticle = 'select title, content, article_id, owner_id, article_link, users.first_name, users.last_name  from articles join users on (articles.owner_id=users.user_id) where article_id =';
$getArticle .= $article_id;
$stmt = $mysqli->prepare($getArticle);

if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}

$stmt->execute();
$stmt->bind_result($title, $content, $article_id, $owner_id, $url, $first, $last);
//on opening page bind fetch user id and put session varaible
//if match for articles id then create h ref for edit
$stmt->fetch();

//display article title, content, etc
echo '<div id="article_info">';
	echo '<p id="article_header">Article:</p>';
	echo '<p id="title_header">Title: </p>';
	printf('%s', htmlentities($title));
	echo '<br>';
	echo '<p id="content">Content:</p>';
	printf('%s', htmlentities($content));
echo '</div>';

echo '<div id="info">';
	if(!empty($url)){
		printf('<br><a target="_blank"  href=%s>Link to original article</a>', htmlentities($url));
	}
	printf('<br>Posted by %s %s', htmlentities($first), htmlentities($last));
	echo '<br>';
	echo '<br>';
echo '</div>';
echo '<div id="article_end"></div>';
		 	
//if owner matches session print link
if(isset($_SESSION['user_id'])){
//if user is owner of the article - edit or delete
	if($owner_id==$_SESSION['user_id']){
		//edit article button
		printf('<form action="edit_article.php" method="POST">');
			printf("<input type = 'hidden' name='article_id' value=%s>", $article_id);
			printf("<input type= 'hidden' name= 'token' value= %s>", $_SESSION['token']);
			echo '<div id="edit_art">';
				echo '<input type="submit" name="edit_article" value="Edit Article"><br>';
			echo '</div>';
		echo '</form>';
		
		//delete article button
		printf('<form action="delete_article.php" method="POST">');
			printf("<input type='hidden' name='article_id' value=%s>", $article_id);
			printf("<input type= 'hidden'  name='token' value= %s>", $_SESSION['token']);
			echo '<div id="delete_art">';
				echo '<input type="submit" name="delete_article" value="Delete Article"><br>';
			echo '</div>';
		echo '</form>';
	}

	//post comment form
  	echo '<p id="leave_comment">Leave a comment</p>';
        echo '<div id="comment">';
        	echo '<form action="post_new_comment.php" method="POST">';
 		       echo '<textarea name="comments"></textarea><br>';
		       printf("<input type='hidden' name='article_id' value= %s >", $article_id);
		       printf("<input type= 'hidden' name= 'token' value= %s>", $_SESSION['token']);
		       echo '<input type="submit" name="submitcomment" value="Submit"/><br>';
	        echo '</form>';
        echo '</div>';       
}

//post comments onto page below articles
$stmt->close();
$getComment = 'select users.first_name, users.last_name, comments.comment_id, comments.owner_id,comments.content from comments join users on(users.user_id = comments.owner_id) where article_id='. $article_id;

$stmt2 = $mysqli->prepare($getComment);

if(!$stmt2){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt2->execute();
$stmt2->bind_result($first, $last, $comment_id, $owner_id, $comments);

//print comment        
echo '<p id="comment_head">Comments:</p>';
while($stmt2->fetch()){
	echo '<div id="comment_start"></div>';
	echo '<br>';
	echo '<div id="comment_posted">';	
		printf('%s', htmlentities($comments));
		echo '<br>';
		echo '<div id="posted_by">';
			printf('<br>Posted by %s %s', htmlentities($first), htmlentities($last));
		echo '</div>';
		echo '<br>';
		echo '<div id="line"><div>';
	echo '</div>';

//if user is owner of comment - edit or delete
if(isset($_SESSION['user_id'])){
	if($owner_id==$_SESSION['user_id']){
		//edit comment button
		printf('<form action="edit_comment.php" method="POST">');
			printf("<input type='hidden' name='comment_id' value=%s>", $comment_id);
			printf("<input type= 'hidden' name= 'token' value= %s>", $_SESSION['token']);
			echo '<div id="edit_but">';
				echo '<input type="submit" name="edit_comment" value="Edit Comment">';
			echo '</div>';
		echo '</form>';

		//delete comment button
		printf('<form action="delete_comment.php" method="POST">');
			printf("<input type = 'hidden' name='comment_id' value=%s>", $comment_id);
			printf("<input type= 'hidden' name= 'token' value= %s>", $_SESSION['token']);
			echo '<div id="delete_but">';
				echo '<input type="submit" name="delete_comment" value="Delete Comment"><br>';
			echo '</div>';
		echo '</form>';
		echo '<div id="comment_end"></div>';
		}	
	}
}
?>
</body>
</html>
