<?php

include('CommonFunctions.php');

$_SESSION["Brightness"] = 127;
$_SESSION["LightSystemID"] = -1;
$_SESSION["Delay"] = 10;
$_SESSION["Minutes"] = 1;
$_SESSION["Width"] = 1;
$_SESSION["ColorEvery"] = 2;
$_SESSION["ShowName"] = -1;
$_SESSION["ChgBrightness"] = 20;

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}


if(!empty($_REQUEST))
{
    $sendArray['UserID'] = $_SESSION['UserID'];
    if(!empty($_POST['SystemName']))
        $_SESSION["LightSystemID"]  = $_POST['SystemName'];

    if(!empty($_POST['Brightness']))
        $_SESSION["Brightness"] = $_POST['Brightness'];

    if(!empty($_POST['Delay']))
        $_SESSION["Delay"] = $_POST['Delay'];

    if(!empty($_POST['Minutes']))
        $_SESSION["Minutes"] = $_POST['Minutes'];

    if(!empty($_POST['Width']))
        $_SESSION["Width"] = $_POST['Width'];

    if(!empty($_POST['ColorEvery']))
        $_SESSION["ColorEvery"] = $_POST['ColorEvery'];

    if(!empty($_POST['ChgBrightness']))
        $_SESSION["ChgBrightness"] = $_POST['ChgBrightness'];

    if(!empty($_POST['ShowName']))
        $_SESSION["ShowName"] = $_POST['ShowName'];

}

if(isset($_REQUEST['Power']))
{

    $onoff = "ON";
    if (empty($_POST['lights']))
      $onoff = "OFF";

    $sendArray['state'] = $onoff;
    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));

}


if(isset($_REQUEST['btnChgBrightness']))
{

	if(!empty($_POST['Brightness']))
	{

		$_SESSION["ChgBrightness"] = $_POST['ChgBrightness'];
		$sendArray['chgBrightness'] = $_POST['ChgBrightness'];

		sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
	}

}




if(isset($_REQUEST['ClearQueue']))
{

    $sendArray['clearQueue'] = 1;
    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));

}




if(isset($_REQUEST['LightShow']))
{

    $r = 5;
    $g = 3;
    $b = 12;


    $sendArray['brightness'] = $_SESSION["Brightness"];

    if(!empty($_POST['ShowName']))
    {
        if(isset($_POST['color_1']))
        {
            if($hex != '#000000')
            {
                $hex = $_POST['color_1'];
                list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");


                $color["r"] = $r;
                $color["g"] = $g;
                $color["b"] = $b;
                $sendColors['color1'] = $color;
            }

        }

        if(isset($_POST['color_2']))
        {
            $hex = $_POST['color_2'];
            if($hex != '#000000')
            {
                list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");


                $color["r"] = $r;
                $color["g"] = $g;
                $color["b"] = $b;
                $sendColors['color2'] = $color;
            }

        }

        if(isset($_POST['color_3']))
        {
            $hex = $_POST['color_3'];
            if($hex != '#000000')
            {
                list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");


                $color["r"] = $r;
                $color["g"] = $g;
                $color["b"] = $b;
                $sendColors['color3'] = $color;
            }

        }

       if(isset($_POST['color_4']))
       {
           $hex = $_POST['color_4'];
           if($hex != '#000000')
           {
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
               $color["r"] = $r;
               $color["g"] = $g;
               $color["b"] = $b;
               $sendColors['color4'] = $color;
            }

       }

        if(!empty($_POST['ShowName']))
            $sendArray['show'] =  $_POST['ShowName'];

        if(!empty($_POST['Delay']))
            $sendArray['delay'] = $_SESSION["Delay"];

        if(!empty($_POST['ShowName']))
            $sendArray['minutes'] = $_SESSION["Minutes"];

        if(count($sendColors) > 0)
           $sendArray['colors'] = $sendColors;
        
        if (!empty($_POST['clearStart']))
            $sendArray['clearStart'] = 1;

        if (!empty($_POST['clearFinish']))
            $sendArray['clearFinish'] = 1;
            
        if (!empty($_POST['gammaCorrection']))
			$sendArray['gammaCorrection'] = 1;

        if (!empty($_POST['powerOn']))
           $sendArray['powerOn'] = "OFF";
           
        if (!empty($_POST['ColorEvery']))
           $sendArray['colorEvery'] = $_SESSION["ColorEvery"];

    }
    //$_SESSION["Color1"] = $g << 16 | $r << 8 | $b;

    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));

}


if(isset($_REQUEST['btnSavelist']))
{
	if(!empty($_POST['PlaylistName']))
	{
		$sendArray['savePlaylist'] = 1;
		$sendArray['playlistName'] = $_POST['PlaylistName'];
		$sendArray['UserID'] = $_SESSION['UserID'];
		$displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
		$query_data = mysqli_fetch_array($displayStrip);

		sendMQTT($query_data['serverHostName'], json_encode($sendArray));
	}

}

if(isset($_REQUEST['btnPlaylist']))
{

	if(!empty($_POST['Playlist']))
	{
		$sendArray['playPlaylist'] = 1;
		$sendArray['playlistName'] = $_POST['Playlist'];
		$sendArray['UserID'] = $_SESSION['UserID'];
		$displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
		$query_data = mysqli_fetch_array($displayStrip);

		sendMQTT($query_data['serverHostName'], json_encode($sendArray));
	}
}

