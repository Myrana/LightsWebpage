<?php

include_once('CommonFunctions.php');

$conn = getDatabaseConnection();


if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}


if(isset($_REQUEST['Config']))
{

    $sql = "INSERT INTO lightSystems(systemName, serverHostName, stripType, stripHeight, stripWidth, dma, gpio, brightness, enabled, userId, gamma) VALUES('" . $_POST['LightSystemName'] . "','" . $_POST['ServerHostName'] . "', '" . $_POST['StripType'] . "','" . $_POST['StripHeight'] . "','" . $_POST['StripWidth'] . "','" . $_POST['DMA'] . "','" . $GPIO = $_POST['GPIO'] . "','" . $_POST['Brightness'] . "', '1', '" . $_POST['userID'] . "', '" . $_POST['gamma'] . "')";
	if ($conn->query($sql) === TRUE)
    {
		 $systemId = $conn->insert_id;
		 
		 $sql = "INSERT INTO lightSystemFeatures(featureId, lightSystemId, featureGpio, featurePlaylist, motionDelayOff, timeFeatureStart, timeFeatureEnd) VALUES";
		 
		 
		 if (!empty($_POST['motionFeature']))
		 {
			$sql .= "('1','" . $systemId . "', '" . $_POST['motionFeatureGPIO'] . "', '" . $_POST['motionPlaylist'] . "', '" . $_POST['motionDelayOff'] . "','0','0')";

		 }
		  
		 if (!empty($_POST['lightFeature']))
		 {
			$sql .= ",('2','" . $systemId . "', '" . $_POST['lightFeatureGPIO'] . "', '" . $_POST['lightPlaylist'] . "','0','0','0')";
		
		 }

		
		 if (!empty($_POST['timeFeature']))
		 {
			 //('3','71', '0','28', '0',', ''); 
			 
			$sql .= ",('3','" . $systemId . "', '0','" . $_POST['timePlaylist'] . "', '0','" . $_POST['startTime'] . "', '" . $_POST['endTime'] . "')";
		 }
    
		$sql .= ";";
    
		if ($conn->query($sql) === TRUE)
		{
			echo "<h1>Your record was added to the database successfully.</h1>";
		}
		else
		{
			echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
		}
    }
	else 
	{
		echo "<h1>Error: " . $conn->error . "</h1>";
		echo $sql;	
    }
    
}



$displayStrip = mysqli_query($conn,"SELECT ID, stripName FROM lStripType");
$stripTypes = '';
while($query_data = mysqli_fetch_array($displayStrip))
{
    //echo $query_data['stripName'];
    //<option>$query_data['stripName']</option>
    $stripTypes .="<option value = '".$query_data['ID']."'>".$query_data['stripName']."</option>";
}


$displayUsername = mysqli_query($conn,"SELECT ID, username FROM registrationTable ");
$users = '';
while($query_data = mysqli_fetch_array($displayUsername))
{
    //echo $query_data['stripName'];
    //<option>$query_data['stripName']</option>
    $users .="<option value = '".$query_data['ID']."'>".$query_data['username']."</option>";
}
	
$playlistoption = '';
$results = mysqli_query($conn,"SELECT ID, playlistName FROM userPlaylist");
if(mysqli_num_rows($results) > 0)
{
    while($row = mysqli_fetch_array($results))
      $playlistoption .="<option value = '".$row['ID']."'>".$row['playlistName']."</option>";

}


$systemlistoption = '';
$results = mysqli_query($conn,"SELECT *  FROM lightSystems");
if(mysqli_num_rows($results) > 0)
{
    $lightSystemsScript = "let systemsMap = new Map();\r\n";
    while($row = mysqli_fetch_array($results))
    {
        $lightSystemsScript .= "var system = new Object(); \r";

        $lightSystemsScript .= "    system.id = " . $row['ID'] .";\r";
        $lightSystemsScript .= "    system.systemName = '" . $row['systemName'] ."';\r";
        $lightSystemsScript .= "    system.stripType = '" . $row['stripType'] ."';\r";
        $lightSystemsScript .= "    system.stripHeight = " . $row['stripHeight'] .";\r";
        $lightSystemsScript .= "    system.stripWidth = " . $row['stripWidth'] .";\r";
        $lightSystemsScript .= "    system.dma = " . $row['dma'] .";\r";
        $lightSystemsScript .= "    system.gpio = " . $row['gpio'] .";\r";
        $lightSystemsScript .= "    system.serverHostName = '" . $row['serverHostName'] ."';\r";
        $lightSystemsScript .= "    system.brightness = " . $row['brightness'] .";\r";
        $lightSystemsScript .= "    system.enabled = " . $row['enabled'] .";\r";
        $lightSystemsScript .= "    system.userId = " . $row['userId'] .";\r";
        $lightSystemsScript .= "    system.gamma = " . $row['gamma'] .";\r";

        $lightSystemsScript .= "systemsMap.set(" . $row['ID'] . ", system);\r";

        $systemlistoption .="<option value = '".$row['ID']."'>".$row['systemName']."</option>";

    }
}




