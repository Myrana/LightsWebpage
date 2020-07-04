<?php

include_once('CommonFunctions.php');

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}


$_SESSION["DesignerEditMode"]  = 1;
    
$conn = getDatabaseConnection();

$playlistoption = '';
$playListScript = "";


if(isset($_REQUEST['CommitPlayList']))
{
	if(!empty($_POST['jsonContainer']))
    {
	    $sql = "update userPlaylist set showParms='" << $_POST['jsonContainer'] << "' where ID = " << $_POST['PlayList'];
		if ($conn->query($sql) == FALSE)
        {
            echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
        }
	}
   
}


if(isset($_REQUEST['btnCreatePlayList']))
{
	if(!empty($_POST['NewPlayListName']))
    {
	    $sql = "insert into userPlaylist(userID, playlistName, showParms) values(". $_SESSION['UserID'] . ",'" . $_POST['NewPlayListName'] . "','[]')" ;
		
		if ($conn->query($sql) == FALSE)
        {
            echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
        }
	}
}


if(isset($_REQUEST['btnDeletePlayList']))
{
	
	if(!empty($_POST['PlayList']))
    {
		$sql = "delete from userPlaylist where userID = " . $_SESSION['UserID'] . " and ID = " . $_POST['PlayList'];
		
        if ($conn->query($sql) == FALSE)
        {
            echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
        }
        
	}
	
	
			//$sendArray['deletePlaylist'] = 1;
		//$sendArray['playlistName'] = $_POST['PlayList'];
		//$sendArray['UserID'] = $_SESSION['UserID'];
		
		//$results = mysqli_query($conn,"SELECT ID,serverHostName from lightSystems WHERE enabled = 1 and masterDevice = 1");
        //if(mysqli_num_rows($results) > 0)
        //{
          //  while($row = mysqli_fetch_array($results))
            //    sendMQTT($row["serverHostName"], json_encode($sendArray));
       // }

}


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


<body onLoad="setPlayListSettings();">


	<?php include('Nav.php'); ?>
	
	<script>
	<?php echo $playListScript;?>
	

