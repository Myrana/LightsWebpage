<?php

include('commonFunctions.php');


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

}


$matrixHTML = "";
if(isset($_REQUEST['btnWorkMatrix']))
{
    $sql = "SELECT ID,systemName,serverHostName,userID,TwitchSupport,mqttRetries,mqttRetryDelay,twitchMqttQueue,lc.* FROM LedLightSystem.lightSystems as ls, LedLightSystem.lightSystemChannels as lc where ls.id = lc.lightSystemId and lc.channelId = 1 and lc.enabled = 1 and ls.enabled = 1 and ID = " . $_SESSION['LightSystemID'];
   
	$results = mysqli_query($conn, $sql);
	if(mysqli_num_rows($results) > 0)
	{
		$row = mysqli_fetch_array($results);
		$ledRows = $row['stripRows'];
		$ledColumns = $row['stripColumns'];
		if($ledRows == 1)
		{
			 $ledRows = $ledColumns;
			 $ledColumns = 1;
		}
		
		
		$currentPos = 0;
		
		for($ledRow = 0; $ledRow < $ledRows; $ledRow++)
		{
			if(($ledRow % 2) == 0)
				$currentPos += $ledColumns ;
					
			for($ledColumn = 0; $ledColumn < $ledColumns; $ledColumn++)
			{
				
					if(($ledRow % 2) != 0)
					{
						
						$currentPos += 1;
						$matrixHTML .= "<span id='" . $currentPos  . "'  onClick='setToBaseColor()' class='pixel'></span>";		
//						echo "Row: " . $ledRow . " col: " . $ledColumn . " Pos: " . $currentPos;
					}
					else
					{
					
						$pos = $currentPos - $ledColumn;
						$matrixHTML .= "<span id='" . $pos  . "' class='pixel'></span>";		
	//					echo "Row: " . $ledRow . " col: " . $ledColumn . " Pos: " . $pos;
						
					}

				
			}
			$matrixHTML .= "<br>";

		}
		
			
	}
	
	
}


if(isset($_REQUEST['btnDisplayArt']))
{
	if(!empty($_POST['matrixData']))
	{
		sendMQTT(getServerHostName($_SESSION["LightSystemID"]), $_POST['matrixData']);
	}	
}


$lightSystemsoption = '';
$lightSystemsScript = '';

$sql = "SELECT ID,systemName,serverHostName,userID,TwitchSupport,mqttRetries,mqttRetryDelay,twitchMqttQueue,lc.* FROM LedLightSystem.lightSystems as ls, LedLightSystem.lightSystemChannels as lc where ls.id = lc.lightSystemId and lc.channelId = 1 and lc.enabled = 1 and ls.enabled = 1 and (userId = " . $_SESSION['UserID'] . " or userId = 1)";

$results = mysqli_query($conn, $sql);
if(mysqli_num_rows($results) > 0)
{

    $lightSystemsScript .= "let systemsMap = new Map();\r\n";
    while($row = mysqli_fetch_array($results))
    {
        $lightSystemsScript .= "var system = new Object(); \r";

        $lightSystemsScript .= "    system.id = " . $row['ID'] .";\r";
        $lightSystemsScript .= "    system.systemName = '" . $row['systemName'] ."';\r";
        $lightSystemsScript .= "    system.stripRows = " . $row['stripRows'] .";\r";
        $lightSystemsScript .= "    system.stripColumns = " . $row['stripColumns'] .";\r";
        $lightSystemsScript .= "    system.brightness = " . $row['brightness'] .";\r";

        $lightSystemsScript .= "systemsMap.set(" . $row['ID'] . ", system);\r";

		
		//echo $row['stripRows'] * $row['stripColumns'];
        if($row['ID'] == $_SESSION["LightSystemID"] )
            $lightSystemsoption .="<option value = '".$row['ID']."' selected>".$row['systemName']."</option>";
        else
            $lightSystemsoption .="<option value = '".$row['ID']."'>".$row['systemName']."</option>";

    }
}

$conn->close();

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Matrix Art</title>
<script src="//kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">

<style>
.pixel {
  height: 25px;
  width: 25px;
  background-color: #1E90FF;
  border-radius: 50%;
  display: inline-block;
  
}

.pixel:hover {
background-color: red;
}

</style>

</head>

<?php 
	if(!empty($_REQUEST) && !isset($_REQUEST['btnDisplayArt']))
	{
		echo '<body onload="setMatrixColors();" >';
	}
	else
	{
		echo '<body>';
	}
	
?>


<?php include("nav.php");  ?>