$results = mysqli_query($conn,"SELECT * FROM lightSystemFeatures");
if(mysqli_num_rows($results) > 0)
{
    $lightFeaturesScript = "let lightFeatureMap = new Map();\r\n";
    while($row = mysqli_fetch_array($results))
    {
        $lightFeaturesScript .= "var lightFeature = new Object(); \r";

       $lightFeaturesScript .= "    lightFeature.lightSystemId = " . $row['lightSystemId'] .";\r";
       $lightFeaturesScript .= "    lightFeature.featureId = " . $row['featureId'] .";\r";
       $lightFeaturesScript .= "    lightFeature.featureGpio = " . $row['featureGpio'] .";\r";
       $lightFeaturesScript .= "    lightFeature.featurePlayList = " . $row['featurePlayList'] .";\r";
       $lightFeaturesScript .= "    lightFeature.motionDelayOff = " . $row['motionDelayOff'] .";\r";
       $lightFeaturesScript .= "    lightFeature.timeFeatureStart = '" . $row['timeFeatureStart'] ."';\r";
       $lightFeaturesScript .= "    lightFeature.timeFeatureEnd = '" . $row['timeFeatureEnd'] ."';\r";
       $lightFeaturesScript .= "    lightFeature.luxThreshHold = " . $row['luxThreshHold'] .";\r";
       $lightFeaturesScript .= "lightFeatureMap.set(" .  $row['lightSystemId'] . ", lightFeature);\r";
    }
}
$conn->close();

?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        $("#motionFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#motionFields").show();
            } else {
                $("#motionFields").hide();
            }
        });
    });
	
	$(function () {
        $("#lightFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#lightFields").show();
            } else {
                $("#lightFields").hide();
            }
        });
    });
	
	$(function () {
        $("#timeFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#timeFields").show();
            } else {
                $("#timeFields").hide();
            }
        });
    });
	
		
</script>


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
	<link href="css/Styles.css" rel="stylesheet" type="text/css">
  </head>
 
<body>
<?php include("Nav.php");  ?>

<script>
<?php echo $lightFeaturesScript;?>
<?php echo $lightSystemsScript;?>

function setLightSystemSettings()
{
    var systemNameId = document.getElementById("LightSystem");
    var lightSystemName = document.getElementById("LightSystemName");
    var serverHostName = document.getElementById("ServerHostName");
    var stripHeight = document.getElementById("StripHeight");
    var stripWidth = document.getElementById("StripWidth");
    var dma = document.getElementById("DMA");
    var gpio = document.getElementById("GPIO");
    var brightness = document.getElementById("Brightness");
    var gamma = document.getElementById("gamma");
    var stripType = document.getElementById("StripType");
    var userID = document.getElementById("userID");
    var motionFeature = document.getElementById("motionFeature");
    var lightFeature = document.getElementById("lightFeature");
    var timeFeature = document.getElementById("timeFeature");
	var motionDelay = document.getElementById("motionDelay");
	var motionGpio = document.getElementById("motionFeatureGPIO");
	var motionPlaylist = document.getElementById("motionPlaylist");


    var index = parseInt(systemNameId.value);
    var lightFeatureSettings = lightFeatureMap.get(index);
    var system = systemsMap.get(index);

    lightSystemName.value = system.systemName;
    serverHostName.value = system.serverHostName;
    stripHeight.value = system.stripHeight;
    stripWidth.value = system.stripWidth;
    dma.value = system.dma;
    gpio.value = system.gpio;
    brightness.value = system.brightness;
    gamma.value = system.gamma;
    stripType.value = system.stripType;
    userID.value = system.userId;

    if(timeFeature.checked == true)
        timeFeature.click();

    if(lightFeature.checked == true)
        lightFeature.click();

    if(timeFeature.checked == true)
        timeFeature.click();

    switch(lightFeatureSettings.featureId)
    {
        case 1:
			motionDelay.value = lightFeatureSettings.motionDelayOff;
			motionGpio.value = lightFeatureSettings.motionFeatureGPIO;
			motionPlaylist.value = lightFeatureSettings.motionPlaylist;
            motionFeature.click();
            break;

        case 2:
            lightFeature.click();
            alert(2);
            break;

        case 3:
            timeFeature.click();
            alert(3);
            break;

    }

   //alert(system.systemName);

}