function rgbToHex(r, g, b)
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


	function removeShowSettings()
    {
		

		var playListId = document.getElementById("PlayList");
		var showListControl = document.getElementById("ShowName");
        
        var playListIndex = parseInt(playListId.value);
        var playList = playListMap.get(playListIndex);
        var showIndex = parseInt(showListControl.value) - 1;
		
        for (i in playList.showParms)
        {
			if(i == showIndex)
            {
				playList.showParms.splice(showIndex,1); 
				break;
			}
			
		}
		
		setPlayListSettings();
	}
	
    function savePlayList()
    {

        var jsonContainer = document.getElementById("jsonContainer");
        var playListId = document.getElementById("PlayList");
        var playListIndex = parseInt(playListId.value);
        var playList = playListMap.get(playListIndex);
       
        jsonContainer.value = JSON.stringify(playList.showParms);


    }

    function saveShowSettings()
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
        var clearStart = document.getElementById("clearStart");
        var clearFinish = document.getElementById("clearFinish");
        var gammaCorrection = document.getElementById("gammaCorrection");
        var powerOn = document.getElementById("powerOn");
        

        var showIndex = parseInt(showListControl.value) - 1;


        for (i in playList.showParms)
        {
            if(i == showIndex)
            {
                var show = showMap.get(parseInt(playList.showParms[i].show));

                 if(show.hasDelay)
                     playList.showParms[i].delay = delay.value;

                    if(show.hasMinutes > 0)
                        playList.showParms[i].minutes = minutes.value;

                    if(show.colorEvery > 0)
                        playList.showParms[i].colorEvery = colorEvery.value;

                    if(show.hasWidth)
                        playList.showParms[i].width = width.value;


                    playList.showParms[i].brightness = brightness.value;

                    if(show.numColors > 0)
                    {
                        switch(show.numColors)
                        {
                            case 1:
                                var cvtColor = hexToRgb(color1.value);
                                playList.showParms[i].colors.color1.r = cvtColor.r;
                                playList.showParms[i].colors.color1.g = cvtColor.g;
                                playList.showParms[i].colors.color1.b = cvtColor.b;
                              break;

                            case 2:
                                var cvtColor = hexToRgb(color1.value);
                                playList.showParms[i].colors.color1.r = cvtColor.r;
                                playList.showParms[i].colors.color1.g = cvtColor.g;
                                playList.showParms[i].colors.color1.b = cvtColor.b;

                                cvtColor = hexToRgb(color2.value);
                                playList.showParms[i].colors.color2.r = cvtColor.r;
                                playList.showParms[i].colors.color2.g = cvtColor.g;
                                playList.showParms[i].colors.color2.b = cvtColor.b;

                              break;

                            case 3:
                                var cvtColor = hexToRgb(color1.value);
                                playList.showParms[i].colors.color1.r = cvtColor.r;
                                playList.showParms[i].colors.color1.g = cvtColor.g;
                                playList.showParms[i].colors.color1.b = cvtColor.b;

                                cvtColor = hexToRgb(color2.value);
                                playList.showParms[i].colors.color2.r = cvtColor.r;
                                playList.showParms[i].colors.color2.g = cvtColor.g;
                                playList.showParms[i].colors.color2.b = cvtColor.b;

                                cvtColor = hexToRgb(color3.value);
                                playList.showParms[i].colors.color3.r = cvtColor.r;
                                playList.showParms[i].colors.color3.g = cvtColor.g;
                                playList.showParms[i].colors.color3.b = cvtColor.b;
                              break;

                            case 4:
                                var cvtColor = hexToRgb(color1.value);
                                playList.showParms[i].colors.color1.r = cvtColor.r;
                                playList.showParms[i].colors.color1.g = cvtColor.g;
                                playList.showParms[i].colors.color1.b = cvtColor.b;

                                cvtColor = hexToRgb(color2.value);
                                playList.showParms[i].colors.color2.r = cvtColor.r;
                                playList.showParms[i].colors.color2.g = cvtColor.g;
                                playList.showParms[i].colors.color2.b = cvtColor.b;

                                cvtColor = hexToRgb(color3.value);
                                playList.showParms[i].colors.color3.r = cvtColor.r;
                                playList.showParms[i].colors.color3.g = cvtColor.g;
                                playList.showParms[i].colors.color3.b = cvtColor.b;

                                cvtColor = hexToRgb(color4.value);
                                playList.showParms[i].colors.color4.r = cvtColor.r;
                                playList.showParms[i].colors.color4.g = cvtColor.g;
                                playList.showParms[i].colors.color4.b = cvtColor.b;

                            break;
                        }

                    }

					playList.showParms[i].clearStart = clearStart.checked;
					playList.showParms[i].clearFinish   = clearStart.checked;
					playList.showParms[i].gammaCorrection   = gammaCorrection.checked;	
					playList.showParms[i].gammaCorrection = powerOn.checked;
					break;

            }
        }

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
        var clearStart = document.getElementById("clearStart");
        var clearFinish = document.getElementById("clearFinish");
        var gammaCorrection = document.getElementById("gammaCorrection");
        var powerOn = document.getElementById("powerOn");
        
        showControl.disabled = true;
        
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
                    colorEvery.value =  playList.showParms[i].colorEvery;

                if(show.hasWidth)
                    width.value = playList.showParms[i].width;

                brightness.value = playList.showParms[i].brightness;

                if(show.numColors > 0)
                {
                    switch(show.numColors)
                    {
                        case 1:
                            color1.value =  rgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            break;

                        case 2:
                            color1.value =  rgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color2.value =  rgbToHex(playList.showParms[i].colors.color2.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            break;

                        case 3:
                            color1.value =  rgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color2.value =  rgbToHex(playList.showParms[i].colors.color2.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color3.value =  rgbToHex(playList.showParms[i].colors.color3.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            break;

                        case 4:
                            color1.value =  rgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color2.value =  rgbToHex(playList.showParms[i].colors.color2.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color3.value =  rgbToHex(playList.showParms[i].colors.color3.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color4.value =  rgbToHex(playList.showParms[i].colors.color4.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            break;

                    }

                }
                
                //handle checkboxes
                clearStart.checked = (playList.showParms[i].clearStart != undefined && playList.showParms[i].clearStart == 1) ? true : false;
                clearFinish.checked = (playList.showParms[i].clearFinish != undefined && playList.showParms[i].clearFinish == 1) ? true : false;
                gammaCorrection.checked = (playList.showParms[i].gammaCorrection != undefined && playList.showParms[i].gammaCorrection == 1) ? true : false;
				powerOn.checked = (playList.showParms[i].powerOn != undefined && playList.showParms[i].powerOn == 1) ? true : false;
				
				
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
	<div class="column" style="margin-top: 15px;">
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label for="PlayList">Select Playlist</label>
	<select id="PlayList" name="PlayList" onchange="setPlayListSettings();">
	<?php echo $playlistoption;?>
	</select>
		
		<p><label for="ShowName">Select Show</label><select id="ShowName" name="ShowName" onchange="setShowParms();"></select></p>
		<p><label>New Playlist Name*</label> <br /><input type="text" id="NewPlayListName" name="NewPlayListName" max="50" placeholder="Enter a playlist name (50 characters)" style="width: 100%"></p>

		<p>
		<button type="submit" name="btnCreatePlayList" style="margin: 3px;">Create Playlist</button>
		<button type="submit" name="btnDeletePlayList" style="margin: 3px;">Delete Playlist</button>
		</p>
	  
	  </div>
	  
         <input name="jsonContainer" type="text" id="jsonContainer" hidden></p>
        <?php include('showDesigner.php'); ?>

    </div>
	</div>
</div>

</form>

</body>
<?php include('Footer.php'); ?>
</html>
