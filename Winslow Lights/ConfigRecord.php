<?php  

$servername = "romoserver.local";
$username = "hellweek";
$password = "covert69guess";
$dbName = "LedLightSystem";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbName);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} 

if (isset($_POST['submit'])) {
    $lightSystemName = $_POST['LightSystemName'];
	$serverHostName = $_POST['ServerHostName'];
	$stripType = $_POST['StripType'];
    $stripHeight = $_POST['StripHeight'];
    $stripWidth = $_POST['StripWidth'];
	$DMA = $_POST['DMA'];
	$GPIO = $_POST['GPIO'];
	$Brightness = $_POST['Brightness'];

}

//$sql = "INSERT INTO lightSystems(systemName, serverHostName, stripType, stripHeight, stripWidth, dma, gpio) VALUES('')

//$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType) VALUES('" . '$lightSystemName' . "','" . '$serverHostName' . "', 257)";

$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType,stripHeight, stripWidth, dma, gpio, brightness, enabled) VALUES('" . $_POST['LightSystemName'] . "','" . $_POST['ServerHostName'] . "', '" . $_POST['StripType'] . "','" . $_POST['StripHeight'] . "','" . $_POST['StripWidth'] . "','" . $_POST['DMA'] . "','" . $GPIO = $_POST['GPIO'] . "','" . $_POST['Brightness'] . "', '1')";


if ($conn->query($sql) === TRUE) {
  echo "<h1>Your record was added to the database successfully.</h1>";
} else {
  echo "<h1>Error: " . $conn->error . "</h1>";
}

$conn->close();

	

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Record Submitted</title>
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
	<label>Light System Name:</label>
	<span><?php echo $_POST['LightSystemName']; ?></span> <br />
	
	<label>Server Host Name:</label>
	<span><?php echo $_POST['ServerHostName']; ?></span> <br />
	
	<label>Strip Type:</label>
	<span><?php echo $_POST['StripType']; ?></span> <br />
	
	<label>Strip Height:</label>
	<span><?php echo $_POST['StripHeight']; ?></span> <br />
	
	<label>Strip Width:</label>
	<span><?php echo $_POST['StripWidth']; ?></span> <br />
	
	<label>DMA:</label>
	<span><?php echo $_POST['DMA']; ?></span> <br />
	
	<label>GPIO Pin:</label>
	<span><?php echo $_POST['GPIO']; ?></span> <br />
	
	<label>Brightness:</label>
	<span><?php echo $_POST['Brightness']; ?></span> <br />
	
</body>
</html>