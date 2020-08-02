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
	
	$systemEnabled =  '0';
	if(!empty($_POST['systemEnabled']))
	{
			$systemEnabled = '1';
	}
	
	$channelEnabled =  '0';
	if(!empty($_POST['channelEnabled']))
	{
			$channelEnabled = '1';
	}
	
	$sql = "update lightSystems set SystemName = '" . $_POST['LightSystemName'] . "',serverHostName = '" . $_POST['ServerHostName'] . "', enabled='" . $systemEnabled . "',userId= '" . $_POST['userID'] . "', twitchSupport = '" . $twitchSupport . "', mqttRetries = '" . $_POST['mqttRetries'] . "', mqttRetryDelay = '" . $_POST['mqttRetryDelay'] . "', twitchMqttQueue = '" . $_POST['twitchMqttQueue'] ."'  where ID = '" . $_POST['LightSystem'] . "';";
	if ($conn->query($sql) === TRUE)
	{
		$channels = "";
		
		if(!empty($_POST['channelEnabled']))
			$channels .= "('1','" . $systemId. "', '" . $_POST['StripType'] . "', '" . $_POST['StripRows'] . "', '" . $_POST['StripColumns'] . "', '" . $_POST['DMA'] . "', '" . $_POST['GPIO'] . "', '" . $_POST['Brightness'] . "', '" . $_POST['gamma'] . "', '1')";
	
		if (!empty($_POST['channelEnabled2']))
		{
			if(!empty($channels)) $channels .= ",";
				
			$channels .= "('2','" . $systemId. "', '" . $_POST['StripType2'] . "', '" . $_POST['StripRows2'] . "', '" . $_POST['StripColumns2'] . "', '" . $_POST['DMA2'] . "', '" . $_POST['GPIO2'] . "', '" . $_POST['Brightness2'] . "', '" . $_POST['gamma2'] . "', '1')";
		}
		
		if(!empty($channels))
		{
		 
			$sql = "INSERT INTO lightSystemChannels(channelId, lightSystemId, stripType, stripRows, stripColumns, dma, gpio,brightness, gamma, enabled) VALUES";
			$sql .= $channels;
			 
			 
			$sql .= " ON DUPLICATE KEY UPDATE stripType = VALUES(stripType),stripType = VALUES(stripType),stripColumns = VALUES(stripColumns),
			dma = VALUES(dma),gpio = VALUES(gpio), brightness = VALUES(brightness), gamma = VALUES(gamma), enabled = VALUES(enabled);";

			if ($conn->query($sql) === TRUE)
			{
				$sql= "";
				$sql= "update lightSystemChannels set stripType =  '" . $_POST['StripType'] ."', stripRows = '" . $_POST['StripRows'] . "', stripColumns = '" . $_POST['StripColumns'] . "', dma = '" . $_POST['DMA'] . "',gpio = '" . $_POST['GPIO'] . "',brightness = '" . $_POST['Brightness'] . "', gamma = '" . $_POST['gamma'] . "', enabled='" . $channelEnabled . "' where channelId = '1' and lightSystemId = '". $_POST['LightSystem'] . "';"; 

			
				if ($conn->query($sql) === TRUE)
				{
					
					$features = "";
					
					$features = "";
					if (!empty($_POST['motionFeature']))
						$features .= "('1','" . $systemId. "', '" . $_POST['motionFeatureGPIO'] . "', '" . $_POST['motionPlaylist'] . "', '" . $_POST['motionDelayOff'] . "','0','0','0')";

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
							echo "<h1>Your Features were added to the database successfully.</h1>";
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
			else
			{
				echo "<h1>Error: " . $conn->error . "</h1>";
				echo $sql;	
			}
		
		}
		
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
	
	$systemEnabled =  '0';
	if(!empty($_POST['systemEnabled']))
	{
			$systemEnabled = '1';
	}
	
	

    $sql = "INSERT INTO lightSystems(systemName, serverHostName, enabled, userId, twitchSupport, mqttRetries, mqttRetryDelay, twitchMqttQueue) VALUES('" . $_POST['LightSystemName'] . 
		"','" . $_POST['ServerHostName'] . "', '" . $systemEnabled . "', '" . $_POST['userID'] . "', '" . $twitchSupport . "', '" . $_POST['mqttRetries'] . "', '" . $_POST['mqttRetryDelay'] . "', '" . $_POST['twitchMqttQueue'] . "')";
	
	if ($conn->query($sql) === TRUE)
    {
		$sql = "";
		$systemId = $conn->insert_id;
		echo "<h1>Your LightSystem record was added to the database successfully.</h1>";
		
		//$sql = "INSERT INTO lightSystemChannels(channelId, lightSystemId, stripType, stripRows, stripColumns, dma, gpio, 
		//brightness, gamma, enabled) 		VALUES ('" . $channelEnabled . "', '". $systemId . "', '". $_POST['StripType'] ."', '". $_POST['StripRows'] ."', '". $_POST['StripColumns'] ."', '". $_POST['DMA'] ."', '". $_POST['GPIO'] ."', '". $_POST['Brightness'] ."', '". $_POST['gamma'] ."', '" . $channelEnabled . "')";
		
		$channels = "";
		
		if(!empty($_POST['channelEnabled']))
			$channels .= "('1','" . $systemId . "', '" . $_POST['StripType'] . "', '" . $_POST['StripRows'] . "', '" . $_POST['StripColumns'] . "', '" . $_POST['DMA'] . "', '" . $_POST['GPIO'] . "', '" . $_POST['Brightness'] . "', '" . $_POST['gamma'] . "', '1')";
	
		if (!empty($_POST['channelEnabled2']))
		{
			if(!empty($channels)) $channels .= ",";
				
			$channels .= "('2','" . $systemId. "', '" . $_POST['StripType2'] . "', '" . $_POST['StripRows2'] . "', '" . $_POST['StripColumns2'] . "', '" . $_POST['DMA2'] . "', '" . $_POST['GPIO2'] . "', '" . $_POST['Brightness2'] . "', '" . $_POST['gamma2'] . "', '1')";
		}
		
		if(!empty($channels))
		{
		 
			$sql = "INSERT INTO lightSystemChannels(channelId, lightSystemId, stripType, stripRows, stripColumns, dma, gpio,brightness, gamma, enabled) VALUES";
			$sql .= $channels;
			 
			 
			$sql .= " ON DUPLICATE KEY UPDATE stripType = VALUES(stripType),stripType = VALUES(stripType),stripColumns = VALUES(stripColumns),
			dma = VALUES(dma),gpio = VALUES(gpio), brightness = VALUES(brightness), gamma = VALUES(gamma), enabled = VALUES(enabled);";

			if ($conn->query($sql) === TRUE)
			{
				echo "<h1>Your Channel records were added to the database successfully.</h1>";
				
				$features = "";
				if (!empty($_POST['motionFeature']))
					$features .= "('1','" . $systemId. "', '" . $_POST['motionFeatureGPIO'] . "', '" . $_POST['motionPlaylist'] . "', '" . $_POST['motionDelayOff'] . "','0','0','0')";

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
						echo "<h1>Your Features were added to the database successfully.</h1>";
					else
					{
						echo "<h1>Error: " . $conn->error . "</h1>";
						echo $sql;	
					}
				}
		
			}
			else
			{
				echo "<h1>Error: " . $conn->error . "</h1>";
				echo $sql;	
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
$results = mysqli_query($conn,"SELECT ID,systemName,serverHostName,userId, ls.enabled as systemEnable,TwitchSupport,mqttRetries,mqttRetryDelay,twitchMqttQueue,lc.* FROM LedLightSystem.lightSystems as ls, LedLightSystem.lightSystemChannels as lc where ls.id = lc.lightSystemId and lc.channelId = 1;");
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
		$lightSystemsScript .= "    system.mqttRetryDelay = " . $row['mqttRetryDelay']   .";\r";
		$lightSystemsScript .= "    system.twitchMqttQueue = '" . $row['twitchMqttQueue'] ."';\r";
		$lightSystemsScript .= "    system.systemEnabled = " . $row['systemEnable'] .";\r";
		$lightSystemsScript .= "    system.channelEnabled = " . $row['enabled'] .";\r";

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
<?php 
$title = 'Config Form';
include('header.php'); 
?>
 
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
	var twitchMqttQueue = document.getElementById("twitchMqttQueue");
	var systemEnabled = document.getElementById("systemEnabled");
	var channelEnabled = document.getElementById("channelEnabled")
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
	twitchMqttQueue.value = system.twitchMqttQueue;
    twitchSupport.checked = system.twitchSupport;
	systemEnabled.checked = system.systemEnabled;
	channelEnabled.checked = system.channelEnabled;
    
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
      return true;
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

		<p>
			<label for="enabled">Enabled</label>
			<input type="checkbox" id="systemEnabled" name="systemEnabled" />
			
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
		
		<p>
		
			<label for="twitchMqttQueue">MQTT Queue:</label> <br />
			<input type="text" id="twitchMqttQueue" name="twitchMqttQueue" placeholder="Queue name here" />
			
			
		</p>
					 
		<p><label for="userID">Light System User:</label><br />
	<select name="userID" id="userID">
		<?php echo $users;?>
		</select>	
	</p>
	
	</div>
		<div class="column fifty">
		<div class="ColumnStyles">
			Channel 1
		<table>
			<tr>
				<td><label for="StripType">Strip Type:</label></td>
	<td><select name="StripType" id="StripType">
		<?php echo $stripTypes;?>
		</select></td>	
	</tr>	
	

	<tr>
	  <td><label for="StripRows">Strip rows:</label></td>
	  <td><input type="number" id="StripRows" name="StripRows" min="1" value"10"></td>
	</tr>
	<tr>
		<td><label for="StripColumns">Strip Columns:</label></td>
	    <td><input type="number" id="StripColumns" name="StripColumns" min="1" value="1"></td>
	</tr>

	<tr>
		<td><label for="onDMA">DMA:</label></td>
	    <td><select name="DMA" id="DMA">
		  <option value="5">5</option>
		  <option value="10">10</option>
		  <option value="12">12</option>
	  </select></td>


</tr>

<tr>
	<td><label for="GPIO">GPIO Pin:</label></td>
	<td><input type="number" id="GPIO" name="GPIO" min="1" max="52" value="18"></td>
</tr>
	
	<tr>
		<td><label for="Brightness">Brightness:</label></td>
	  	<td><input type="number" id="Brightness" name="Brightness" min="1" max="255" value="60"></td>
	</tr>
					  
	<tr>
		<td><label for="ongamma">Gamma:</label></td>
		<td><input type="number" id="gamma" name="gamma" step=".1" min=".1" max="3.0" value="1"></td>
		</tr>
	
	<tr>
		<td><label for="enabled">Enabled</label>
		<input type="checkbox" id="channelEnabled" name="channelEnabled" /></td>
		
	</tr>
		</table>		  
		</div>
			</div>
		
		<div class="column fifty">
		<div class="ColumnStyles">
			Channel 2
		<table>
			<tr>
				<td><label for="StripType2">Strip Type:</label></td>
	<td><select name="StripType2" id="StripType2">
		<?php echo $stripTypes;?>
		</select></td>	
	</tr>	
	

	<tr>
	  <td><label for="StripRows2">Strip rows:</label></td>
	  <td><input type="number" id="StripRows2" name="StripRows2" min="1" value"10"></td>
	</tr>
	<tr>
		<td><label for="StripColumns2">Strip Columns:</label></td>
	    <td><input type="number" id="StripColumns2" name="StripColumns2" min="1" value="1"></td>
	</tr>

	<tr>
		<td><label for="onDMA2">DMA:</label></td>
	    <td><select name="DMA2" id="DMA2">
		  <option value="5">5</option>
		  <option value="10">10</option>
		  <option value="12">12</option>
	  </select></td>


</tr>

<tr>
	<td><label for="GPIO2">GPIO Pin:</label></td>
	<td><input type="number" id="GPIO2" name="GPIO2" min="1" max="52" value="18"></td>
</tr>
	
	<tr>
		<td><label for="Brightness2">Brightness:</label></td>
	  	<td><input type="number" id="Brightness2" name="Brightness2" min="1" max="255" value="60"></td>
	</tr>
					  
	<tr>
		<td><label for="ongamma2">Gamma:</label></td>
		<td><input type="number" id="gamma2" name="gamma2" step=".1" min=".1" max="3.0" value="1"></td>
		</tr>
	
	<tr>
		<td><label for="enabled2">Enabled</label>
		<input type="checkbox" id="channelEnabled2" name="channelEnabled2" /></td>
		
	</tr>
		</table>		  
		</div>
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
 
