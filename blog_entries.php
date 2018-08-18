<?php
	$edit = isset($_GET['edit']) ? $_GET['edit'] : ''; 
	$fetch_error = false;
	if(is_numeric($edit) && !$_POST){
		$conn = new mysqli('localhost','root','root','feedback-form'); // Here your database 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$data = $conn->query("SELECT * FROM blog_entries WHERE id=$edit");
		if($data && $data->num_rows > 0){
			$row = $data->fetch_assoc();
			$title = $row['title'];
			$author = $row['author'];
			$content = $row['content'];
			$success = 	isset($_GET['success']) ? $_GET['success'] : '';
			$error = array("title" => "","author" => "", "content" => "","database" => "");
		}else{
			$fetch_error = true;
		}
	} else {
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$author = isset($_POST['author']) ? $_POST['author'] : '';
		$content = isset($_POST['content']) ? $_POST['content'] : '';
		$success = 	isset($_GET['success']) ? $_GET['success'] : '';
		$error = array("title" => "","author" => "", "content" => "","database" => "");
	}
	if($_POST){
		if(strlen($title) == 0 || strlen($author) == 0 || strlen($content) == 0){
			if(strlen($title) == 0){
				$error['title'] = 'Error';
			}
			if(strlen($author) == 0){
				$error['author'] = 'Error';
			}
			if(strlen($content) == 0){
				$error['content'] = 'Error';
			}
		
		}else {
		
			$conn = new mysqli('localhost','root','root','feedback-form'); // Here your database 
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$title = $conn->real_escape_string($title);
			$author = $conn->real_escape_string($author);
			$content = $conn->real_escape_string($content);
			if(is_numeric($edit)){
				print "UPDATE blog_entries SET 'title'='$title' WHERE 'id'=$edit";
				$saved = $conn->query("UPDATE blog_entries SET title='$title',
					author='$author', content='$content' WHERE id='$edit'");
				print_r($conn->error);
			}else {
				$saved = $conn->query("INSERT INTO blog_entries (title, author, content) 
					VALUES ('$title','$author','$content')");
			}
			if($saved){
				header('Location: ' . $_SERVER['PHP_SELF'] . '?success=OK');
			}else{
				$error['database'] = "Error when saving";
			}
		}
	}
	if(strlen($success) == 0) {
?>
<!doctype html>
<html>
	<head>
			<title>Blog create</title>
	</head>
	<body>
		<h2>Add blog text</h2>
		<?php
			if($fetch_error == false){
				if($edit != ""){
					$path = $_SERVER['PHP_SELF'] . '?edit=' . $edit;
				}else{
					$path = $_SERVER['PHP_SELF'];
				} 
		?>
		<form method="post" action="<?php echo $path; ?>">
			<p>Title: 
				<input type="text" name="title" value="<?php echo $title; ?>"/>
				<?php echo $error['title']; ?>
			</p>
			<p>Author: <input type="text" name="author" 
				value="<?php echo $author; ?>"/>
				<?php echo $error['author']; ?></p>
			<p>Content: 
				<textarea name="content"><?php echo $content; ?></textarea>
				<?php echo $error['content']; ?>
			</p>
			<?php echo $error['database']; ?>
			<p><?php echo $success; ?>
			<p><input type="submit" value="Save"></p>
		</form>
		<?php
			} else {
		?>
			<p>Error fetching updatable content</p>
		<?php
			} 
		?>
	</body>
</html>
<?php
	}else {
		print "Ok";
	}
?>