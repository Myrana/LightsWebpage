<?php

include_once('commonFunctions.php');


$conn = getDatabaseConnection();


$results = mysqli_query($conn,"SELECT * FROM lightShows WHERE enabled = 1 order by showOrder asc");
if(mysqli_num_rows($results) > 0)
{
    $_SESSION['lightShowsScript'] = "let showMap = new Map();\r";
	$lightShowsoption = "";
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
		$_SESSION['lightShowsScript'] .= "  show.matrixType = " . $row['matrixType'] .";\r";
		$_SESSION['lightShowsScript'] .= "  show.matrixShape = " . $row['matrixShape'] .";\r";
		
        $_SESSION['lightShowsScript'] .= "    showMap.set(" . $row['ID'] . ", show);\r";


    }
    
    
}



$systemResults = mysqli_query($conn,"SELECT * FROM lightSystems where userId =" . $_SESSION['UserID'] . " or userId = 1 and enabled = 1");
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
   
	$postionOptions = "";
    $postionsResults = mysqli_query($conn,"SELECT * FROM lPosition");
	if(mysqli_num_rows($postionsResults) > 0)
	{
		while($positionRow = mysqli_fetch_array($postionsResults))
		{
			if($positionRow['ID'] == $_SESSION["position"] )
				$postionOptions .= '<option selected value="' . $positionRow['ID'] . '">' . $positionRow['description'] . '</option>';
			else
				$postionOptions .= '<option value="' . $positionRow['ID'] . '">' . $positionRow['description'] . '</option>';
			
		}
	}
		 

	$directionOptions = "";
    $directionResults = mysqli_query($conn,"SELECT * FROM lDirection");
	if(mysqli_num_rows($postionsResults) > 0)
	{
		while($directionRow = mysqli_fetch_array($directionResults))
		{
			if($directionRow['ID'] == $_SESSION["direction"] )
				$directionOptions .= '<option selected value="' . $directionRow['ID'] . '">' . $directionRow['description'] . '</option>';
			else
				$directionOptions .= '<option value="' . $directionRow['ID'] . '">' . $directionRow['description'] . '</option>';
			
		}
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


<div class="column thirty-seven">
    <div class="ColumnStyles">
		
		<center><img src="Images/Show-Designer.png" alt="Show Designer"/></center>

    		<center>
				<label for="channelId">Channel</label>
				<select id="ChannelId" name="ChannelId"style="width: 25%">
				</select>
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
			
			<p>
				<select name="position" id="position" style="width: 50%">
					<?php echo $postionOptions;?>
				</select>
				<input type="text" name="hasText" id="hasText" placeholder="Scrolling text" style="width: 50%" />	
			</p>
			
			<input type="file" name="uploadArt" id="updateArt" accept="image/jpeg, image/png" />
			
			
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
    elseif($_SESSION["DesignerEditMode"]  == 1)
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
		</div>
<div class="column thirty-seven" id="matrixDesigner">		
	 <div id="divArt" class="ColumnStyles" hidden>
		<div style="text-align: center">
		  <h1>Matrix Designer!</h1>
			<p>
				
				<?php if($_SESSION["DesignerEditMode"]  == 0)
					echo '<p><label>Save art</label><input type="checkbox" name="saveArt" id="saveArt"><input type="text" name="saveArtName" id="saveArtName" placeholder="Art Name" style="width: 85%"></p>';
				?>
				<select id="brushSize" name="brushSize" style="width: 25%">
				  <option value="1" selected>single width</option>
				  <option value="2">double width</option>
				  <option value="3">triple width</option>
				</select>
			</p>
			
			<label>Base Color</label>
			<input type="color" id="baseColor" onchange="setMatrixColors()" name="baseColor" value="#000000"/>
			<label>Color Select</label>
			<input type="color" id="colorSelect" name="colorSelect" value="#34ebde" />
			<input type="text" id="matrixData" name="matrixData" hidden />
			<div oncontextmenu="return false;" id="divMatrix" name="divMatrix" style ="margin-top: 15px;">
		<p><?php echo $matrixHTML; ?></P>
			</div>
		</div>
		
    </div>

	<div id="divShapes" name="divShapes" class="ColumnStyles">
		<div style="text-align: center">
		
			<h1>Matrix Designer!</h1>
		<center>	
			<table>
				<tr>
					<td><label>Starting Row</label></td>
					<td><input type="number" id="startRow" name="startRow" value="<?php echo $_SESSION["startRow"];?>" /></td>
				</tr>
				
				<tr>
					<td><label>Starting Column</label></td>
					<td><input type="number" id="startColumn" name="startColumn" value="<?php echo $_SESSION["startColumn"];?>" /></td>
				
				</tr>
				
				<tr>
					<td><label>Radius</label></td>
					<td><input type="number" id="radius" name="radius" value="<?php echo $_SESSION["radius"];?>" /></td>
				
				</tr>
				
				<tr>
					<td><label>Length</label></td>
					<td><input type="number" id="length" name="length" value="<?php echo $_SESSION["length"];?>" /></td>
				
				</tr>
				
				<tr>
					<td><label>Height</label></td>
					<td><input type="number" id="height" name="height" value="<?php echo $_SESSION["height"];?>" /></td>
				
				</tr>

				<tr>
					<td><label>Triangle Direction</label></td>
					<td><select name="direction" id="direction">
					<?php echo $directionOptions;?>
					</select></td>
									
				</tr>

				
				<tr>
				<td><label>Fill</label></td>
				<?php if($_SESSION["fill"] == 0)
						echo '<td><input type="checkbox" id="fill" name="fill" /></td>';
					 else
					    echo '<td><input type="checkbox" id="fill" name="fill" checked/></td>';
				?>
				</tr>
			
			</table>
		</center>
			
		</div>
	
	</div>
		</div>
			
			<!-- Clear fix end div -->
			</div>

		<?php include('footer.php'); ?>

<script>
	
	

<?php echo $_SESSION['lightShowsScript'];?>
<?php echo $_SESSION['lightSystemsScript'];?>
<?php echo $_SESSION['userArtScript'];?>



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
		var position = document.getElementById("position");
		//var upload = document.getElementById("uploadArt");
		var startRow = document.getElementById("startRow");
		var startColumn = document.getElementById("startColumn");
		var radius = document.getElementById("radius");
		var len = document.getElementById("length");
		var height = document.getElementById("height");
		
		var divArt = document.getElementById("divArt");
		var divShapes = document.getElementById("divShapes");
		
		var baseColor = document.getElementById("baseColor");
		var direction = document.getElementById("direction");

		
		
        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		hasText.setAttribute('disabled', true);
		position.setAttribute('disabled', true);
		startRow.setAttribute('disabled', true);
		startColumn.setAttribute('disabled', true);
		radius.setAttribute('disabled', true);
		len.setAttribute('disabled', true);
		height.setAttribute('disabled', true);
		direction.setAttribute('disabled', true);
		//upload.setAttribute('disabled', true);
		
		divArt.setAttribute('hidden', true);
		divArt.hidden = true;
		divShapes.setAttribute('hidden', true);
		divShapes.hidden = true;
		
		
		
		if(arg1 ==  true)
		{
			if( system.channelsMap.get(1).stripRows > 1 && showMap.get(index).matrixType == 1)
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
			if( system.channelsMap.get(1).stripRows > 1 && showMap.get(index).matrixType == 1)
			{
				divArt.setAttribute('hidden', false);
				divArt.hidden = false;
			}
			
		}
		
			
		if( system.channelsMap.get(1).stripRows > 1 && showMap.get(index).matrixType == 2) 
		{
		
			hasText.setAttribute('disabled', false);
			hasText.disabled = false;
			position.setAttribute('disabled', false);
			position.disabled = false;


		}
              
		if( system.channelsMap.get(1).stripRows > 1 && showMap.get(index).matrixType == 4)
		{
		
			divShapes.setAttribute('hidden', false);
			divShapes.hidden = false;
			switch(showMap.get(index).matrixShape)
			{
				case 1:
					startRow.disabled = false;
					startColumn.disabled  = false;
					radius.disabled = false;
					
				break;
				
				case 2:
				case 3:
					startRow.disabled = false;
					startColumn.disabled  = false;
					len.disabled = false;
					height.disabled = false;
				break;
					
				case 4:
					startRow.disabled = false;
					startColumn.disabled  = false;
					len.disabled = false;
					direction.disabled = false;
				break;
				
				
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
	
        
    }
    

function setSystemSettings()
{
	var widthId  = document.getElementById("WidthId");
	var widthOutput = document.getElementById("WidthValue");
	var chgBrightnessId = document.getElementById("ChgBrightnessId");
	var brightness = document.getElementById("Brightness");

	var systemNameId = document.getElementById("SystemNameId");
	var channelId = document.getElementById("ChannelId");
	
	var index = parseInt(systemNameId.value);
	var system = systemsMap.get(index);
	channelId.options.length = 0;
	for (let [key, value] of system.channelsMap)
	{
		if(value.enabled)
		{
			var option = document.createElement("option");
			option.text = key;
			option.value = key;
			
			channelId.add(option); 
		}
		
	}
	
	
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

setSystemSettings();
setShowSettings(true);

		
</script>    

		
