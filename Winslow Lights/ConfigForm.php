<?php

include_once('CommonFunctions.php');

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}


if(isset($_REQUEST['Config']))
{
	$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType,stripHeight, stripWidth, dma, gpio, brightness, enabled) VALUES('" . $_POST['LightSystemName'] . "','" . $_POST['ServerHostName'] . "', '" . $_POST['StripType'] . "','" . $_POST['StripHeight'] . "','" . $_POST['StripWidth'] . "','" . $_POST['DMA'] . "','" . $GPIO = $_POST['GPIO'] . "','" . $_POST['Brightness'] . "', '1')";


	if ($conn->query($sql) === TRUE) {
  	echo "<h1>Your record was added to the database successfully.</h1>";
} else {
  echo "<h1>Error: " . $conn->error . "</h1>";
}

$conn->close();

	

}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Config Page</title>
    <!-- Bootstrap -->
	<link href="../css/bootstrap-4.4.1.css" rel="stylesheet">
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
	  <h1>Config Page</h1>
	          <form name="Config Page" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	<p><label for="LightSystemName">Light System Name:</label><br />
	  <input name="LightSystemName" type="text" id="LightSystemName" placeholder="100 characters or less" maxlength="100"></p>
	
	<p><label for="ServerHostName">Server Host Name:</label><br />
	  <input name="ServerHostName" type="text" id="ServerHostName" placeholder="50 characters or less" maxlength="50"></p>
	
<?php
	
	

$displayStrip = mysqli_query($conn,"SELECT ID, stripName FROM lStripType");
$option = '';
while($query_data = mysqli_fetch_array($displayStrip))
{
	//echo $query_data['stripName'];
	//<option>$query_data['stripName']</option>
	$option .="<option value = '".$query_data['ID']."'>".$query_data['stripName']."</option>";
}
	
?>
		
	<p><label for="StripType">Strip Type:</label><br />
	<select name="StripType">
		<?php echo $option;?>
		</select>	
	</p>	
	
	<?php
	
	$conn->close();
	
	?>


<p><label for="StripHeight">Strip Height:</label><br />
	  <input type="number" id="StripHeight" name="StripHeight" min="1" value="1"></p>

<p><label for="StripWidth">Strip Width:</label><br />
	  <input type="number" id="StripWidth" name="StripWidth" min="1" value"10"></p>

<p><label for="DMA">DMA:</label><br />
	  <select name="DMA">
		  <option value="5">5</option>
		  <option value="10">10</option>
		  <option value="12">12</option>
	  </select>


</p>

<p><label for="GPIO">GPIO Pin:</label><br />
	  <input type="number" id="GPIO" name="GPIO" min="1" max="52" value="18"></p>
	
	<p><label for="Brightness">Brightness:</label><br />
	  <input type="number" id="Brightness" name="Brightness" min="1" max="200" value="60"></p>
	
<button type="submit" name="Config">Add Record</button>
</form>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
	<script src="../js/jquery-3.4.1.min.js"></script>

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/popper.min.js"></script> 
	<script src="../js/bootstrap-4.4.1.js"></script>
  </body>
	<footer>Winslow Lights&trade; &copy; 2020 All rights reserved. Powered by Romoserver</footer>
</html>
