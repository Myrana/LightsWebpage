<?php  

include_once('CommonFunctions.php');

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}

$enabled = 0;
$media = 0;

if (!empty($_POST)) 
{
 
    $description= $_POST['description'];
    $path = $_POST['path'];
    $text = $_POST['text'];
    $media = $_POST['media'];
    $enabled = $_POST['enabled'];
   

	//$sql = "INSERT INTO lightSystems(systemName, serverHostName, stripType, stripHeight, stripWidth, dma, gpio) VALUES('')

	//$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType) VALUES('" . '$lightSystemName' . "','" . '$serverHostName' . "', 257)";

	$sql = "INSERT INTO productMedia(description, path, text, isVideo, enabled) VALUES('" . $_POST['description'] . "','" . $_POST['path'] . "', '" . $_POST['text'] . "','" . $_POST['media'] . "','" . $_POST['enabled'] . "')";


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

<script>
function includeHTML() {
  var z, i, elmnt, file, xhttp;
  /*loop through a collection of all HTML elements:*/
  z = document.getElementsByTagName("*");
  for (i = 0; i < z.length; i++) {
    elmnt = z[i];
    /*search for elements with a certain atrribute:*/
    file = elmnt.getAttribute("w3-include-html");
    if (file) {
      /*make an HTTP request using the attribute value as the file name:*/
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {elmnt.innerHTML = this.responseText;}
          if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
          /*remove the attribute, and call this function once more:*/
          elmnt.removeAttribute("w3-include-html");
          includeHTML();
        }
      }      
      xhttp.open("GET", file, true);
      xhttp.send();
      /*exit the function:*/
      return;
    }
  }
};
</script>
<body>
	<div w3-include-html="Nav.html"></div>
	
<script>
includeHTML();
</script>
  	<!-- body code goes here -->
	  <h1>Product Media</h1>
<form name="productMedia" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	<p><label for="description">Description:</label><br />
	  <input name="description" type="text" id="description" placeholder="what does this do?" maxlength="255"></p>
	
	<p><label for="path">Path:</label><br />
	  <input name="path" type="text" id="path" placeholder="The location of the media" maxlength="255"></p>
	
	
	<p><label for="text">Alt Text:</label><br />
	  <input name="text" type="text" id="text" placeholder="This fills in the alt text"></p>
	
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
</html>