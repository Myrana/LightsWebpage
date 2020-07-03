<?php

include_once('CommonFunctions.php');

$conn = getDatabaseConnection();

$playlistoption = '';
$playListScript = "";

$results = mysqli_query($conn,"SELECT ID,userID,playlistName,showParms FROM userPlaylist where userID =" . $_SESSION['UserID'] . " or userID = 1");
if(mysqli_num_rows($results) > 0)
{
	$playListScript = "let playListMap = new Map();\r";
	while($row = mysqli_fetch_array($results))
	{
		$playListScript .= "var playList = new Object(); \r";

        $playListScript .= "    playList.id = " . $row['ID'] .";\r";
        $playListScript .= "    playList.userId = " . $row['userID'] .";\r";
        $playListScript .= "    playList.playListName = '" . $row['playlistName'] ."';\r";
        $playListScript .= "    playList.showParms = JSON.parse('" . $row['showParms'] . "');\r";       
        $playListScript .= "    playListMap.set(" . $row['ID'] . ", playList);\r";
		
		$playlistoption .="<option value = '".$row['ID']."'>".$row['playlistName']."</option>";
	}

}


$conn->close();

?>



<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Shows</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">	
</head>

<body onLoad="onBodyLoad();">
	<?php include('Nav.php'); ?>
	
	<script>
	<?php echo $playListScript;?>
	
	function onBodyLoad()
	{
		setPlayListSettings();
		setShowParms();
		
	}
	
	function setShowParms()
	{
		var playListId = document.getElementById("PlayList");
		var showListControl = document.getElementById("ShowName");
		var showControl = document.getElementById("ShowNameId");
		var playListIndex = parseInt(playListId.value);
		var playList = playListMap.get(playListIndex);
	
		var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");
        var brightness = document.getElementById("Brightness");
        
		var showIndex = parseInt(showListControl.value) - 1;	
	    
	    
		for (i in playList.showParms)
		{
			if(i == showIndex)
			{
				
				var show = showMap.get(parseInt(playList.showParms[i].show));
				//set the control to proper show.
				//next force the onchange event to fire to set the controls to 
				//proper state for show.
	    
				showControl.value = show.id;
				showControl.onchange();

                if(show.hasDelay)
                    delay.value = playList.showParms[i].delay;

                if(show.hasMinutes > 0)
                    minutes.value = playList.showParms[i].minutes;
                if(show.colorEvery > 0)
                    colorEvery.value = minutes.value = playList.showParms[i].colorEvery;
			
				//Now for the fun, lets set the controls based on the saved values.
				brightness.value = playList.showParms[i].brightness;
				break;
			}
		}
		
	}
	
	function setPlayListSettings()
	{
		var playListId = document.getElementById("PlayList");
		var showListControl = document.getElementById("ShowName");
		var counter = 1;
        
		showListControl.options.length = 0;
		playListIndex = parseInt(playListId.value);
		var playList = playListMap.get(playListIndex);
	
		for (i in playList.showParms)
		{
			var option = document.createElement("option");
			var showIndex = parseInt(playList.showParms[i].show);
			var show = showMap.get(showIndex);
			
			option.text = show.showName;
			option.value = counter;
			counter = counter + 1;
			showListControl.add(option); 
			
		}
		//We now need to set the first shows parms. 
		setShowParms();
	}

</script>

	<div class="clearfix">
	<div class="column" style="margin-top: 15px;"><label for="PlayList">Dad's Test select</label>
	<select id="PlayList" name="PlayList" onchange="setPlayListSettings();">
	<?php echo $playlistoption;?>
	</select>
		
		<p>
		
	<label for="ShowName">Dad's Second Test select</label>
	<select id="ShowName" name="ShowName" onchange="setShowParms();">
	
	
	</select>
			
		</p>
	  
	  
	  </div>
	  
	  
        <?php include('showDesigner.php'); ?>

    </div>
	</div>
</div>
</body>
<?php include('Footer.php'); ?>
</html>
