<?php

include_once('commonFunctions.php');

if($_SESSION['authorized'] == 0)
{
  header("Location: registration.php");
  exit();
}

$_SESSION["DesignerEditMode"]  = 1;
    
$conn = getDatabaseConnection();

$playlistoption = "";
$playListScript = "";

if(isset($_REQUEST['CommitPlayList']))
{
	if(!empty($_POST['jsonContainer']))
    {
	    $sql = "update userPlaylist set showParms='" . $_POST['jsonContainer'] . "' where ID = " . $_POST['PlayList'];
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
		$sendUserID =  $_SESSION['UserID'];
		if(!empty($_POST['allUsersPlaylist']))
		{
       			$sendUserID = '1';
		}

	    $sql = "insert into userPlaylist(userID, playlistName, showParms) values(". $sendUserID . ",'" . $_POST['NewPlayListName'] . "','[]')" ;
		
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
		$sql = "delete from userPlaylist where ID = " . $_POST['PlayList'];
        if ($conn->query($sql) == FALSE)
        {
            echo "<h1>Error: " . $conn->error . "</h1>";
			echo $sql;	
        }
        
	}
	

}


$results = mysqli_query($conn,"SELECT ID,userID,playlistName,showParms FROM userPlaylist where userID =" . $_SESSION['UserID'] . " or userID = 1");
if(mysqli_num_rows($results) > 0)
{
	$playListScript = "let playListMap = new Map();\r";
	$playListScript .= "let userId = '" . $_SESSION['UserID'] . "';\r";
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
<?php 
$title = 'Edit Shows';
include('header.php'); 
?>

<?php include('nav.php'); ?>

<body onLoad="setPlayListSettings();">


	
	<script>
	<?php echo $playListScript;?>
	
	

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



	function addShowSettings()
	{
		
		
		var matrixData = document.getElementById("matrixData");
		
		var playListId = document.getElementById("PlayList");
		var showListControl = document.getElementById("ShowName");
		var showControl = document.getElementById("ShowNameId");
		
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
        var hasText = document.getElementById("hasText");
		var position = document.getElementById("position");
		var direction = document.getElementById("direction");
		var upload = document.getElementById("uploadArt");
		var saveArt = document.getElementById("saveArt");

		var systemNameId = document.getElementById("SystemNameId");
		var channelId = document.getElementById("ChannelId");
		var startRow = document.getElementById("startRow");
		var startColumn = document.getElementById("startColumn");
		var radius = document.getElementById("radius");
		var len = document.getElementById("length");
		var height = document.getElementById("height");
        
        var playListIndex = parseInt(playListId.value);
        var playList = playListMap.get(playListIndex);
        var showIndex = parseInt(showListControl.value) - 1;
		
		var show = showMap.get(parseInt(showControl.value));
        var parmIndex = 0;
		//Deal with new or empty playlist
		
		if(playList.showParms.length == 0)
		{
			var json = '[{"show": "' + show.id + '", "UserID": "' + userId + '"}]';
			
			playList.showParms = JSON.parse(json);
			
		}
		else
		{

			var json = '{"show": "' + show.id + '", "UserID": "' + userId + '"}';

            parmIndex = playList.showParms.length;
			playList.showParms[parmIndex] = JSON.parse(json);
			playList.showParms[parmIndex].UserID = userId;

		}

	
		playList.showParms[parmIndex].systemId = systemNameId.value;
		
		playList.showParms[parmIndex].channelId = channelId.value;
		
		var system = systemsMap.get(parseInt(systemNameId.value));
		
		if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 1)
		{
			storeMatrix();
			if(matrixData.value.length > 0 )
			{
				
				playList.showParms[parmIndex].pixles = JSON.parse(matrixData.value);
			}
		}
			
		
		if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 2)
		{
			if(hasText.value.length > 0)
			{
			   playList.showParms[parmIndex].matrixText = hasText.value;
			   playList.showParms[parmIndex].position = position.value;
			   
			   //alert("added show parm: " + playList.showParms[parmIndex].position);
			}
		}
		
		
		if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 4)
		{
			
			switch(show.matrixShape)
			{
					case 1:
						
						playList.showParms[parmIndex].startRow = startRow.value;
						playList.showParms[parmIndex].startColumn = startColumn.value;
						playList.showParms[parmIndex].radius = radius.value;
					
					break;
					
					case 2:
					case 3:
						playList.showParms[parmIndex].startRow = startRow.value;
						playList.showParms[parmIndex].startColumn = startColumn.value;
						
						playList.showParms[parmIndex].len = len.value;
						playList.showParms[parmIndex].height = height.value;
					break;
					
					case 4:
						playList.showParms[parmIndex].startRow = startRow.value;
						playList.showParms[parmIndex].startColumn = startColumn.value;
						playList.showParms[parmIndex].len = len.value;
						playList.showParms[parmIndex].direction = direction.value;
					break;
					
			}
		
		}
		
		if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 5)
		{
			if(upload.value.length > 0)
			{
			   playList.showParms[parmIndex].upload = upload.value;
			   playList.showParms[parmIndex].saveArt = saveArt.value;
			   
			   //alert("added show parm: " + playList.showParms[parmIndex].position);
			}
		}
		
		
        playList.showParms[parmIndex].brightness = brightness.value;

        if(show.hasDelay)
            playList.showParms[parmIndex].delay = delay.value;
				
		if(show.hasMinutes > 0)
            playList.showParms[parmIndex].minutes = minutes.value;

        if(show.colorEvery > 0)
            playList.showParms[parmIndex].colorEvery = colorEvery.value;

        if(show.hasWidth)
            playList.showParms[parmIndex].width = width.value;

		
        if(show.numColors > 0)
        {

            switch(show.numColors)
            {
                case 1:
                    var cvtColor = hexToRgb(color1.value);
                    var json = '{"color1": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '}}';

                    var colors = JSON.parse(json);

                    playList.showParms[parmIndex].colors = colors;

                  break;

                case 2:
                    var cvtColor = hexToRgb(color1.value);
                    var json = '{"color1": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '},';

                    cvtColor = hexToRgb(color2.value);
                    json += '"color2": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '}}';
                    var colors = JSON.parse(json);
                    playList.showParms[parmIndex].colors = colors;

                  break;

                case 3:
                    var cvtColor = hexToRgb(color1.value);
                    var json = '{"color1": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '},';

                    cvtColor = hexToRgb(color2.value);
                    json += '"color2": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '},';

                    cvtColor = hexToRgb(color3.value);
                    json += '"color3": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '}}';

                    var colors = JSON.parse(json);

                    playList.showParms[parmIndex].colors = colors;

                  break;

                case 4:
                    var cvtColor = hexToRgb(color1.value);
                    var json = '{"color1": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '},';

                    cvtColor = hexToRgb(color2.value);
                    json += '"color2": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '},';

                    cvtColor = hexToRgb(color3.value);
                    json += '"color3": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '},';

                    cvtColor = hexToRgb(color4.value);
                    json += '"color4": {"b": ' + cvtColor.b + ', "g": ' + cvtColor.g + ', "r": ' + cvtColor.r + '}}';

                    var colors = JSON.parse(json);

                   playList.showParms[parmIndex].colors = colors;
                   break;


            }

        }

        playList.showParms[parmIndex].clearStart = (clearStart.checked) ? 1 : 0;
        playList.showParms[parmIndex].clearFinish   = (clearFinish.checked) ? 1 : 0;
        playList.showParms[parmIndex].gammaCorrection   = (gammaCorrection.checked) ? 1 : 0;

	
        var option = document.createElement("option");
     
        option.text = show.showName;
        option.value = showListControl.length + 1;
        showListControl.add(option);

        showListControl.selectedIndex = showListControl.length - 1;
     

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
		var hasText = document.getElementById("hasText");
		var position = document.getElementById("position");
		var direction = document.getElementById("direction");
		var upload = document.getElementById("uploadArt");
		var saveArt = document.getElementById("saveArt");
        var clearStart = document.getElementById("clearStart");
        var clearFinish = document.getElementById("clearFinish");
        var gammaCorrection = document.getElementById("gammaCorrection");
        var matrixData = document.getElementById("matrixData");
        var systemNameId = document.getElementById("SystemNameId");
        var channelId = document.getElementById("ChannelId");
		
		var startRow = document.getElementById("startRow");
		var startColumn = document.getElementById("startColumn");
		var radius = document.getElementById("radius");
		var len = document.getElementById("length");
		var height = document.getElementById("height");
		
	
        var showIndex = parseInt(showListControl.value) - 1;
        
        
		
       
		var matrixData = document.getElementById("matrixData");
		

        for (i in playList.showParms)
        {
            if(i == showIndex)
            {
                var show = showMap.get(parseInt(playList.showParms[i].show));
                playList.showParms[i].systemId = systemNameId.value;

                 if(show.hasDelay)
                     playList.showParms[i].delay = delay.value;
                
                playList.showParms[i].channelId = channelId.value;
				
				if(show.hasText)
					playList.showParms[i].matrixText = hasText.value;
					playList.showParms[i].position = position.value;

                    if(show.hasMinutes > 0)
                        playList.showParms[i].minutes = minutes.value;

                    if(show.colorEvery > 0)
                        playList.showParms[i].colorEvery = colorEvery.value;

                    if(show.hasWidth)
                        playList.showParms[i].width = width.value;

					var system = systemsMap.get(parseInt(playList.showParms[i].systemId));	
					if(system == undefined)
						system = systemsMap.get(parseInt(systemNameId.value));
					
					if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 1)
					{
						storeMatrix();
						if(matrixData.value.length > 0 )
						{
							playList.showParms[i].pixles = JSON.parse(matrixData.value);
						}

					}
		
					if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 2)
					{
						playList.showParms[i].matrixText = hasText.value;
						playList.showParms[i].position = position.value;
						//alert(playList.showParms[i].position);
						//alert("save show parm: " + playList.showParms[i].position);
					}
					
					if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 4)
					{
						switch(show.matrixShape)
						{
								case 1:
									playList.showParms[i].startRow = startRow.value;
									playList.showParms[i].startColumn = startColumn.value;
									playList.showParms[i].radius = radius.value;
									
									

								break;
								
								case 2:
								case 3:
									playList.showParms[i].startRow = startRow.value;
									playList.showParms[i].startColumn = startColumn.value;
									
									playList.showParms[i].len = len.value;
									playList.showParms[i].height = height.value;
									
									break;
								
								case 4:
									playList.showParms[i].startRow = startRow.value;
									playList.showParms[i].startColumn = startColumn.value;
									playList.showParms[i].len = len.value;
									playList.showParms[i].direction = direction.value;
								break;
								
						}
					
					}
				
					if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 5)
				{
				
				   playList.showParms[i].upload = upload.value;
				   playList.showParms[i].saveArt = saveArt.value;

				   //alert("added show parm: " + playList.showParms[parmIndex].position);
				}
			
				
		
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


                    playList.showParms[i].clearStart = (clearStart.checked) ? 1 : 0;
                    playList.showParms[i].clearFinish   = (clearFinish.checked) ? 1 : 0;
                    playList.showParms[i].gammaCorrection   = (gammaCorrection.checked) ? 1 : 0;
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
        var hasText = document.getElementById("hasText");
		var position = document.getElementById("position");
		var direction = document.getElementById("direction");
		var upload = document.getElementById("uploadArt");
		var saveArt = document.getElementById("saveArt");
        var matrixData = document.getElementById("matrixDiv");
        var divMatrix = document.getElementById("divMatrix");
        var divArt = document.getElementById("divArt");
        var systemNameId = document.getElementById("SystemNameId");
        var channelId = document.getElementById("ChannelId");
		
		var startRow = document.getElementById("startRow");
		var startColumn = document.getElementById("startColumn");
		var radius = document.getElementById("radius");
		var len = document.getElementById("length");
		var height = document.getElementById("height");
	
		var showIndex = parseInt(showListControl.value) - 1;	
		
		for (i in playList.showParms)
		{
			
			if(i == showIndex)
			{
				
				var show = showMap.get(parseInt(playList.showParms[i].show));
				//set the control to proper show.
				//next force the onchange event to fire to set the controls to 
				//proper state for show.
	    
								
				
				system = systemsMap.get(parseInt(playList.showParms[i].systemId));	
				
				
				if(system == undefined)
					system = systemsMap.get(parseInt(systemNameId.value));
				
				systemNameId.value = system.id;
		
				if(playList.showParms[i].channelId != undefined)
				{
					channelId.options[playList.showParms[i].channelId - 1].selected = true;
				}
				
				showControl.value = show.id;	
				
				divArt.setAttribute('hidden', true);
				divArt.hidden = true;
				if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 1)
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
							currentPos += 1;
							matrixHTML += "<span id='" + currentPos  + "' class='pixel' style='background-color:" + playList.showParms[i].pixles[currentPos].co.replace("0x","#") + "' ></span>";		
						}
						matrixHTML += "<br>";

					}
				
				
					divMatrix.innerHTML = matrixHTML;
					
					
				}	
				if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 2)
				{
					hasText.value = playList.showParms[i].matrixText;
					hasText.setAttribute('disabled', false);
					hasText.disabled = false;
					position.value = playList.showParms[i].position;
					position.setAttribute('disabled', false);
					position.disabled = false;
				}
				
				if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 4)
				{
					
					
					switch(show.matrixShape)
					{
							case 1:
							 	startRow.value = playList.showParms[i].startRow;
								startColumn.value = playList.showParms[i].startColumn ;
								radius.value = playList.showParms[i].radius;

							break;
								
							case 2:
							case 3:
							
							 	startRow.value = playList.showParms[i].startRow;
								startColumn.value = playList.showParms[i].startColumn;
									
								len.value = playList.showParms[i].len;
								height.value = playList.showParms[i].height;
									
								break;
							
							case 4:
							
							 	startRow.value = playList.showParms[i].startRow;
								startColumn.value = playList.showParms[i].startColumn;	
								len.value = playList.showParms[i].len;
								height.value = playList.showParms[i].height;
								direction.value = playList.showParms[i].direction;
									
								break;
								
					}
					
				}
				
				if( system.channelsMap.get(1).stripRows > 1 && show.matrixType == 5)
				{
					upload.value = playList.showParms[i].uploadArt;
					upload.setAttribute('disabled', false);
					upload.disabled = false;
					saveArt.value = playList.showParms[i].saveArt;
					saveArt.setAttribute('disabled', false);
					saveArt.disabled = false;
				}
			
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
                            color1.value =  jsonrgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            break;

                        case 2:
                            color1.value =  jsonrgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color2.value =  jsonrgbToHex(playList.showParms[i].colors.color2.r, playList.showParms[i].colors.color2.g, playList.showParms[i].colors.color2.b);
                            break;

                        case 3:
                            color1.value =  jsonrgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color2.value =  jsonrgbToHex(playList.showParms[i].colors.color2.r, playList.showParms[i].colors.color2.g, playList.showParms[i].colors.color2.b);
                            color3.value =  jsonrgbToHex(playList.showParms[i].colors.color3.r, playList.showParms[i].colors.color3.g, playList.showParms[i].colors.color3.b);

                            break;

                        case 4:
                            color1.value =  jsonrgbToHex(playList.showParms[i].colors.color1.r, playList.showParms[i].colors.color1.g, playList.showParms[i].colors.color1.b);
                            color2.value =  jsonrgbToHex(playList.showParms[i].colors.color2.r, playList.showParms[i].colors.color2.g, playList.showParms[i].colors.color2.b);
                            color3.value =  jsonrgbToHex(playList.showParms[i].colors.color3.r, playList.showParms[i].colors.color3.g, playList.showParms[i].colors.color3.b);
                            color4.value =  jsonrgbToHex(playList.showParms[i].colors.color4.r, playList.showParms[i].colors.color4.g, playList.showParms[i].colors.color4.b);
                            break;

                    }

                }
                //handle checkboxes
                clearStart.checked = (playList.showParms[i].clearStart != undefined && playList.showParms[i].clearStart == 1) ? true : false;
                clearFinish.checked = (playList.showParms[i].clearFinish != undefined && playList.showParms[i].clearFinish == 1) ? true : false;
                gammaCorrection.checked = (playList.showParms[i].gammaCorrection != undefined && playList.showParms[i].gammaCorrection == 1) ? true : false;
				

				break;
			}
		}
		
		setShowSettings(false);
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
		
        setShowParms();
        setShowSettings(false);

  }
  
