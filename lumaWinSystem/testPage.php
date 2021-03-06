<?php

include('commonFunctions.php');


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

	if(!empty($_POST['ChgBrightness']))
	{

		$_SESSION['ChgBrightness'] = $_POST['ChgBrightness'];
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
            $sendArray['delay'] = $_SESSION['Delay'];

        if(!empty($_POST['Minutes']))
            $sendArray['minutes'] = $_SESSION['Minutes'];

        if(count($sendColors) > 0)
           $sendArray['colors'] = $sendColors;
        
        if (!empty($_POST['clearStart']))
            $sendArray['clearStart'] = 1;

        if (!empty($_POST['Width']))
            $sendArray['width'] = $_POST['Width'];

        if (!empty($_POST['clearFinish']))
            $sendArray['clearFinish'] = 1;
            
        if (!empty($_POST['gammaCorrection']))
			$sendArray['gammaCorrection'] = 1;

        if (!empty($_POST['powerOn']))
           $sendArray['powerOn'] = "OFF";
           
        if (!empty($_POST['ColorEvery']))
           $sendArray['colorEvery'] = $_SESSION["ColorEvery"];
           
        if(!empty($_POST['hasText']))
			$sendArray['matrixText'] = $_POST['hasText'];
			
		if(!empty($_POST['matrixData']))
			$sendArray['pixles'] = json_decode($_POST['matrixData']);
		
        $sendArray['systemId'] = $_SESSION["LightSystemID"];

    }
    
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
<?php 
include('header.php'); 
?>


<body >
<?php include("nav.php");  ?>


<div class="clearfix">

<?php

include_once('commonFunctions.php');


$conn = getDatabaseConnection();