<div class="clearfix">
<div class="column">
	

    <form method="post" name="frmMatrix" id="frmMatrix" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<img src="System-Control.png" alt="System Control" width="100%" />
    <p><label for="SystemName">System Name:</label><br />
    <select id="SystemNameId" name="SystemName" onChange="setSystemSettings();">
        <?php echo $lightSystemsoption;?>
        </select>       
    </p>
	<p><button type="submit" name="btnWorkMatrix">Create Art!</button>
	<p><button type="submit" onClick="storeMatrix()" name="btnDisplayArt" >Display Art!</button>

    </div>


    <div class="column">
        <div class="ColumnStyles">
		<div style="text-align:center">
		  <h1>Matrix Art!</h1>
			<label>Base Color</label>
			<input type="color" id="baseColor" onchange="setMatrixColors()" name="baseColor" value="#1E90FF" />
			<label>Color Select</label>
			<input type="color" id="colorSelect" name="colorSelect" value="#34ebde" />
			<input type="text" id="matrixData" name="matrixData" hidden />
			<div oncontextmenu="return false;" id="divMatrix" name="divMatrix">
		<p></p><?php echo $matrixHTML; ?></P>
		</div>
		</div>
		
    </div>
    </div>
	</div>
	<?php include('footer.php'); ?>

<script>




<?php echo $lightSystemsScript;?>
   

let isDrawing = false;
const divMatrix = document.getElementById('divMatrix');

divMatrix.addEventListener('mousedown', e => {
	e.stopPropagation();
    e.preventDefault();

	
	
	switch(e.which)
	{
		case 1:
		isDrawing = true;
		break;

		case 2:
		setToBaseColor();                          
		break;

		case 3:
		setColor();	   
		break;
	}

  
});


divMatrix.addEventListener('mousemove', e => {
	 e.stopPropagation();
     e.preventDefault();
	
	if(isDrawing)
	{
	
		setColor();
	}
});


divMatrix.addEventListener('mouseup', e => {
    e.stopPropagation();
    e.preventDefault();
	isDrawing = false;
	
});





function setColor()
{	
	var pixel = document.getElementById(this.event.target.id);
	if(pixel.id != "divMatrix")
	{
		
		//var baseColor = document.getElementById('baseColor');
		var color = document.getElementById('colorSelect');
		
		//if(getColorHex(pixel.style.backgroundColor) == baseColor.value)
		//{
			pixel.style.backgroundColor = color.value;
		//}
		//else
		//{

			//pixel.style.backgroundColor = baseColor.value;
		//}
		
	}
}

function setToBaseColor()
{	
	isDrawing = false;
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
    
    for(var row = 0; row < system.stripRows; row++)
    {
				
		for(var column = 0; column < system.stripColumns ; column++)
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

function rgbToWebHex(r, g, b) {
  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function componentFromStr(numStr, percent) {
    var num = Math.max(0, parseInt(numStr, 10));
    return percent ?
        Math.floor(255 * Math.min(100, num) / 100) : Math.min(255, num);
}


function rgbToHex(rgb) {
	
    var rgbRegex = /^rgb\(\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*,\s*(-?\d+)(%?)\s*\)$/;
    var result, r, g, b, hex = "";
    if ( (result = rgbRegex.exec(rgb)) ) {
			
		
        r = componentFromStr(result[1], result[2]);
        g = componentFromStr(result[3], result[4]);
        b = componentFromStr(result[5], result[6]);
		
        hex = "0x" + (0x1000000 + (r << 16) + (g << 8) + b).toString(16).slice(1);
        //hex = rgbToWebHex(r,g,b);
        
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
    var numLeds = system.stripRows * system.stripColumns;
    var ledNum = 0;
    var currentPos = 0;
    
    var matrixJson = '{"show": "23","gammaCorrection": 1, "brightness":"70", "pixles": {';
    
	for(var row = 0; row < system.stripRows; row++)
    {
		if((row % 2) == 0)
			currentPos += system.stripColumns;
	
		for(var column = 0; column < system.stripColumns; column++)
		{
	
			if((row % 2) != 0)
			{
				currentPos += 1;
				pixel = document.getElementById(currentPos);
				matrixJson += '"' + currentPos + '":{"r":' + row + ',"c":' + column + ',"co":"' + rgbToHex(pixel.style.backgroundColor) + '"}';
			
				
			}
			else
			{
			
				var pos = currentPos - column;
				pixel = document.getElementById(pos);
				matrixJson += '"' + pos + '":{"r":' + row + ',"c":' + column + ',"co":"' + rgbToHex(pixel.style.backgroundColor) + '"}';

				
			}
					
			if(column != (system.stripColumns - 1))
				matrixJson += ",";
			
		}
		
		if(row != system.stripRows - 1)
			matrixJson += ",";
		
	}
    
	matrixJson += '}}';
	matrixData.value = matrixJson;
		
	
}


</script>


	
	 </form>
   
	
</body>
</html>

