<?php

include_once('commonFunctions.php');

if($_SESSION['authorized'] == 0)
{
  header("Location: registration.php");
  exit();
}

$_SESSION["DesignerEditMode"]  = 3;
    
$conn = getDatabaseConnection();

$playlistoption = "";
$playListScript = "";

if(isset($_REQUEST['btnCommitArtShow']))
{
	if(!empty($_POST['jsonContainer']))
    {
	    $sql = "update matrixArt set showParms='" . $_POST['jsonContainer'] . "' where ID = " . $_POST['PlayArtShow'];
	    if ($conn->query($sql) == FALSE)
        {
            echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
        }
	}
   
}

if(isset($_REQUEST['btnDeleteArtShow']))
{
	$sql = "delete from matrixArt where ID = " . $_POST['PlayArtShow'];
	if ($conn->query($sql) == FALSE)
	{
		echo "<h1>Error: " . $conn->error . "</h1>";
		echo $sql;	
	}
   
}



$_SESSION['userArtScript'] = "";
$_SESSION['userArtOptions'] = "";
$artresults = mysqli_query($conn,"SELECT * FROM  matrixArt where userID =" . $_SESSION['UserID'] . " or userID = 1");

if(mysqli_num_rows($artresults) > 0)
{
	$_SESSION['userArtScript']  = "let artListMap = new Map();\r";
	while($artRow = mysqli_fetch_array($artresults))
	{
	
		$_SESSION['userArtScript']  .= "var art = new Object(); \r";

		$_SESSION['userArtScript']  .= "    art.id = " . $artRow['ID'] .";\r";
		$_SESSION['userArtScript']  .= "    art.userId = " . $artRow['userID'] .";\r";
		$_SESSION['userArtScript']  .= "    art.artName = '" . $artRow['artName'] ."';\r";
		$_SESSION['userArtScript']  .= "    art.showParms = JSON.parse('" . $artRow['showParms'] . "');\r";       
		$_SESSION['userArtScript']  .= "    artListMap.set(" . $artRow['ID'] . ", art);\r";
		
		$_SESSION['userArtOptions']  .="<option value = '".$artRow['ID']."'>".$artRow['artName']."</option>";
	}

}



/*
if(isset($_REQUEST['btnDeletePlayList']))
{
	
	if(!empty($_POST['PlayList']))
    {
		$sql = "delete from userPlaylist where ID = " . $_POST['PlayList'];
        if ($conn->query($sql) == FALSE)
        {
            echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
        }
        
	}
	

}
*/


$conn->close();

?>



<!doctype html>
<?php 
$title = 'Edit Shows';
include('header.php'); 
?>

<?php include('nav.php'); ?>

<body onLoad="setArtShowSettings();">


	
	<script>
	

	

function jsonrgbToHex(r, g, b)
{
  return "#" + ((1 << 24) + (parseInt(r) << 16) + (parseInt(g) << 8) + parseInt(b)).toString(16).slice(1);
}

function hexToRgb(hex)
{
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function(m, r, g, b){
    return r + r + g + g + b + b;
  });

  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ?
    {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}



	
  

function setArtShowSettings()
{
	
	var showControl = document.getElementById("ShowNameId");
	var matrixData = document.getElementById("matrixDiv");
	var divMatrix = document.getElementById("divMatrix");
	var divArt = document.getElementById("divArt");
	var currentPos = 0;
	var matrixHTML = "";
	
	var playArtShow = document.getElementById("PlayArtShow");
	var art = artListMap.get(parseInt(playArtShow.value));
	var systemNameId = document.getElementById("SystemNameId");
	
	showControl.disabled = true;
	showControl.value = art.showParms.show;
	setShowSettings(true);
	
	system = systemsMap.get(parseInt(art.showParms.systemId));
	if(system == undefined)
		system = systemsMap.get(parseInt(systemNameId.value));
				
	for(var ledRow = 0; ledRow < system.channelsMap.get(1).stripRows; ledRow++)
	{
		
		for(var ledColumn = 0; ledColumn < system.channelsMap.get(1).stripColumns; ledColumn++)
		{
			
			currentPos += 1;
			matrixHTML += "<span id='" + currentPos  + "' class='pixel' style='background-color:" + art.showParms.pixles[currentPos].co.replace("0x","#") + "' ></span>";
			
		}
		matrixHTML += "<br>";

	}

	
	divMatrix.innerHTML = matrixHTML;
	
}


function saveArtSettings()
{

	var playArtShow = document.getElementById("PlayArtShow");
	var showListControl = document.getElementById("ShowName");
	var showControl = document.getElementById("ShowNameId");

	var brightness = document.getElementById("Brightness");
	var clearStart = document.getElementById("clearStart");
	var clearFinish = document.getElementById("clearFinish");
	var gammaCorrection = document.getElementById("gammaCorrection");
	var matrixData = document.getElementById("matrixData");
	var systemNameId = document.getElementById("SystemNameId");
	var channelId = document.getElementById("ChannelId");
	var matrixData = document.getElementById("matrixData");
	var jsonContainer = document.getElementById("jsonContainer");
    
	var systemNameId = document.getElementById("SystemNameId");
	
	var art = artListMap.get(parseInt(playArtShow.value));
	system = systemsMap.get(parseInt(art.showParms.systemId));
	if(system == undefined)
		system = systemsMap.get(parseInt(systemNameId.value));
	
	art.systemId = systemNameId.value;
	art.channelId = channelId.value;
			
	
	storeMatrix();
	
	if(matrixData.value.length > 0 )
	{
		
		art.showParms.pixles = JSON.parse(matrixData.value);
		
		
	}
	art.brightness = brightness.value;


	art.clearStart = (clearStart.checked) ? 1 : 0;
	art.clearFinish   = (clearFinish.checked) ? 1 : 0;
	art.gammaCorrection   = (gammaCorrection.checked) ? 1 : 0;
	
	jsonContainer.value = JSON.stringify(art.showParms);
	
	
}


  
function confirmDelete()
{
	var playArtShow = document.getElementById("PlayArtShow");
	var art = artListMap.get(parseInt(playArtShow.value));
	
	var retVal = confirm("Are you sure you want to delete? " + art.artName);
	
	if (retVal)
	  return true;
	else
		return false;
	
} 




</script>
	
<center><img src="Images/edit-shows.png" alt="Edit Art Work" /></center>
<div class="clearfix">
	<div class="column twenty-five">
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<label for="PlayList">Select Art</label>
			<select id="PlayArtShow"  name="PlayArtShow" onChange="setArtShowSettings();">
				<?php echo $_SESSION['userArtOptions'];?>
			</select>
		<input name="jsonContainer" type="text" id="jsonContainer" hidden></p>
		<button type="submit" onClick="saveArtSettings();" name="btnCommitArtShow" id="btnCommitArtShow">Save Art Work</button>	
		<button type="submit" onClick="return confirmDelete();" name="btnDeleteArtShow" id="btnDeleteArtShow">Delete Art Work</button>	
		</p>
		
		<p style="margin-top: -12px; margin-left: 3px">
		<button onclick="location.href='lightShows.php'; return false" name="btnLightShows">Light Shows</button>	
		</p>
	  
	</div>
	  
        <?php include('showDesigner.php'); ?>
        
        
</div>
	
</form>

</body>

</html>
