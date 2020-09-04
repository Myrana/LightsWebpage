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
	if(!empty($_POST['ChannelId']))
		$_SESSION['ChannelId'] = $_POST['ChannelId'];


	if(!empty($_POST['jsonContainer']))
    {
		 $sql = "select stripColumns from lightSystemChannels where lightSystemId = '" . $_SESSION['LightSystemID'] . "' and channelId = '" . $_SESSION['ChannelId'] . "';";
		  
		 $results = mysqli_query($conn , $sql);
		  
		  if(mysqli_num_rows($results) > 0)
          {
				$row = mysqli_fetch_array($results);
				$sql = "update matrixArt set showParms='" . $_POST['jsonContainer'] . "', savedPixalsWidth = '" . $row['stripColumns'] . "' where ID = " . $_POST['PlayArtShow'];
				
	    
				if ($conn->query($sql) == FALSE)
				{
					echo "<h1>Error: " . $conn->error . "</h1>";
					echo $sql;	
				}
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


buildUserArt();

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
$title = 'Edit Art';
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
	var divShapes = document.getElementById("divShapes");
	var systemNameId = document.getElementById("SystemNameId");
	
	var currentPos = 0;
	var matrixHTML = "";
	
	var playArtShow = document.getElementById("PlayArtShow");
	var systemNameId = document.getElementById("SystemNameId");
	showControl.disabled = true;
	setShowSettings();
	
	if(parseInt(playArtShow.value) != 0)
	{
		var art = artListMap.get(parseInt(playArtShow.value));
		system = systemsMap.get(parseInt(systemNameId.value));
		systemNameId.value = system.id;
		showControl.value = art.showParms.show;
		
		var offset = 0;
		var maxRows = Object.keys(art.showParms.pixles).length / art.origWidth;
		for(var ledRow = 0; ledRow < maxRows; ledRow++)
		{
			var skipRow = (ledRow > maxRows) ? true : false;
			if((ledRow > maxRows) )
			{
				break;
			}
			else
			{
				for(var ledColumn = 0; ledColumn < system.channelsMap.get(1).stripColumns; ledColumn++)
				{
					if(ledColumn < art.origWidth)
					{
						currentPos += 1;
						matrixHTML += "<span id='" + currentPos  + "' class='pixel' style='background-color:" + art.showParms.pixles[currentPos - offset].co.replace("0x","#") + "' ></span>";
					}
					else
					{
						currentPos += 1;
						matrixHTML += "<span id='" + currentPos  + "' class='pixel' style='background-color:#000000' ></span>";
						offset++;
					}
					
					
				}
				
				matrixHTML += "<br>";

			}
		}
		
		if(maxRows < system.channelsMap.get(1).stripRows)
		{
			for(var ledRow = maxRows; ledRow < system.channelsMap.get(1).stripRows; ledRow++)
			{
		
				for(var ledColumn = 0; ledColumn < system.channelsMap.get(1).stripColumns; ledColumn++)
				{
					currentPos += 1;
					offset++;
					matrixHTML += "<span id='" + currentPos  + "' class='pixel' style='background-color:#000000' ></span>";
				}
				matrixHTML += "<br>";
			}
			
			
		}
		
		divMatrix.innerHTML = matrixHTML;
		
		setShowSettings(false);
	
	}
	

	
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
	
	art.showParms.systemId = systemNameId.value;
	art.showParms.channelId = channelId.value;
			
	
	storeMatrix();
	if(matrixData.value.length > 0 )
	{
		
		art.showParms.pixles = JSON.parse(matrixData.value);
		
		
	}
	art.showParms.brightness = brightness.value;


	art.showParms.clearStart = (clearStart.checked) ? 1 : 0;
	art.showParms.clearFinish   = (clearFinish.checked) ? 1 : 0;
	art.showParms.gammaCorrection   = (gammaCorrection.checked) ? 1 : 0;
	
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
	
<center><img src="Images/edit-art.png" alt="edit art" /></center>
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