if(isset($_REQUEST['btnDeletePlaylist']))
{

	if(!empty($_POST['Playlist']))
	{
		$sendArray['deletePlaylist'] = 1;
		$sendArray['playlistName'] = $_POST['Playlist'];
		$sendArray['UserID'] = $_SESSION['UserID'];
		sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
	}

}


$lightSystemsoption = '';
$lightSystemsScript = '';
    $results = mysqli_query($conn,"SELECT ID, systemName, stripHeight, stripWidth, brightness FROM lightSystems WHERE enabled = 1 and userId =" . $_SESSION['UserID'] . " or userId =1");
if(mysqli_num_rows($results) > 0)
{

    $lightSystemsScript .= "let systemsMap = new Map();\r\n";
    while($row = mysqli_fetch_array($results))
    {
        $lightSystemsScript .= "var system = new Object(); \r";

        $lightSystemsScript .= "    system.id = " . $row['ID'] .";\r";
        $lightSystemsScript .= "    system.systemName = '" . $row['systemName'] ."';\r";
        $lightSystemsScript .= "    system.stripHeight = " . $row['stripHeight'] .";\r";
        $lightSystemsScript .= "    system.stripWidth = " . $row['stripWidth'] .";\r";
        $lightSystemsScript .= "    system.brightness = " . $row['brightness'] .";\r";

        $lightSystemsScript .= "systemsMap.set(" . $row['ID'] . ", system);\r";

        if($row['ID'] == $_SESSION["LightSystemID"] )
            $lightSystemsoption .="<option value = '".$row['ID']."' selected='selected'>".$row['systemName']."</option>";
        else
            $lightSystemsoption .="<option value = '".$row['ID']."'>".$row['systemName']."</option>";

    }
}




$playlistoption = '';
$results = mysqli_query($conn,"SELECT ID, playlistName FROM userPlaylist where userId =" . $_SESSION['UserID'] . " or userId =1");
if(mysqli_num_rows($results) > 0)
{
    while($row = mysqli_fetch_array($results))
      $playlistoption .="<option value = '".$row['ID']."'>".$row['playlistName']."</option>";

}

$conn->close();

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Name Page</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">
</head>


<body onload="initShowSystem();">
<?php include("Nav.php");  ?>


<script>

<?php echo $lightSystemsScript;?>

	function initShowSystem()
	{
		setSystemSettings();
		setShowSettings();
	}

    function setSystemSettings()
    {
        var systemNameId = document.getElementById("SystemNameId");
        var widthId  = document.getElementById("WidthId");
        var widthOutput = document.getElementById("WidthValue");
        var chgBrightnessId = document.getElementById("ChgBrightnessId");

        var index = parseInt(systemNameId.value);
        var numLeds = systemsMap.get(index).stripWidth * systemsMap.get(index).stripHeight;

        if(widthId.value > numLeds)
        {
            widthId.setAttribute('value', numLeds);
            widthId.value = numLeds;
            widthOutput.innerHTML = numLeds;

        }

        widthId.setAttribute('max', numLeds);
        widthId.max = numLeds;

		chgBrightnessId.value = systemsMap.get(index).brightness;
    }


</script>

<div class="clearfix">
<div class="column">
	

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<img src="System-Control.png" alt="System Control" width="100%" />
    <p><label for="SystemName">System Name:</label><br />
    <select id="SystemNameId" name="SystemName" onChange="setSystemSettings();">
        <?php echo $lightSystemsoption;?>
        </select>
    </p>
    <p><label for="ChgBrightness">Change Brightness:</label>
        <input type="number" value="<?php echo $_SESSION["ChgBrightness"];?>" id="ChgBrightnessId" name="ChgBrightness" min="1" max="255">
        <button type="submit" name="btnChgBrightness">Change</button>
    </p>
        <label for="On">On</label>
    <input type="checkbox" name="lights"  value="ON" checked><button type="submit" name="Power">Power</button>


    </div>





<?php include_once('showDesigner.php'); ?>

    <div class="column">
        <div class="ColumnStyles">

<img src="Images/Playlist-Manager.png" alt="Playlist Manager" width="100%" />



<script>

    function setPlaylistName()
    {
        var playlistName = document.getElementById("PlayListNameId");
        var playListId = document.getElementById("PlayListId");
        var selectedText = playListId.options[playListId.selectedIndex].text;
        playlistName.value = selectedText;


    }
</script>

        <select id="PlayListId"  name="Playlist" onChange="setPlaylistName();">
        <?php echo $playlistoption;?>
        </select>
        <p>
            <label>New Playlist Name*</label> <br />
            <input type="text" id="PlayListNameId" name="PlaylistName" max="50" placeholder="Enter a playlist name (50 characters)" style="width: 100%">
            </p>

        <p>
        <button type="submit" name="btnSavelist" style="margin: 3px;">Save Shows</button>
        <button type="submit" name="btnDeletePlaylist" style="margin: 3px;">Delete Show</button>
        <button type="submit" name="btnPlaylist">Play</button>
        </p>


    </form>
    </div>
    </div>
	</div>
	<?php include('Footer.php'); ?>
	
	
	
</body>
</html>

