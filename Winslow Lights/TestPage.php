<?php

include_once('CommonFunctions.php');

$_SESSION["Brightness"] = 20;
$_SESSION["LightSystemID"] = -1;

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}

if(isset($_REQUEST['Power']))
{ 
echo "1";
    $_SESSION["LightSystemID"]  = $_POST['SystemName'];
    $_SESSION["Brightness"] = $_POST['Brightness'];
    $onoff = "ON";
    if (empty($_POST['lights']))
      $onoff = "OFF";
    
    $sendArray['state'] = $onoff;;
    $displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
    $query_data = mysqli_fetch_array($displayStrip);

    sendMQTT($query_data['serverHostName'], json_encode($sendArray));

}


if(isset($_REQUEST['ConfigShow']))
{ 
echo "2";
    $_SESSION["LightSystemID"]  = $_POST['SystemName'];
    $_SESSION["Brightness"] = $_POST['Brightness'];


    $displayStrip = mysqli_query($conn,"SELECT serverHostName,numColors,hasDelay, hasSpeed, isBlink, hasWidth  FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
    $query_data = mysqli_fetch_array($displayStrip);
    if(mysqli_num_rows($query_data) >= 1)
    {
     	$_SESSION["numColors"] = $query_data['numColors'];
     	$_SESSION["hasDelay"] = $query_data['hasDelay'];
     	$_SESSION["hasSpeed"] = $query_data['hasSpeed'];
     	$_SESSION["isBlink"] = $query_data['isBlink'];
     	$_SESSION["hasWidth"] = $query_data['hasWidth'];
     	
    }

}



if(isset($_REQUEST['LightShow']))
{ 
echo "3";
    $_SESSION["LightSystemID"]  = $_POST['SystemName'];
    $_SESSION["Brightness"] = $_POST['Brightness'];

    foreach($_POST['ShowName'] as $selectedOption)
	$showArray[] = $selectedOption;

    $sendArray['shows'] =  $_POST['ShowName'];
    $sendArray['brightness'] = $_SESSION["Brightness"];


    $displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
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
    if($query_data['ID'] == $_SESSION["LightSystemID"] )
    {

        $option .="<option value = '".$query_data['ID']."' selected='selected'>".$query_data['systemName']."</option>";
    }
    else
    {
        $option .="<option value = '".$query_data['ID']."'>".$query_data['systemName']."</option>";
    }
}
	
?>
	
	<div class="column">
	
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<p><label for="SystemName">System Name:</label><br />
	<select name="SystemName">
		<?php echo $option;?>
		</select>	
	</p>
		<label for="On">On</label>
	<input type="checkbox" name="lights"  value="ON" checked>
	<p><button type="submit" name="Power">Power</button></p>	
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

	<p><label for="ShowName">Show Name</label><br />
	<select name="ShowName" size="7">
		<?php echo $option;?>
		</select>
		
	</p>
		<p><label for="Brightness">Brightness:</label><br />
<input type="range" step="1" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="10" max="200">
Value: <span id="BrightnessValue"></span></p>

<script>
var slider = document.getElementById("Brightness");
var output = document.getElementById("BrightnessValue");
output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>
	
		<p><button type="submit" name="LightShow">Send Command</button></p>
	</form>
	
	</div>
<div class="column">
	
	<form>
	
	<input type="color" id="color 1"><label for ="color 1">Color 1</label> <br />
		<input type="color" id="color 2"><label for ="color 2">Color 2</label> <br />
		<input type="color" id="color 3"><label for ="color 3">Color 3</label> <br />
		<input type="color" id="color 4"><label for ="color 4">Color 4</label> <br />
	
	</form>
	
	</div>	

	
	<?php
	
	$conn->close();
	
	?>	
</body>
</html>