</script>

	  <h1>Config Page</h1>
	<div class="clearfix">
	<div class="column" style="width: 33%">
		<form name="Config Page" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		
			<p><label for="onLightSystem">Light System:</label><br />
			<select name="LightSystem" id="LightSystem" onChange="setLightSystemSettings();">
			<?php echo $systemlistoption;?>
			</select>	
	</p>
			<button type="submit" name="Config">Add Record</button> 
			<button type="submit" name="Edit">Edit Record</button>
		
		</div>
	
	          <div class="column" style="width: 33%">
				  <div class="ColumnStyles">
	
	<p><label for="LightSystemName">Light System Name:</label><br />
	  <input name="LightSystemName" type="text" id="LightSystemName" placeholder="100 characters or less" maxlength="100"></p>
	
	<p><label for="ServerHostName">Server Host Name:</label><br />
	  <input name="ServerHostName" type="text" id="ServerHostName" placeholder="50 characters or less" maxlength="50"></p>
	

	<p><label for="StripType">Strip Type:</label><br />
	<select name="StripType" id="StripType">
		<?php echo $stripTypes;?>
		</select>	
	</p>	
	



<p><label for="StripHeight">Strip Height:</label><br />
	  <input type="number" id="StripHeight" name="StripHeight" min="1" value="1"></p>

<p><label for="StripWidth">Strip Width:</label><br />
	  <input type="number" id="StripWidth" name="StripWidth" min="1" value"10"></p>

<p><label for="onDMA">DMA:</label><br />
	  <select name="DMA" id="DMA">
		  <option value="5">5</option>
		  <option value="10">10</option>
		  <option value="12">12</option>
	  </select>


</p>

<p><label for="GPIO">GPIO Pin:</label><br />
	  <input type="number" id="GPIO" name="GPIO" min="1" max="52" value="18"></p>
	
	<p><label for="Brightness">Brightness:</label><br />
	  <input type="number" id="Brightness" name="Brightness" min="1" max="255" value="60">
		</p>
					  
	<p><label for="ongamma">Gamma:</label><br />
<input type="number" id="gamma" name="gamma" step=".1" min=".1" max="3.0" value="1">
		</p>
				  

	<p><label for="userID">Light System User:</label><br />
	<select name="userID" id="userID">
		<?php echo $users;?>
		</select>	
	</p>	
	
	</div>
		</div>		  
<div class="column" style="width: 33%">
<div class="ColumnStyles">	
<p><label for="motionFeature">Use a motion sensor?</label>
	
	<input type="checkbox" id="motionFeature" name="motionFeature"/></p>
	
	<div id="motionFields" style="display: none">
	
		<label>Motion Delay:</label><br />
		<input type="number" id="motionDelay" name="motionDelayOff" min="5" value="10">
	<p>
	<label for="motionPlaylist">Motion Playlist:</label>
		<select id="PlayListId"  name="motionPlaylist">
        <?php echo $playlistoption;?>
        </select>

	
	</p>
	
	<P>
	
	<label for="motionFeatureGPIO">Motion GPIO Pin:</label><br />
	  <input type="number" id="motionFeatureGPIO" name="motionFeatureGPIO" min="1" max="52" value="18">	
	
	</P>
	
	</div>
	</div>
	<div class="ColumnStyles">
	
	<p><label for="OnlightFeature">Use a light sensor?</label>
	
	<input type="checkbox" id="lightFeature" name="lightFeature"/></p>
	
	<div id="lightFields" style="display: none">
	
		<label for="OnlightPlaylist">Light Playlist:</label>
		<select id="PlayListId"  name="lightPlaylist">
        <?php echo $playlistoption;?>
        </select>
		
		<P>
		
		<label for="OnlightFeatureGPIO">Motion GPIO Pin:</label><br />
	  <input type="number" id="lightFeatureGPIO" name="lightFeatureGPIO" min="1" max="52" value="18">
		
		</P>
	
	</div>
			  
</div>
	
	<div class="ColumnStyles">
		
		<p><label for="OntimeFeature">Use time of day?</label>
	
	<input type="checkbox" id="timeFeature" name="timeFeature"/></p>
	<div id="timeFields" style="display: none">
	<label>Start Time:</label> <br />
		<input type="time" id="startTime" name="startTime" />
	<p>
		
	<label>End Time:</label> <br />
		<input type="time" id="endTime" name="endTime" />
		
	</p>
		<p>
		
			<label for="timePlaylist">Time Playlist:</label>
		<select id="PlayListId"  name="timePlaylist">
        <?php echo $playlistoption;?>
        </select>
		
		</p>
	</div>
		</div>
	</form>
	</div>
		</div>
	<?php include('Footer.php'); ?>
	
	
	
	
			  
				  
	

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
	<script src="../js/jquery-3.4.1.min.js"></script>

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/popper.min.js"></script> 
	<script src="../js/bootstrap-4.4.1.js"></script>
  </body>

</html>
 