function confirmDelete()
{
	var playListId = document.getElementById("PlayList");
	var playListIndex = parseInt(playListId.value);
	var playList = playListMap.get(playListIndex);

	var retVal = confirm("Are you sure you want to delete? " + playList.playListName);
	
	if (retVal)
	  return true;
	else
		return false;
	
} 


function setArtSystem()
{
	var artShowId = document.getElementById("PlayArtShow");
	if(artShowId.value != 0)
	{
		
		var systemNameId = document.getElementById("SystemNameId");
		var playArtShow = document.getElementById("PlayArtShow");
		var art = artListMap.get(parseInt(playArtShow.value));
		
		systemNameId.value = art.showParms.systemId;
	}
}


function addArtShow()
{
	
	var artShowId = document.getElementById("PlayArtShow");
	if(artShowId.value != 0)
	{
		
		var systemNameId = document.getElementById("SystemNameId");
		var playArtShow = document.getElementById("PlayArtShow");
		var playListId = document.getElementById("PlayList");
		var showControl = document.getElementById("ShowNameId");
		var showListControl = document.getElementById("ShowName");
		
		var art = artListMap.get(parseInt(playArtShow.value));
		var playList = playListMap.get(parseInt(playListId.value));
		playList.showParms[playList.showParms.length] = art.showParms;
		
		
		var option = document.createElement("option");
		var show = showMap.get(parseInt(art.showParms.show));
		
		option.text = show.showName;
		option.value = showListControl.length + 1;
		showListControl.add(option); 
		
		showListControl.value = showListControl.length;
		showControl.value = art.showParms.show;
	
		setShowParms();
	  
	}
	
	return false;
}

	
</script>
	
