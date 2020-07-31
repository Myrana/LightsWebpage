<?php

include_once('commonFunctions.php');

$conn = getDatabaseConnection();

if(!empty($_REQUEST))
{
    if(!empty($_POST['LightSystem']))
        $_SESSION["LightSystemID"]  = $_POST['LightSystem'];

}

if($_SESSION['authorized'] == 0 || $_SESSION['isAdmin'] == 0)
{
  header("Location: lightShows.php");
  exit();
}



if(isset($_REQUEST['Edit']))
{
	$twitchSupport =  '0';
	if(!empty($_POST['twitchSupport']))
	{
			$twitchSupport = '1';
	}
	
	$sql = "update lightSystems set SystemName = '" . $_POST['LightSystemName'] . "',serverHostName = '" . $_POST['ServerHostName'] . "', enabled='1',userId= '" . $_POST['userID'] . "', twitchSupport = '" . $twitchSupport . "', mqttRetries = '" . $_POST['mqttRetries'] . "', mqttRetryDelay = '" . $_POST['mqttRetryDelay'] . "' where ID = '" . $_POST['LightSystem'] . "';";
	
	$channelsql= "";
	$systemId = $conn->insert_id;
	$channelsql= "update lightsSystemsChannel set channelId = '1', lightSystemId = '".$systemId ."', stripType =  '" . $_POST['StripType'] ."', stripRows = '" . $_POST['StripRows'] . "', stripColumns = '" . $_POST['StripColumns'] . "', dma = '" . $_POST['DMA'] . "',gpio = '" . $_POST['GPIO'] . "',brightness = '" .
	$_POST['Brightness'] . "', gamma = '" . $_POST['gamma'] . "', , enabled='1' ;"; 

	
	
	if ($conn->query($sql) === TRUE)
	{
		
		$features = "";
		$featureDelete = "";

		if (!empty($_POST['motionFeature']))
		{
			$features = "('1','" . $_POST['LightSystem'] . "', '" . $_POST['motionFeatureGPIO'] . "', '" . $_POST['motionPlaylist'] . "', '" . $_POST['motionDelayOff'] . "','0','0','0')";
		}
		else
		{
			$featureDelete =  "1";
		}

		if (!empty($_POST['lightFeature']))
		{
			if(!empty($features)) $features .= ",";

			$features .= "('2','" . $_POST['LightSystem'] . "', '" . $_POST['lightFeatureGPIO'] . "', '" . $_POST['lightPlaylist'] . "','0','0','0','0')";

		}
		else
		{
			if(!empty($featureDelete)) $featureDelete .=  ",";
			$featureDelete .=  "2";
		}
		

		if (!empty($_POST['timeFeature'])) 
		{
			if(!empty($features)) $features .= ",";
			$features .= "('3','" . $_POST['LightSystem'] . "', '0','" . $_POST['timePlaylist'] . "', '0','" . $_POST['startTime'] . "', '" . $_POST['endTime'] . "','0')";
		}
		else
		{
			if(!empty($featureDelete)) $featureDelete .=  ",";
			$featureDelete .=  "3";
		}
		
		
		if (!empty($_POST['luxFeature'])) 
		{
			if(!empty($features)) $features .= ",";
			$features .= "('4','" . $_POST['LightSystem'] . "', '0','" . $_POST['luxPlaylist'] . "', '0','0', '0','" . $_POST['luxThreshHold'] . "')";
		}
		else
		{
			if(!empty($featureDelete)) $featureDelete .=  ",";
			$featureDelete .=  "4";
		}

		if(!empty($featureDelete))
		{
			$sql = "delete from lightSystemFeatures where featureId in (" . $featureDelete . ") and lightSystemId = " . $_POST['LightSystem'];
			if ($conn->query($sql) === FALSE)
			{
				echo "<h1>Error: " . $conn->error . "</h1>";
				echo $sql;	
			}
				
		}
		
		if(!empty($features))
		{
		 
			$sql = "INSERT INTO lightSystemFeatures(featureId, lightSystemId, featureGpio, featurePlaylist, motionDelayOff, timeFeatureStart, timeFeatureEnd, luxThreshHold) VALUES";
			$sql .= $features;
			 
			 
			$sql .= " ON DUPLICATE KEY UPDATE featureGpio = VALUES(featureGpio),featurePlaylist = VALUES(featurePlaylist),motionDelayOff = VALUES(motionDelayOff),
			timeFeatureStart = VALUES(timeFeatureStart),timeFeatureEnd = VALUES(timeFeatureEnd), luxThreshHold = VALUES(luxThreshHold);";

			if ($conn->query($sql) === TRUE)
				echo "<h1>Your record was Edited successfully.</h1>";
			else
			{
				echo "<h1>Error: " . $conn->error . "</h1>";
				echo $sql;	
			}
		}
		
		$sendArray["systemConfigChange"] = 1;
		
		sendMQTT(getServerHostName($_POST['LightSystem']), json_encode($sendArray));
		
	}
	else
	{
		echo "<h1>Error: " . $conn->error . "</h1>";
		echo $sql;	
	}
	
}


