<?php

include_once('CommonFunctions.php');


if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}

if(isset($_REQUEST['LightShow']))
{ 

	$systemName = $_POST['SystemName'];

	$onoff = "ON";
	if (empty($_POST['lights']))
		$onoff = "OFF";
	//$systemName = $_POST['Lights'];

	foreach($_POST['ShowName'] as $selectedOption)
	       $showArray[] = $selectedOption;

	$sendArray['state'] = $onoff;;
	$sendArray['shows'] = $showArray;
	$sendArray['brightness'] = $_POST['Brightness'];


	$displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$systemName);
	$option = '';
	$query_data = mysqli_fetch_array($displayStrip);

	sendMQTT($query_data['serverHostName'], json_encode($sendArray));
	
}


?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Name Page</title>
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
	
	
<?php
	
	
$displayStrip = mysqli_query($conn,"SELECT ID, systemName FROM lightSystems WHERE enabled = 1");
$option = '';
while($query_data = mysqli_fetch_array($displayStrip))
{
	//echo $query_data['stripName'];
	//<option>$query_data['stripName']</option>
	$option .="<option value = '".$query_data['ID']."'>".$query_data['systemName']."</option>";
}
	
?>
	
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<p><label for="SystemName">System Name:</label><br />
	<select name="SystemName">
		<?php echo $option;?>
		</select>	
	</p>
		<label for="On">On</label>
	<input type="checkbox" name="lights"  value="ON" checked>
		
		<?php
	
	

$displayStrip = mysqli_query($conn,"SELECT ID, showName FROM lightShows WHERE enabled = 1");
$option = '';
while($query_data = mysqli_fetch_array($displayStrip))
{
	//echo $query_data['stripName'];
	//<option>$query_data['stripName']</option>
	$option .="<option value = '".$query_data['ID']."'>".$query_data['showName']."</option>";
}
	
?>
		
	<p><label for="ShowName">Show Name (may select multiple)</label><br />
	<select name="ShowName[]" size="7" multiple= "multiple">
		<?php echo $option;?>
		</select>	
	</p>
		<p><label for="Brightness">Brightness:</label><br />
	  <input type="number" id="Brightness" name="Brightness" min="10" max="200" value="10"></p>
		
	
		<p><button type="submit" name="LightShow">Send Command</button></p>
	</form>
		
	
	<?php
	
	$conn->close();
	
	?>	
</body>
</html>
