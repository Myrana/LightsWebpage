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
	
	$results = mysqli_query($conn,"SELECT ID, systemName, stripRows, stripColumns, brightness FROM lightSystems WHERE enabled = 1 and ID =" . $_POST['SystemName']);
	if(mysqli_num_rows($results) > 0)
	{
		$row = mysqli_fetch_array($results);
		$ledNumber = 0;
		$ledRows = $row['stripRows'];
		$ledColumns = $row['stripColumns'];
		if($ledRows == 1)
		{
			 $ledRows = $ledColumns;
			 $ledColumns = 1;
		}
		
		for($ledRow = 0; $ledRow < $ledRows; $ledRow++)
		{
			for($ledColumn = 0; $ledColumn < $ledColumns; $ledColumn++)
			{
				$ledNumber += 1;
				$matrixHTML .= "<span id='" . $ledNumber . "' onClick='getId()' class='pixel'></span>";			
				
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

$results = mysqli_query($conn,"SELECT ID, systemName, stripRows, stripColumns, brightness FROM lightSystems WHERE enabled = 1 and userId =" . $_SESSION['UserID'] . " or userId =1");
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
  background-color: #ADD8E6;
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


<script>

<?php echo $lightSystemsScript;?>
   
function getId()
{	
	var pixel = document.getElementById(this.event.target.id);
	var color = document.getElementById('colorSelect');
	
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
    
    for(var row = 1; row < system.stripRows; row++)
    {
		
		for(var column = 0; row < system.stripColumns; column++)
		{
			ledNum += 1;
			pixel = document.getElementById(ledNum);
			pixel.style.background = baseColor.value;
			
			
		}	
	}

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
    var colNum = 0;
    
    var matrixJson = '{"show": "23","pixles": {';
	  
	for(var row = 1; row <= system.stripRows; row++)
    {
	  
		for(var column = 0; column < system.stripColumns; column++)
		{
			ledNum += 1;
			pixel = document.getElementById(ledNum);
			matrixJson += '"' + ledNum + '":{"r":' + row + ',"c":' + column + ',"co":"' + rgbToHex(pixel.style.backgroundColor) + '"}';
			if(column != (system.stripColumns - 1))
				matrixJson += ",";
		
		}
		
		if(row != system.stripRows)
			matrixJson += ",";	
	}

	matrixJson += '}}';
	
	matrixData.value = matrixJson;
		
	
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
	<p><button type="submit" name="btnWorkMatrix">Create Art!</button>
	<p><button type="submit" onClick="storeMatrix()" name="btnDisplayArt" >Display Art!</button>

    </div>


    <div class="column">
        <div class="ColumnStyles">
		<div style="text-align:center">
		  <h1>Matrix Art!</h1>
			<input type="color" id="baseColor" name="baseColor" value="#ADD8E6" />
			<input type="checkbox" id="reset" name="reset" /><label>reset</label>
			<input type="color" id="colorSelect" name="colorSelect" value="#34ebde" />
			<input type="text" id="matrixData" name="matrixData" hidden />
		<p></p><?php echo $matrixHTML; ?></P>
		
		</div>
		
    </form>
    </div>
    </div>
	</div>
	<?php include('footer.php'); ?>
	
	
	
</body>
</html>

