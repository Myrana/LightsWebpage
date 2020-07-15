<?php

include('commonFunctions.php');

$_SESSION["Brightness"] = 127;
$_SESSION["LightSystemID"] = -1;
$_SESSION["Delay"] = 10;
$_SESSION["Minutes"] = 1;
$_SESSION["Width"] = 1;
$_SESSION["ColorEvery"] = 2;
$_SESSION["ShowName"] = -1;
$_SESSION["ChgBrightness"] = 20;
$_SESSION["DesignerEditMode"] = 0;

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: registration.php");
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

        if(!empty($_POST['Minutes']))
            $sendArray['minutes'] = $_SESSION["Minutes"];

        if(count($sendColors) > 0)
           $sendArray['colors'] = $sendColors;
        
        if (!empty($_POST['clearStart']))
            $sendArray['clearStart'] = 1;

        if (!empty($_POST['width']))
            $sendArray['width'] = $_POST['width'];

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



if(isset($_REQUEST['btnPlaylist']))
{

	if(!empty($_POST['Playlist']))
	{
		$sendArray['playPlaylist'] = 1;
		$sendArray['playlistName'] = intval($_POST['Playlist']);
		$sendArray['UserID'] = intval($_SESSION['UserID']);
		$displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
		$query_data = mysqli_fetch_array($displayStrip);
		sendMQTT($query_data['serverHostName'], json_encode($sendArray));
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



?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Name Page</title>
<script src="//kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">
</head>


<body onload="initShowSystem();">
<?php include("nav.php");  ?>


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




<?php
$lightShowsoption = '';
$_SESSION['lightShowsScript'] = '';
$results = mysqli_query($conn,"SELECT ID,showName,numColors,hasDelay,hasWidth, hasMinutes, colorEvery FROM lightShows WHERE enabled = 1 order by showOrder asc");
if(mysqli_num_rows($results) > 0)
{
    $_SESSION['lightShowsScript'] .= "let showMap = new Map();\r";

    while($row = mysqli_fetch_array($results))
    {
        
        $lightShowsoption .="<option value = '".$row['ID']."'>".$row['showName']."</option>";


        $_SESSION['lightShowsScript'] .= "var show = new Object(); \r";

        $_SESSION['lightShowsScript'] .= "    show.id = " . $row['ID'] .";\r";
        $_SESSION['lightShowsScript'] .= "    show.showName = '" . $row['showName'] ."';\r";
        $_SESSION['lightShowsScript'] .= "    show.numColors = " . $row['numColors'] .";\r";
        $_SESSION['lightShowsScript'] .= "    show.hasDelay = " . $row['hasDelay'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.hasWidth = " . $row['hasWidth'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.hasMinutes = " . $row['hasMinutes'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.colorEvery = " . $row['colorEvery'] .";\r";

        $_SESSION['lightShowsScript'] .= "    showMap.set(" . $row['ID'] . ", show);\r";




    }

}

$conn->close();

?>

<script>

    <?php echo $_SESSION['lightShowsScript'];?>


    function setShowSettings()
    {

        var showNameId = document.getElementById("ShowNameId");
        var index = parseInt(showNameId.value);


        var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");

        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		
        if(showMap.get(index).hasWidth == 1)
        {
            width.setAttribute('disabled', false);
            width.disabled = false;

        }

        if(showMap.get(index).hasMinutes == 1)
        {
            minutes.setAttribute('disabled', false);
            minutes.disabled = false;
        }

        if(showMap.get(index).hasDelay == 1)
        {
            delay.setAttribute('disabled', false);
            delay.disabled = false;
        }

        if(showMap.get(index).numColors >= 1)
        {
            color1.setAttribute('disabled', false);
            color1.disabled = false;
        }

        if(showMap.get(index).numColors >= 2)
        {

            color2.setAttribute('disabled', false);
            color2.disabled = false;
        }

        if(showMap.get(index).numColors >= 3)
        {
            color3.setAttribute('disabled', false);
            color3.disabled = false;
        }

        if(showMap.get(index).numColors == 4)
        {
            color4.setAttribute('disabled', false);
            color4.disabled = false;
        }
        
        
        if(showMap.get(index).colorEvery == 1)
        {
            colorEvery.setAttribute('disabled', false);
            colorEvery.disabled = false;
        }
        
    }
</script>


<div class="column">
    <div class="ColumnStyles">
		
		<img src="Images/Show-Designer.png" alt="Show Designer" width="100%" />

    <p><label for="ShowName">Show name</label><br /><select id="ShowNameId" name="ShowName" onChange="setShowSettings();">
    <?php echo $lightShowsoption;?></select>
</p>
		<div class="clearfix">
<div style="width: 50%; float: left;">
    <p><label for="colors">Colors:</label>
		<input type="color"  Name="color_1" id="Color1" value ="#ff6500">
        <input type="color" Name="color_2" id="Color2" value="#906bfa">
        <input type="color" Name="color_3" id="Color3" value="#2c2367">
        <input type="color" Name="color_4" id="Color4" value="#ad5e8c"></p>

        <p><label for="Width">Width:</label>
<input type="number" id="WidthId" name="Width" min="1" max="300" value="<?php echo $_SESSION["Width"];?>"></p>

<p><label for="ColorEvery">X led:</label>
<input type="number" id="ColorEveryId" name="ColorEvery" min="1" max="300" value="<?php echo $_SESSION["ColorEvery"];?>">
</p>
</div>


<div style="width: 50%; float: left;">
        <p><label for="Delay">Delay:</label>
<input type="number" id="DelayId" name="Delay" min="1" max="100000" value="<?php echo $_SESSION["Delay"];?>">
</p>



    <p><label for="NumMinutes">Minutes:</label>
<input type="number" id="NumMinutesId" name="Minutes" min="1" value="<?php echo $_SESSION["Minutes"];?>">
</p>




<p><label for="Brightness">Brightness:</label>
    <input type="number" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="1" max="255">
</p>
</div>
	</div>

        <p><label for="On1" style="font-size: 14px">Clear start</label>
    <input type="checkbox" name="clearStart" id="clearStart">
			<label for="On2" style="font-size: 14px">Clear finish</label>
    <input type="checkbox" name="clearFinish" id="clearFinish">
    
		<label for="On3" style="font-size: 14px">Gamma correction</label>
		<input type="checkbox" name="gammaCorrection" id="gammaCorrection"></p>
	
   
        <!--<p><label for="On" style="font-size: 14px">Power on</label>
    <input type="checkbox" name="powerOn" id="powerOn" value="OFF">
        </p>-->

<?php
    if($_SESSION["DesignerEditMode"]  == 0)
    {
        echo '<p>
			<button type="submit" name="LightShow">Send Show</button>
			<button type="submit" name="ClearQueue">Clear Queue</button>
        </p>';
    }
    else
    {
        echo '<p style="margin-bottom: -14px;">
		<button onClick="addShowSettings();return false" name="AddShow">Add Show</button>
		</p>
		
		<p>
		<button onClick="saveShowSettings();return false" name="SaveShow">Store Changes</button>
			<button onClick="removeShowSettings();return false" name="RemoveShow">Remove Show</button>
			
		</p>';

			
    }
		
		$conn->close();
    ?>

    </div>
    </div>


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

	<select id="PlayListId"  name="Playlist" onChange="setPlaylistName();"><?php echo $playlistoption;?></select>
	<p><button type="submit" name="btnPlaylist">Play</button>
	<button onclick="location.href='/editShows.php'; return false" name="btnEditist">Editor</button></p>
	


    </form>
    </div>
    </div>
	</div>
	<?php include('footer.php'); ?>
	
	
	
</body>
</html>