if(isset($_REQUEST['Config']))
{

	$twitchSupport =  '0';
	if(!empty($_POST['twitchSupport']))
	{
			$twitchSupport = '1';
	}


    $sql = "INSERT INTO lightSystems(systemName, serverHostName, enabled, userId, twitchSupport, mqttRetries, mqttRetryDelay) VALUES('" . $_POST['LightSystemName'] . 
		"','" . $_POST['ServerHostName'] . "', '1', '" . $_POST['userID'] . "', '" . $twitchSupport . "', '" . $_POST['mqttRetries'] . "', '" . $_POST['mqttRetryDelay'] . "')";
	
	$channelsql = "";
	$systemId = $conn->insert_id;
	
	$channelsql = "INSERT INTO lightSystemChannels(channelId, lightSystemId, stripType, stripRows, stripColumns, dma, gpio, brightness, gamma, enabled) VALUES ('1', '". $systemId. "', '". $_POST['StripType'] ."', '". $_POST['StripRows'] ."', '". $_POST['StripColumns'] ."', '". $_POST['DMA'] ."', '". $_POST['GPIO'] ."', '". $_POST['Brightness'] ."', '". $_POST['gamma'] ."', '1')";
	
	if ($conn->query($sql) === TRUE)
    {
		
		$features = "";
		$systemId = $conn->insert_id;

		if (!empty($_POST['motionFeature']))
			$features = "('1','" . $systemId. "', '" . $_POST['motionFeatureGPIO'] . "', '" . $_POST['motionPlaylist'] . "', '" . $_POST['motionDelayOff'] . "','0','0','0')";

		if (!empty($_POST['lightFeature']))
		{
			if(!empty($features)) $features .= ",";

			$features .= "('2','" . $systemId . "', '" . $_POST['lightFeatureGPIO'] . "', '" . $_POST['lightPlaylist'] . "','0','0','0','0')";

		}

		if (!empty($_POST['timeFeature'])) 
		{
			if(!empty($features)) $features .= ",";
			$features .= "('3','" . $systemId . "', '0','" . $_POST['timePlaylist'] . "', '0','" . $_POST['startTime'] . "', '" . $_POST['endTime'] . "','0')";
		}
		
		if (!empty($_POST['luxFeature'])) 
		{
			if(!empty($features)) $features .= ",";
			$features .= "('4','" . $systemId . "', '0','" . $_POST['luxPlaylist'] . "', '0','0', '0','" . $_POST['luxThreshHold'] . "')";
		}

		if(!empty($features))
		{
		 
			$sql = "INSERT INTO lightSystemFeatures(featureId, lightSystemId, featureGpio, featurePlaylist, motionDelayOff, timeFeatureStart, timeFeatureEnd, luxThreshHold) VALUES";
			$sql .= $features;
			 
			 
			$sql .= " ON DUPLICATE KEY UPDATE featureGpio = VALUES(featureGpio),featurePlaylist = VALUES(featurePlaylist),motionDelayOff = VALUES(motionDelayOff),
			timeFeatureStart = VALUES(timeFeatureStart),timeFeatureEnd = VALUES(timeFeatureEnd), luxThreshHold = VALUES(luxThreshHold);";

			if ($conn->query($sql) === TRUE)
				echo "<h1>Your record was added to the database successfully.</h1>";
			else
			{
				echo "<h1>Error: " . $conn->error . "</h1>";
				echo $sql;	
			}
			
			if ($conn->query($channelsql) === TRUE)
				echo "<h1>Your record was added to the database successfully.</h1>";
			else
			{
				echo "<h1>Error: " . $conn->error . "</h1>";
				echo $channelsql;	
			}
		}
    }
	else 
	{
		echo "<h1>Error: " . $conn->error . "</h1>";
		echo $sql;	
    }
	
	
	
    
}

