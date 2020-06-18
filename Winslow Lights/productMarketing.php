<?php  

include_once('CommonFunctions.php');

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}


if (!empty($_POST)) 
{

    $mediaChecked = 1;
    if (empty($_POST['media']))
      $mediaChecked = 0;

    $enabledChecked = 1;
    if (empty($_POST['enabled']))
      $enabledChecked = 0;
	
	

	$sql = "INSERT INTO productMedia(description, path, text, isVideo, enabled) VALUES('" . $_POST['description'] . "','" . $_POST['path'] . "', '" . $_POST['text'] . "','" . $mediaChecked . "','" . $enabledChecked . "')";


	if ($conn->query($sql) === TRUE) {
	  echo "<h1>Your media was added to the database successfully.</h1>";
	} else {
	  echo "<h1>Error: " . $conn->error . "</h1>";
	}

	$conn->close();


}

?>
	


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Registration Page</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="Styles.css" rel="stylesheet" type="text/css">
</head>


<body>
<?php include("Nav.php");  ?>
  	
	  <h1>Product Media</h1>
<form name="productMedia" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	<p><label for="description">Description:</label><br />
	  <input name="description" type="text" id="description" placeholder="what does this do?" maxlength="255" required ></p>
	
	<p><label for="path">Path:</label><br />
	  <input name="path" type="text" id="path" placeholder="The location of the media" maxlength="255" required></p>
	
	
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
	<?php include("Footer.php"); ?>
</html>