$results = mysqli_query($conn,"SELECT ID,showName,numColors,hasDelay,hasWidth, hasMinutes, colorEvery, isMatrix, hasText FROM lightShows WHERE enabled = 1 order by showOrder asc");
if(mysqli_num_rows($results) > 0)
{
    $_SESSION['lightShowsScript'] = "let showMap = new Map();\r";

    while($row = mysqli_fetch_array($results))
    {
        if($_SESSION["ShowName"] != $row['ID'])
			$lightShowsoption .="<option value = '".$row['ID']."'>".$row['showName']."</option>";
		else
			$lightShowsoption .="<option value = '".$row['ID']."' selected>".$row['showName']."</option>";


        $_SESSION['lightShowsScript'] .= "var show = new Object(); \r";

        $_SESSION['lightShowsScript'] .= "    show.id = " . $row['ID'] .";\r";
        $_SESSION['lightShowsScript'] .= "    show.showName = '" . $row['showName'] ."';\r";
        $_SESSION['lightShowsScript'] .= "    show.numColors = " . $row['numColors'] .";\r";
        $_SESSION['lightShowsScript'] .= "    show.hasDelay = " . $row['hasDelay'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.hasWidth = " . $row['hasWidth'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.hasMinutes = " . $row['hasMinutes'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.colorEvery = " . $row['colorEvery'] .";\r";
		$_SESSION['lightShowsScript'] .= "  show.isMatrix = " . $row['isMatrix'] .";\r";
		$_SESSION['lightShowsScript'] .= "  show.hasText = " . $row['hasText'] .";\r";

        $_SESSION['lightShowsScript'] .= "    showMap.set(" . $row['ID'] . ", show);\r";




    }
    
    
}



$systemResults = mysqli_query($conn,"SELECT * FROM lightSystems where userId =" . $_SESSION['UserID'] . " or userId = 1");
if(mysqli_num_rows($systemResults) > 0)
{
	$_SESSION['systemlistoption'] = '';
    $_SESSION['lightSystemsScript'] = "let systemsMap = new Map();\r\n";
    while($systemRow = mysqli_fetch_array($systemResults))
    {
			
		
		$_SESSION['lightSystemsScript'] .= "var system = new Object(); \r";

        $_SESSION['lightSystemsScript'] .= "    system.id = " . $systemRow['ID'] . ";\r";
        $_SESSION['lightSystemsScript'] .= "    system.systemName = '" . $systemRow['systemName'] . "';\r";
        $_SESSION['lightSystemsScript'] .= "    system.serverHostName = '" . $systemRow['serverHostName'] . "';\r";
        $_SESSION['lightSystemsScript'] .= "    system.enabled = " . $systemRow['enabled'] . ";\r";
        $_SESSION['lightSystemsScript'] .= "    system.userId = " . $systemRow['userId'] . ";\r";
        $_SESSION['lightSystemsScript'] .= "    system.twitchSupport = " . $systemRow['twitchSupport'] . ";\r";
		$_SESSION['lightSystemsScript'] .= "    system.mqttRetries = " . $systemRow['mqttRetries'] . ";\r";
		$_SESSION['lightSystemsScript'] .= "    system.mqttRetryDelay = " . $systemRow['mqttRetryDelay']   .";\r";
		$_SESSION['lightSystemsScript'] .= "    system.twitchMqttQueue = '" . $systemRow['twitchMqttQueue'] ."';\r";
		$_SESSION['lightSystemsScript'] .= "    system.channelsMap = new Map();\r";
		$_SESSION['lightSystemsScript'] .= "    system.featuresMap = new Map();\r";
		
		$channelResults = mysqli_query($conn,"SELECT * FROM lightSystemChannels where lightSystemId = " . $systemRow['ID'] . ";");
		
		if(mysqli_num_rows($channelResults) > 0)
		{
			
			while($channelRow = mysqli_fetch_array($channelResults))
			{
				$_SESSION['lightSystemsScript'] .= "var channel = new Object(); \r";
				$_SESSION['lightSystemsScript'] .= "    channel.channelId = " . $channelRow['channelId'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.stripType = " . $channelRow['stripType'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.stripColumns = " . $channelRow['stripColumns'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.stripRows = " . $channelRow['stripRows'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.dma = " . $channelRow['dma'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.gpio = " . $channelRow['gpio'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.brightness = " . $channelRow['brightness'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.gamma = " . $channelRow['gamma'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    channel.enabled = " . $channelRow['enabled'] .";\r";
				$_SESSION['lightSystemsScript'] .= "system.channelsMap.set(" . $channelRow['channelId'] . ", channel);\r";
				
			}
		}
		
		$featureResults = mysqli_query($conn,"SELECT * FROM lightSystemFeatures where lightSystemId = " . $systemRow['ID'] . ";");
		if(mysqli_num_rows($featureResults) > 0)
		{
			while($featureRow = mysqli_fetch_array($featureResults))
			{
				$_SESSION['lightSystemsScript'] .= "var lightFeature = new Object(); \r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.featureId = " . $featureRow['featureId'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.featureGpio = " . $featureRow['featureGpio'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.featurePlayList = " . $featureRow['featurePlayList'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.motionDelayOff = " . $featureRow['motionDelayOff'] .";\r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.timeFeatureStart = '" . $featureRow['timeFeatureStart'] ."';\r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.timeFeatureEnd = '" . $featureRow['timeFeatureEnd'] ."';\r";
				$_SESSION['lightSystemsScript'] .= "    lightFeature.luxThreshHold = " . $featureRow['luxThreshHold'] .";\r";
				$_SESSION['lightSystemsScript'] .= "system.featuresMap.set(" . $featureRow['featureId']  . ", lightFeature);\r";
				
			}
		}
		

	
        $_SESSION['lightSystemsScript'] .= "systemsMap.set(" . $systemRow['ID'] . ", system);\r";

        if($systemRow['ID'] == $_SESSION["LightSystemID"] )
            $_SESSION['systemlistoption'] .="<option value = '".$systemRow['ID']."' selected>". $systemRow['systemName']."</option>";
        else
            $_SESSION['systemlistoption'] .="<option value = '".$systemRow['ID']."'>". $systemRow['systemName']."</option>";

    }
    
   
}



$conn->close();

?>


<style>
.pixel {
  height: 15px;
  width: 15px;
  background-color: #000000;
  border-radius: 50%;
  display: inline-block;
  
}

.pixel:hover {
background-color: red;

}

</style>


<div class="column seventy-five">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		
		<center><img src="Images/Show-Designer.png" alt="Show Designer"/></center>

    		<center>
		<p>
			
			<label for="SystemName">System Name:</label>
			<select id="SystemNameId" name="SystemName"style="width: 25%" onChange="setSystemSettings();">
			<?php echo $_SESSION['systemlistoption'];?></select>
			
			<label for="ShowName">Show name</label>
			<select id="ShowNameId" name="ShowName" onChange="setShowSettings(true);" style="width: 25%"><?php echo $lightShowsoption;?></select>
			
			
		</p>
    
			</center>
		
<div class="clearfix">
	<div style="width: 50%; float: left;">
		<table>
    		<tr>
				<td><label for="colors">Colors:</label></td>
			
				<td><input type="color"  Name="color_1" id="Color1" value ="#ff6500">
        		<input type="color" Name="color_2" id="Color2" value="#906bfa">
        		<input type="color" Name="color_3" id="Color3" value="#2c2367">
        		<input type="color" Name="color_4" id="Color4" value="#ad5e8c"></td>
			</tr>

        	<tr>
				<td><label for="Width">Width:</label></td>
				<td><input type="number" id="WidthId" name="Width" min="1" max="300" value="<?php echo $_SESSION["Width"];?>"></td>
			</tr>

			<tr>
				<td><label for="ColorEvery">X led:</label></td>
				<td><input type="number" id="ColorEveryId" name="ColorEvery" min="1" max="300" value="<?php echo $_SESSION["ColorEvery"];?>"></td>
			</tr>
		</table>
	</div>


<div style="width: 50%; float: left;">
	<table>
        <tr>
			<td><label for="Delay">Delay:</label></td>
			<td><input type="number" id="DelayId" name="Delay" min="1" max="100000" value="<?php echo $_SESSION["Delay"];?>"></td>
		</tr>



    	<tr>
			<td><label for="NumMinutes">Minutes:</label></td>
			<td><input type="number" id="NumMinutesId" name="Minutes" min="1" value="<?php echo $_SESSION["Minutes"];?>"></td>
		</tr>


		<tr>
			<td><label for="Brightness">Brightness:</label></td>
    		<td><input type="number" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="1" max="255"></td>
		</tr>
	</table>
</div>
	</div>

        <center>
			
			<p><input type="text" name="hasText" id="hasText" placeholder="Scrolling text" /></p>
			
			
			<p><label for="On1" style="font-size: 14px">Clear start</label>
    <input type="checkbox" name="clearStart" id="clearStart">
			<label for="On2" style="font-size: 14px">Clear finish</label>
    <input type="checkbox" name="clearFinish" id="clearFinish">
    
		<label for="On3" style="font-size: 14px">Gamma correction</label>
		<input type="checkbox" name="gammaCorrection" id="gammaCorrection" checked></p>
		</center>
		
		<?php

    if($_SESSION["DesignerEditMode"]  == 0)
    {
        echo '<p>
			<button type="submit" onClick="storeMatrix()" name="LightShow">Send Show</button>
        </p>';
    }
    else
    {
        echo '<p style="margin-bottom: -14px;">
		<button onClick="addShowSettings();return false" name="AddShow">Add Show</button>
		</p>
		
		<p>
		<button onClick="saveShowSettings();return false" name="SaveShow">Save Changes</button>
			<button onClick="removeShowSettings();return false" name="RemoveShow">Remove Show</button>
			
		</p>';

			
    }
?>
		
	</div>
		
 <div id="divArt" class="ColumnStyles fifty" hidden>
		<div style="text-align:center">
		  <h1>Matrix Art!</h1>
			<label>Base Color</label>
			<p>
				<select id="brushSize" name="brushSize">
				  <option value="1" selected>S</option>
				  <option value="2">M</option>
				  <option value="3">L</option>
				</select>
			</p>
			
			
			<input type="color" id="baseColor" onchange="setMatrixColors()" name="baseColor" value="#000000"/>
			
			
			
			<label>Color Select</label>
			<input type="color" id="colorSelect" name="colorSelect" value="#34ebde" />
			<input type="text" id="matrixData" name="matrixData" hidden />
			<div oncontextmenu="return false;" id="divMatrix" name="divMatrix" style ="margin-top: 15px;">
		<p><?php echo $matrixHTML; ?></P>
			</div>
		</div>
		
    </div>
</div>

		
<script>
	
	

<?php echo $_SESSION['lightShowsScript'];?>
<?php echo $_SESSION['lightSystemsScript'];?>

let mode = 0;
let oldx = 0;
let oldy = 0;
let direction = 1;

const divMatrix = document.getElementById('divMatrix');

divMatrix.addEventListener('mouseleave', e => {
  
  	mode = 0;
	
});

divMatrix.addEventListener('mousedown', e => {
	e.stopPropagation();
    e.preventDefault();

	
	
	switch(e.which)
	{
		case 1:
			mode = 1;
		break;

		case 2:
			mode = 0;
			captureColor();	
		break;

		case 3:
			mode = 2;
		
		break;
	}
  
});

    
divMatrix.addEventListener('mousemove', e => 
{
	 e.stopPropagation();
     e.preventDefault();
     if (e.pageX > oldx && e.pageY == oldy) 
     {
             direction = 3;
     }
     else if (e.pageX == oldx && e.pageY > oldy) 
     {
             direction = 2;
     }
     else if (e.pageX == oldx && e.pageY < oldy) 
     {
             direction = 1;
     }
     else if (e.pageX < oldx && e.pageY == oldy) 
     {
             direction = 4;
     }
  
	oldx = e.pageX;
    oldy = e.pageY;
	
	if(mode == 1 || mode == 2)
		setColor();
	
	
});


divMatrix.addEventListener('mouseup', e => 
{
    e.stopPropagation();
    e.preventDefault();
	mode = 0;
	
});


function setColor()
{	
	var brushSize = document.getElementById("brushSize");
	var systemNameId = document.getElementById("SystemNameId");
	var system = systemsMap.get(parseInt(systemNameId.value));
	var index = 0;
	
    var pos = parseInt(this.event.target.id);
    var maxPixels = system.channelsMap.get(1).stripRows * system.channelsMap.get(1).stripColumns;
	
	var pixel = document.getElementById(this.event.target.id);
	if(pixel.id != "divMatrix")
	{
		for(var count = 0; count < brushSize.value; count++)
		{
			if(direction == 3 || direction == 4)
				index = pos + (system.channelsMap.get(1).stripColumns * count);
			else
				index = pos + count;
				
			if(index <= maxPixels)
			{
				
				pixel = document.getElementById(index);
				
				if(mode == 1)
				{
					var color = document.getElementById('colorSelect');	
				}
				else if(mode ==  2)
				{
					var color = document.getElementById('baseColor');	
				}
		
				pixel.style.background = color.value;
			}
		}
		
		
		
	}
}


function captureColor()
{
	var pixel = document.getElementById(this.event.target.id);
	if(pixel.id != "divMatrix")
	{
		var color = document.getElementById('colorSelect');
		color.value = rgbToWebHex(pixel.style.backgroundColor);
		
	}
	
}

function setToBaseColor()
{	
	isDrawing = 0;
	var pixel = document.getElementById(this.event.target.id);
	
	if(pixel.id == "divMatrix") return;
	var color = document.getElementById('baseColor');
	pixel.style.background = color.value;

}

function setMatrixColors()
{
	var pixel;
	var systemNameId = document.getElementById("SystemNameId");
	var baseColor = document.getElementById("baseColor");
    var index = parseInt(systemNameId.value);
    var system = systemsMap.get(index);
    var ledNum = 0;
    for(var row = 0; row < system.channelsMap.get(1).stripRows; row++)
    {
		for(var column = 0; column < system.channelsMap.get(1).stripColumns; column++)
		{
			
			ledNum += 1;
			pixel = document.getElementById(ledNum);
			pixel.style.background = baseColor.value;
				
		}
			
		
	}
	
    
}

function getColorHex(color){
    var hex;
    if(color.indexOf('#')>-1){
        //for IE
        hex = color;
    } else {
        var rgb = color.match(/\d+/g);
        hex = '#'+ ('0' + parseInt(rgb[0], 10).toString(16)).slice(-2) + ('0' + parseInt(rgb[1], 10).toString(16)).slice(-2) + ('0' + parseInt(rgb[2], 10).toString(16)).slice(-2);
    }
    return hex;
}


function componentToHex(c) {
  var hex = c.toString(16);
  return hex.length == 1 ? "0" + hex : hex;
}


function componentFromStr(numStr, percent) {
    var num = Math.max(0, parseInt(numStr, 10));
    return percent ?
        Math.floor(255 * Math.min(100, num) / 100) : Math.min(255, num);
}


function rgbToWebHex(rgb) 
{
	var rgbRegex = /^rgb\(\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*\)$/;
    var result, r, g, b, hex = "";
    if ( (result = rgbRegex.exec(rgb)) ) 
    {
        r = componentFromStr(result[1], result[2]);
        g = componentFromStr(result[3], result[4]);
        b = componentFromStr(result[5], result[6]);
	}
        
  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}


function rgbToHex(rgb) {
	
    var rgbRegex = /^rgb\(\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*\)$/;
    var result, r, g, b, hex = "";
    if ( (result = rgbRegex.exec(rgb)) ) {
			
		
        r = componentFromStr(result[1], result[2]);
        g = componentFromStr(result[3], result[4]);
        b = componentFromStr(result[5], result[6]);
		
        hex = "0x" + (0x1000000 + (r << 16) + (g << 8) + b).toString(16).slice(1);
        
    }
    return hex;
}

function storeMatrix()
{
	
    
	var pixel;
	var systemNameId = document.getElementById("SystemNameId");
	var matrixData = document.getElementById("matrixData");
	
    var index = parseInt(systemNameId.value);

    var system = systemsMap.get(index);
    var currentPos = 0;
    
    var matrixJson = '{';
    
	for(var row = 0; row < system.channelsMap.get(1).stripRows; row++)
    {
		for(var column = 0; column < system.channelsMap.get(1).stripColumns; column++)
		{
			
			currentPos += 1;
			pixel = document.getElementById(currentPos);
			matrixJson += '"' + currentPos + '":{"r":' + row + ',"c":' + column + ',"co":"' + rgbToHex(pixel.style.backgroundColor) + '"}';
			
			if(column != (system.channelsMap.get(1).stripColumns - 1))
				matrixJson += ",";
				
		}
			
		
		if(row != system.channelsMap.get(1).stripRows - 1)
			matrixJson += ",";
		
	}
	
	matrixJson += '}';
	matrixData.value = matrixJson;
	
		
}


function setShowSettings(arg1)
    {
		var showNameId = document.getElementById("ShowNameId");
		var systemNameId = document.getElementById("SystemNameId");
		
		var system = systemsMap.get(parseInt(systemNameId.value));
		var index = parseInt(showNameId.value);		
		
		
        var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");
		var hasText = document.getElementById("hasText");
		
		var divArt = document.getElementById("divArt");
		var baseColor = document.getElementById("baseColor");
		
		
        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		hasText.setAttribute('disabled', true);
		divArt.setAttribute('hidden', true);
		divArt.hidden = true;
		
		if(arg1 ==  true)
		{
			if( (system.channelsMap.get(1).stripRows > 1 && showMap.get(index).isMatrix) && showMap.get(index).hasText === 0)
			{
		
				
				var matrixHTML = "";
				var divMatrix = document.getElementById("divMatrix");
				
				divArt.setAttribute('hidden', false);
				divArt.hidden = false;
				
				
				var currentPos = 0;
				
		
				for(var ledRow = 0; ledRow < system.channelsMap.get(1).stripRows; ledRow++)
				{
					
					for(var ledColumn = 0; ledColumn < system.channelsMap.get(1).stripColumns; ledColumn++)
					{
						style="background-color:grey;"
						currentPos += 1;
						matrixHTML += "<span id='" + currentPos  + "' class='pixel' style='background-color:" + baseColor.value + "' ></span>";		
					}
					matrixHTML += "<br>";

				}
			
			
				divMatrix.innerHTML = matrixHTML;
				
				
			}
		}
		else
		{
			if( (system.channelsMap.get(1).stripRows > 1 && showMap.get(index).isMatrix) && showMap.get(index).hasText === 0)
			{
				divArt.setAttribute('hidden', false);
				divArt.hidden = false;
			}
		}
		
       
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
		
		if( (system.channelsMap.get(1).stripRows > 1 && showMap.get(index).isMatrix) && showMap.get(index).hasText == 1)
		{
		
			hasText.setAttribute('disabled', false);
			hasText.disabled = false;


		}
        
    }
    
function setSystemSettings()
    {
		var widthId  = document.getElementById("WidthId");
        var widthOutput = document.getElementById("WidthValue");
        var chgBrightnessId = document.getElementById("ChgBrightnessId");
        var brightness = document.getElementById("Brightness");

		var systemNameId = document.getElementById("SystemNameId");
        var index = parseInt(systemNameId.value);
        var system = systemsMap.get(index);
        
        var numLeds = system.channelsMap.get(1).stripRows * system.channelsMap.get(1).stripColumns;
        if(widthId.value > numLeds)
        {
            widthId.setAttribute('value', numLeds);
            widthId.value = numLeds;
            widthOutput.innerHTML = numLeds;

        }

        widthId.setAttribute('max', numLeds);
        widthId.max = numLeds;

		chgBrightnessId.value = system.channelsMap.get(1).brightness;
		brightness.value = system.channelsMap.get(1).brightness;
		//setShowSettings();

    }

setShowSettings(true);

		
</script>    

	<div class="column twenty-five">
		<div class="ColumnStyles">
		<center><img src="System-Control.png" id="systemControlPic" alt="System Control"/></center>
    <p><label for="ChgBrightness">Change Brightness:</label>
        <input type="number" value="<?php echo $_SESSION["ChgBrightness"];?>" id="ChgBrightnessId" name="ChgBrightness" min="1" max="255">
        <button type="submit" name="btnChgBrightness">Change</button>
    	

    </p>
        <label for="On">On</label>
		<input type="checkbox" name="lights"  value="ON" checked>
		<button type="submit" name="Power">Power</button>
		<button type="submit" name="ClearQueue">Clear Queue</button>
		<p>
			


	
	<script>

	
	function setPlaylistName()
	{
		var playlistName = document.getElementById("PlayListNameId");
		var playListId = document.getElementById("PlayListId");
		var selectedText = playListId.options[playListId.selectedIndex].text;
		playlistName.value = selectedText;


	}
	</script>
	<label>Playlist</label>
	<select id="PlayListId"  name="Playlist" onChange="setPlaylistName();"><?php echo $playlistoption;?></select>
	<p><button type="submit" name="btnPlaylist">Play</button>
	<button onclick="location.href='editShows.php'; return false" name="btnEditist">Editor</button></p>
			
		</p>
		</form>		
	</div>
		</div>
	
	
 
    
    
	<?php include('footer.php'); ?>
	
	
	
</body>
</html>