<center><img src="Images/edit-shows.png" alt="Edit Shows" /></center>
<div class="clearfix">
	<div class="column twenty-five">
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<label for="PlayList">Select Playlist</label>
			<select id="PlayList" name="PlayList" onchange="setPlayListSettings();">
			<?php echo $playlistoption;?>
			</select>
		
		<p>
		<label for="ShowName">Select Show</label><select id="ShowName" name="ShowName" onchange="setShowParms();"></select>
		</p>
		<p>
		<label>Art</label>
				<select id="PlayArtShow"  name="PlayArtShow" onChange="setArtSystem();"><?php echo $_SESSION['userArtOptions'];?></select>
				<button name="btnAddArtShow" onclick="return addArtShow();">Add Art</button>
		</p>
		<p>
		<label>New Playlist Name*</label> <br /><input type="text" id="NewPlayListName" name="NewPlayListName" max="50" placeholder="Enter a playlist name (50 characters)" style="width: 100%">
		</p>

		<p style="margin-bottom: -14px;">
		<button type="submit" name="btnCreatePlayList" style="margin: 3px;">Create Playlist</button>
		<button type="submit" onclick="return confirmDelete();" name="btnDeletePlayList" style="margin: 3px;">Delete Playlist</button>
		</p>
			
		<p style="margin-left: 3px">
		<input type="checkbox" id="allUsersPlaylist" name="allUsersPlaylist" />
		<label for="allUserPlaylist" style="margin-left: 3px">All users can use</label>
		<button type="submit" onClick="savePlayList();" name="CommitPlayList" id="CommitPlayList">Save PlayList</button>	
		</p>
		
		<p style="margin-top: -12px; margin-left: 3px">
		<button onclick="location.href='lightShows.php'; return false" name="btnLightShows">Light Shows</button>	
		</p>
	  
	</div>
	  
         <input name="jsonContainer" type="text" id="jsonContainer" hidden></p>
        <?php include('showDesigner.php'); ?>
        
</div>
	
</form>

</body>

</html>
