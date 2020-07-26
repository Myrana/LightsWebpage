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
		$numLeds = $row['stripRows'] * $row['stripColumns'];
		for($ledRow = 0; $ledRow < $row['stripRows']; $ledRow++)
		{
			for($ledColumn = 0; $ledColumn < $row['stripColumns']; $ledColumn++)
			{
				$ledNumber += 1;
				$matrixHTML .= "<span id='" . $ledNumber . "' onClick='getId()' class='pixel'></span>";
				
			}	
			$matrixHTML .= "<br>";
		}
		
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


<body >
<?php include("nav.php");  ?>


<script>

<?php echo $lightSystemsScript;?>
   
function getId()
{	
	var pixel = document.getElementById(this.event.target.id);
	pixel.style.background = 'red';
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
	<p><button type="submit" onClick='storeMatrix()' name="btnDisplayArt" >Display Art!</button>

    </div>


    <div class="column">
        <div class="ColumnStyles">
		<div style="text-align:center">
		  <h1>Matrix Art!</h1>
			<input type="color" id="baseColor" name="baseColor" value="#ADD8E6" />
			<input type="checkbox" id="reset" name="reset" /><label>reset</label>
			<input type="color" id="colorSelect" name="colorSelect" value="#34ebde" />
		<?php echo $matrixHTML; ?>
		
		</div>
		
    </form>
    </div>
    </div>
	</div>
	<?php include('footer.php'); ?>
	
	
	
</body>
</html>

