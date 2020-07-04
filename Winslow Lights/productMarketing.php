


<?php  

include_once('commonFunctions.php');

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}




if (!empty($_POST)) 
{

    $target_dir = "media/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;

	print "Received {$_FILES['fileToUpload']['name']} - its size is {$_FILES['fileToUpload']['size']}";

	if ($uploadOk == 0) 
	{
	  echo "Sorry, your file was not uploaded.";
	
	} 
	else 
	{
	  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
	  {
		echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	    $mediaChecked = 1;
	    
		if (empty($_POST['media']))
		  $mediaChecked = 0;

		$enabledChecked = 1;
		if (empty($_POST['enabled']))
		  $enabledChecked = 0;
		
		
		$sql = "INSERT INTO productMedia(description, path, text, isVideo, enabled) VALUES('" . $_POST['description'] . "','" . $target_file . "', '" . $_POST['text'] . "','" . $mediaChecked . "','" . $enabledChecked . "')";

		if ($conn->query($sql) === TRUE)
		 {
		
		  echo "<h1>Your media was added to the database successfully.</h1>";
		} 
		else 
		{
		  echo "<h1>Error: " . $conn->error . "</h1>";
		}

		
	  } 
	  else 
	  {
		echo "Sorry, there was an error uploading your file. " . $target_file;
	  }
	}



}
$conn->close();

?>
	


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Registration Page</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">
</head>


<body>
<?php include("nav.php");  ?>
  	
	  <h1>Product Media</h1>
<form name="productMedia" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	<p><label for="description">Description:</label><br />
	  <input name="description" type="text" id="description" placeholder="what does this do?" maxlength="255" required ></p>
	
	<p><label for="File">File:</label><br />
	  <input name="fileToUpload" type="file" id="fileToUpload" required></p>
	
	
	<p><label for="text">Alt Text:</label><br />
	  <input name="text" type="text" id="text" placeholder="This fills in the alt text" required></p>
	
	<p><label for="media">Is media?:</label><br />
	  <input type="checkbox" id="media" name="media" value="1">
		<label for="media">Yes</label>
	
	</p>
	
	<p><label for="enabled">enabled?:</label><br />
	  <input type="checkbox" id="enabled" name="enabled" value="1">
		<label for="enabled">Yes</label>
	
	</p>

	
<button type="submit" name="Submit">Add Media</button>
</form>
</body>
	<?php include("footer.php"); ?>
</html>