if(isset($_REQUEST['Delete']))
{
	$sql = "DELETE FROM lightSystems WHERE ID =" . $_POST['LightSystem'];
	if ($conn->query($sql) === TRUE)
		echo "<h1>Your record was deleted from lightSystems database successfully.</h1>";
	else
		{
			echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
		}

	$sql = "DELETE FROM lightSystemFeatures WHERE lightSystemId =" .$_POST['LightSystem'];
	
	if ($conn->query($sql) === TRUE)
		echo "<h1>Your features record was deleted from lightSystemFeatures database successfully.</h1>";
	else
		{
			echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
		}
	
	$sql = "DELETE FROM lightSystemChannels WHERE lightSystemId =" .$_POST['LightSystem'];
	
	if ($conn->query($sql) === TRUE)
		echo "<h1>Your features record was deleted from lightSystemChannels database successfully.</h1>";
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
    $stripTypes .="<option value = '".$query_data['ID']."'>".$query_data['stripName']."</option>";
}


$displayUsername = mysqli_query($conn,"SELECT ID, username FROM lumaUsers ");
$users = '';
while($query_data = mysqli_fetch_array($displayUsername))
{
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
$results = mysqli_query($conn,"SELECT ID,systemName,serverHostName,userId, ls.enabled,TwitchSupport,mqttRetries,mqttRetryDelay,twitchMqttQueue,lc.* FROM LedLightSystem.lightSystems as ls, LedLightSystem.lightSystemChannels as lc where ls.id = lc.lightSystemId and lc.channelId = 1 and lc.enabled = 1 and ls.enabled = 1;");
if(mysqli_num_rows($results) > 0)
{
    $lightSystemsScript = "let systemsMap = new Map();\r\n";
    while($row = mysqli_fetch_array($results))
    {
        $lightSystemsScript .= "var system = new Object(); \r";

        $lightSystemsScript .= "    system.id = " . $row['ID'] .";\r";
        $lightSystemsScript .= "    system.systemName = '" . $row['systemName'] ."';\r";
        $lightSystemsScript .= "    system.stripType = '" . $row['stripType'] ."';\r";
        $lightSystemsScript .= "    system.stripColumns = " . $row['stripColumns'] .";\r";
        $lightSystemsScript .= "    system.stripRows = " . $row['stripRows'] .";\r";
        $lightSystemsScript .= "    system.dma = " . $row['dma'] .";\r";
        $lightSystemsScript .= "    system.gpio = " . $row['gpio'] .";\r";
        $lightSystemsScript .= "    system.serverHostName = '" . $row['serverHostName'] ."';\r";
        $lightSystemsScript .= "    system.brightness = " . $row['brightness'] .";\r";
        $lightSystemsScript .= "    system.enabled = " . $row['enabled'] .";\r";
        $lightSystemsScript .= "    system.userId = " . $row['userId'] .";\r";
        $lightSystemsScript .= "    system.gamma = " . $row['gamma'] .";\r";
		$lightSystemsScript .= "    system.twitchSupport = " . $row['TwitchSupport'] .";\r";
		$lightSystemsScript .= "    system.mqttRetries = " . $row['mqttRetries'] .";\r";
		$lightSystemsScript .= "    system.mqttRetryDelay = " . $row['mqttRetryDelay'] .";\r";

        $lightSystemsScript .= "systemsMap.set(" . $row['ID'] . ", system);\r";

        if($row['ID'] == $_SESSION["LightSystemID"])
            $systemlistoption .="<option value = '".$row['ID']."' selected>".$row['systemName']."</option>";
        else
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

		$lightFeaturesScript .= "if(!lightFeatureMap.has(" . $row['lightSystemId'] . "))\r";
		$lightFeaturesScript .= "{\r";
		$lightFeaturesScript .= "   var lightFeatures = new Map();\r";
		$lightFeaturesScript .= "   lightFeatureMap.set(" . $row['lightSystemId'] . ", lightFeatures);\r";
		$lightFeaturesScript .= "}\r";
       
        $lightFeaturesScript  .= "   lightFeatureMap.get(" . $row['lightSystemId'] . ").set(lightFeature.featureId,lightFeature);\r";
      
    }
}


$systemStatus = "";
if(isset($_REQUEST['Status']))
{
	$rcv_message = "";
	$statusmsg = "";
	requestSystemInfo(getServerHostName($_POST['LightSystem']));
	if(!empty($rcv_message) )
    {
			
        $systemInfo = json_decode($rcv_message);
		
		$systemStatus .= "<div id='systemStyles' class='systemStyles'><table style='width:100%; font-size:14px; font-weight:bold;'>";
		
		
		$systemStatus .= "<tr>" . "<td>Name</td>" . "<td>" . $systemInfo->{'systemName'} . "</td></tr>";
		$systemStatus .= "<tr>" . "<td>Temp</td>" . "<td>" . $systemInfo->{'systemTemp'} . "</td></tr>";
		$systemStatus .= "<tr>" . "<td>Up</td>" . "<td>" . $systemInfo->{'uptime'} . "</td></tr>";
		$systemStatus .= "<tr>" . "<td>Load</td>" . "<td>" . $systemInfo->{'load'} . "</td></tr>";
		$systemStatus .= "<tr>" . "<td>Total</td>" . "<td>" . $systemInfo->{'totalRam'} . "Ram</td></tr>";
		$systemStatus .= "<tr>" . "<td>Free</td>" . "<td>" . $systemInfo->{'freeRam'} . " Ram</td></tr>";
		$systemStatus .= "<tr>" . "<td>Queue</td>" . "<td>" . $systemInfo->{'showsInQueue'} . "</td></tr>";
		
		
		if($systemInfo->{'showsInQueue'} > 0)
        {
			$systemStatus .= "<tr>" . "<td>Show</td>" . "<td>" . $systemInfo->{'runningShow'} . "</td></tr>";
        }

		$systemStatus .= "<tr>" . "<td>Alerts</td>" . "<td>" . $systemInfo->{'alerts'} . "</td></tr>";

		$systemStatus .= "</table></div>";
    }
    else
    {
        echo $statusmsg."TIMEDOUT";
    }

} 

$conn->close();

?>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
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
	
	$(function () {
        $("#luxFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#luxFields").show();
            } else {
                $("#luxFields").hide();
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
    
	
	<script src="//kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>	
	<link href="css/Styles.css" rel="stylesheet" type="text/css">
  </head>
 
<body onload="setLightSystemSettings(true);">
<?php include("nav.php");  ?>

<script>


<?php echo $lightFeaturesScript;?>
<?php echo $lightSystemsScript;?>

function setLightSystemSettings(fromPost)
{

    var systemNameId = document.getElementById("LightSystem");
    var lightSystemName = document.getElementById("LightSystemName");
    var serverHostName = document.getElementById("ServerHostName");
    var stripColumns = document.getElementById("StripColumns");
    var stripRows = document.getElementById("StripRows");
    var dma = document.getElementById("DMA");
    var gpio = document.getElementById("GPIO");
    var brightness = document.getElementById("Brightness");
    var gamma = document.getElementById("gamma");
    var stripType = document.getElementById("StripType");
    var userID = document.getElementById("userID");
    var motionFeature = document.getElementById("motionFeature");
    var lightFeature = document.getElementById("lightFeature");
    var timeFeature = document.getElementById("timeFeature");
	var luxFeature = document.getElementById("luxFeature");
	var motionDelay = document.getElementById("motionDelay");
	var motionGpio = document.getElementById("motionFeatureGPIO");
	var motionPlaylist = document.getElementById("motionPlaylistId");
    var lightGpio = document.getElementById("lightFeatureGPIO");
    var lightPlaylist = document.getElementById("lightPlayListId");
    var timePlaylist = document.getElementById("timePlayListId");
    var startTime = document.getElementById("startTime");
    var endTime = document.getElementById("endTime");
	var luxThreshold = document.getElementById("luxThreshHold");
	var luxPlaylist = document.getElementById("luxPlaylistId");
	var twitchSupport = document.getElementById("twitchSupport");
	var mqttRetries = document.getElementById("mqttRetries");
	var mqttRetryDelay = document.getElementById("mqttRetryDelay");
	var systemStyles = document.getElementById("systemStyles");
	
	if(systemStyles && !fromPost)
		systemStyles.style.display = "none";
		
    var index = parseInt(systemNameId.value);
    var lightFeatureSettings = lightFeatureMap.get(index);
    
    var system = systemsMap.get(index);

    lightSystemName.value = system.systemName;
    serverHostName.value = system.serverHostName;
    stripColumns.value = system.stripColumns;
    stripRows.value = system.stripRows;
    dma.value = system.dma;
    gpio.value = system.gpio;
    brightness.value = system.brightness;
    gamma.value = system.gamma;
    stripType.value = system.stripType;
    userID.value = system.userId;
	mqttRetries.value = system.mqttRetries;
	mqttRetryDelay.value = system.mqttRetryDelay;
    twitchSupport.checked = system.twitchSupport;
    
	if(motionFeature.checked == true)
        motionFeature.click();

    if(lightFeature.checked == true)
        lightFeature.click();

    if(timeFeature.checked == true)
        timeFeature.click();
	

    if(lightFeatureSettings)
    {
		for (let [featureId, feature] of lightFeatureSettings)
		{
			switch(featureId)
			{
				case 1:
					motionDelay.value = feature.motionDelayOff;
					motionGpio.value = feature.featureGpio;
					motionPlaylist.value = feature.featurePlayList;
					motionFeature.click();
					break;

				case 2:
					lightGpio.value = feature.featureGpio;               
					lightPlaylist.value = feature.featurePlayList;
					lightFeature.click()
					break;

				case 3:
					timePlaylist.value = feature.featurePlayList;
					startTime.value    = feature.timeFeatureStart;
					endTime.value      = feature.timeFeatureEnd;
					timeFeature.click();
					
					break;
					
				case 4:
					luxPlaylist.value = feature.featurePlayList;
					luxThreshold.value = feature.luxThreshHold;
					luxFeature.click()
					break;

			}
		}
    }
    

}

function confirmDelete()
{
	  
  var systemNameId = document.getElementById("LightSystem");
    	
  var index = parseInt(systemNameId.value);
    
  var system = systemsMap.get(index);
    
  var retVal = confirm("Are you sure you want to delete? " + system.systemName);
  if (retVal)
      return false;
  else
    return false;
	
} 


</script>

	  <h1>Config Page</h1>
	<div class="clearfix">
	<div class="column thirty-three">
		<form name="Config Page" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		
			<p><label for="onLightSystem">Light System:</label><br />
			<select name="LightSystem" id="LightSystem" onChange="setLightSystemSettings(false);">
			<?php echo $systemlistoption;?>
			</select>	
	</p>
			<button type="submit" name="Config">Add Record</button> 
			<button type="submit" name="Edit">Edit Record</button>
			<button type="submit" onclick="return confirmDelete();" name="Delete">Delete Record</button>
			<button type="submit" name="Status">Status Of LightSystem</button> 
			
			<?php echo $systemStatus; ?>
			
		</div>
	
	          <div class="column thirty-three">
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
	



<label for="StripRows">Strip rows:</label>
	  <input type="number" id="StripRows" name="StripRows" min="1" value"10" style="width:15%"> </p>
<p><label for="StripColumns">Strip Columns:</label>
	  <input type="number" id="StripColumns" name="StripColumns" min="1" value="1" style="width:15%">

<p><label for="onDMA">DMA:</label><br />
	  <select name="DMA" id="DMA">
		  <option value="5">5</option>
		  <option value="10">10</option>
		  <option value="12">12</option>
	  </select>


</p>
<div class="clearfix">
	<div class="column" style="width: 50%">
<p><label for="GPIO">GPIO Pin:</label><br />
	  <input type="number" id="GPIO" name="GPIO" min="1" max="52" value="18"></p>
	
	<p><label for="Brightness">Brightness:</label><br />
	  <input type="number" id="Brightness" name="Brightness" min="1" max="255" value="60">
		</p>
					  
	<p><label for="ongamma">Gamma:</label><br />
<input type="number" id="gamma" name="gamma" step=".1" min=".1" max="3.0" value="1">
		</p>
	
</div>

	<div class="column" style="width: 50%">
		<p>
			<label for="twitchSupport">Twitch Support</label>
			<input type="checkbox" id="twitchSupport" name="twitchSupport" />
			</p>
		
		<p>
		
			<label for="mqttRetries">MQTT Retries:</label> <br />
			<input type="number" id="mqttRetries" name="mqttRetries" value="2000" />
		
		</p>
		
		<p>
		
			<label for="mqttRetryDelay">MQTT Retry Delay:</label> <br />
			<input type="number" id="mqttRetryDelay" name="mqttRetryDelay" value="2500" />
			
		</p>
	
	</div>
	
</div>		

	<p><label for="userID">Light System User:</label><br />
	<select name="userID" id="userID">
		<?php echo $users;?>
		</select>	
	</p>	
			</div>
				</div>		  
<div class="column thirty-three">
<div class="ColumnStyles">	
<p><label for="motionFeature">Use a motion sensor?</label>
	
	<input type="checkbox" id="motionFeature" name="motionFeature"/></p>
	
	<div id="motionFields" style="display: none">
	
		<label>Motion Delay:</label><br />
		<input type="number" id="motionDelay" name="motionDelayOff" min="5" value="10">
	<p>
	<label for="motionPlaylist">Motion Playlist:</label>
		<select id="motionPlayListId"  name="motionPlaylist">
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
		<select id="lightPlayListId"  name="lightPlaylist">
        <?php echo $playlistoption;?>
        </select>
		
		<P>
		
		<label for="OnlightFeatureGPIO">Light GPIO Pin:</label><br />
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
		<select id="timePlayListId"  name="timePlaylist">
        <?php echo $playlistoption;?>
        </select>
		
		</p>
	</div>
		</div>
	<div class="ColumnStyles">
	<p><label for="OnluxFeature">Use lux?</label>
		<input type="checkbox" id="luxFeature" name="luxFeature"/></p>
		<div id="luxFields" style="display: none">
		<input type="number" id="luxThreshHold" name="luxThreshHold" value="300">
		<p>
		<label for="luxPlaylist">Lux Playlist:</label>
			<select id="luxPlaylistId" name="luxPlaylist">
			<?php echo $playlistoption;?>
			</select>
		</p>	
		</div>
		
	
	</div>
	</form>
	</div>
		</div>
	<?php include('footer.php'); ?>
	
  </body>

</html>
 
