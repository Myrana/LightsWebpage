<?php

include_once('CommonFunctions.php');

$_SESSION["Brightness"] = 20;
$_SESSION["LightSystemID"] = -1;
$_SESSION["Delay"] = 10;
$_SESSION["NumLoops"] = 1;

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}


if(!empty($_POST))
{
    $_SESSION["LightSystemID"]  = $_POST['SystemName'];
    $_SESSION["Brightness"] = $_POST['Brightness'];
    $_SESSION["Delay"] = $_POST['Delay'];
    $_SESSION["NumLoops"] = $_POST['NumLoops'];

}

if(isset($_REQUEST['Power']))
{ 

	$onoff = "ON";
    if (empty($_POST['lights']))
      $onoff = "OFF";
    
    $sendArray['state'] = $onoff;   
	sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
	
}


if(isset($_REQUEST['ConfigShow']))
{ 

	$results = mysqli_query($conn,"SELECT serverHostName,numColors,hasDelay, hasSpeed, isBlink, hasWidth  FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
	if(mysqli_num_rows($results) > 0)
	{
		$row = mysqli_fetch_array($results);

		$_SESSION["numColors"] = $row['numColors'];
		$_SESSION["hasDelay"] = $row['hasDelay'];
		$_SESSION["hasSpeed"] = $row['hasSpeed'];
		$_SESSION["isBlink"] = $row['isBlink'];
		$_SESSION["hasWidth"] = $row['hasWidth'];
		
	}

}



if(isset($_REQUEST['LightShow']))
{ 

    $r = 5;
    $g = 3;
    $b = 12;

    $sendArray['UserID'] = $_SESSION['UserID'];
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
        $sendArray['shows'] =  $_POST['ShowName'];

        $sendArray['delay'] = $_SESSION["Delay"];
        $sendArray['numLoops'] = $_SESSION["NumLoops"];
        $sendArray['colors'] = $sendColors;
        if (!empty($_POST['clearStart']))
            $sendArray['clearStart'] = 1;

        if (!empty($_POST['clearFinish']))
            $sendArray['clearFinish'] = 1;

        if (!empty($_POST['powerOn']))
           $sendArray['powerOn'] = "OFF";

    }
    //$_SESSION["Color1"] = $g << 16 | $r << 8 | $b;

    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));

}


    if(isset($_REQUEST['btnSavelist']))
    {
        if(!empty($_POST['PlaylistName']))
        {
            $sendArray['savePlaylist'] = 1;
            $sendArray['playlistName'] = $_POST['PlaylistName'];
            $sendArray['UserID'] = $_SESSION['UserID'];
            $displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
            $query_data = mysqli_fetch_array($displayStrip);

            sendMQTT($query_data['serverHostName'], json_encode($sendArray));
        }

    }

    if(isset($_REQUEST['btnPlaylist']))
    {

        if(!empty($_POST['Playlist']))
        {
            $sendArray['playPlaylist'] = 1;
            $sendArray['playlistName'] = $_POST['Playlist'];
            $sendArray['UserID'] = $_SESSION['UserID'];
            $displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$_SESSION["LightSystemID"] );
            $query_data = mysqli_fetch_array($displayStrip);

            sendMQTT($query_data['serverHostName'], json_encode($sendArray));
        }
    }

    if(isset($_REQUEST['btnDeletePlaylist']))
    {

        if(!empty($_POST['Playlist']))
        {
            $sendArray['deletePlaylist'] = 1;
            $sendArray['playlistName'] = $_POST['Playlist'];
            $sendArray['UserID'] = $_SESSION['UserID'];
            sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
        }

    }
    
	
$lightSystemsoption = '';
$results = mysqli_query($conn,"SELECT ID, systemName FROM lightSystems WHERE enabled = 1");
if(mysqli_num_rows($results) > 0)
{

	while($row = mysqli_fetch_array($results))
	{
		if($row['ID'] == $_SESSION["LightSystemID"] )
	
			$lightSystemsoption .="<option value = '".$row['ID']."' selected='selected'>".$row['systemName']."</option>";
		else
			$lightSystemsoption .="<option value = '".$row['ID']."'>".$row['systemName']."</option>";
	
	}
}


$lightShowsoption = '';
$lightShowsScript = '';

$results = mysqli_query($conn,"SELECT ID,showName,numColors,hasDelay FROM lightShows WHERE enabled = 1");
if(mysqli_num_rows($results) > 0)
{
	$lightShowsScript .= "let showMap = new Map();\r\n";

	while($row = mysqli_fetch_array($results))
	{
		$lightShowsoption .="<option value = '".$row['ID']."'>".$row['showName']."</option>";
		
		
		$lightShowsScript .= "var show = new Object(); \r";
    	
    	$lightShowsScript .= "	show.id = " . $row['ID'] .";\r";
    	$lightShowsScript .= "	show.name = '" . $row['showName'] ."';\r";
    	$lightShowsScript .= "	show.numColors = " . $row['numColors'] .";\r";
    	$lightShowsScript .= "	show.hasDelay = " . $row['hasDelay'] .";\r";
    	
    	$lightShowsScript .= "	showMap.set(" . $row['ID'] . ", show);\r";
    	
  
    	

	}
		
}

$playlistoption = '';
$results = mysqli_query($conn,"SELECT ID, playlistName FROM userPlaylist where userID = " . $_SESSION['UserID']);
if(mysqli_num_rows($results) > 0)
{
	while($row = mysqli_fetch_array($results))
	  $playlistoption .="<option value = '".$row['ID']."'>".$row['playlistName']."</option>";
	
}

$conn->close();

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>System Name Page</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>	
<link href="Styles.css" rel="stylesheet" type="text/css">	
</head>

<script>
function includeHTML() {
  var z, i, elmnt, file, xhttp;
  /*loop through a collection of all HTML elements:*/
  z = document.getElementsByTagName("*");
  for (i = 0; i < z.length; i++) {
    elmnt = z[i];
    /*search for elements with a certain atrribute:*/
    file = elmnt.getAttribute("w3-include-html");
    if (file) {
      /*make an HTTP request using the attribute value as the file name:*/
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {elmnt.innerHTML = this.responseText;}
          if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
          /*remove the attribute, and call this function once more:*/
          elmnt.removeAttribute("w3-include-html");
          includeHTML();
        }
      }      
      xhttp.open("GET", file, true);
      xhttp.send();
      /*exit the function:*/
      return;
    }
  }
};
</script>
<body>
	<div w3-include-html="Nav.html"></div>
	
<script>
includeHTML();
</script>
	
	
		
	

<div class="column">
	
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<p><label for="SystemName">System Name:</label><br />
	<select name="SystemName">
		<?php echo $lightSystemsoption;?>
		</select>	
	</p>
		<label for="On">On</label>
	<input type="checkbox" name="lights"  value="ON" checked>
	<p><button type="submit" name="Power">Power</button></p>	

	
	</div>

	


<script>
	
	<?php echo $lightShowsScript;?>
	
    function setShowSettings()
    {
	
		var showNameId = document.getElementById("ShowNameId");
		var index = parseInt(showNameId.value);
		
		
		var color1 = document.getElementById("color1");
		var color2 = document.getElementById("color2");
		var color3 = document.getElementById("color3");
		var color4 = document.getElementById("color4");
		var delay = document.getElementById("DelayId");
		
		color1.setAttribute('disabled', true);
		color2.setAttribute('disabled', true);
		color3.setAttribute('disabled', true);
		color4.setAttribute('disabled', true);
		delay.setAttribute('disabled', true);

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
    }
</script>

<div class="column">
	<div class="ColumnStyles">

    <form>
    <p><label for="ShowName">Show Name</label><br /><select id="ShowNameId" name="ShowName" size="7" onChange="setShowSettings();">
    <?php echo $lightShowsoption;?></select>
</p>
<p><button type="submit" name="ConfigShow">Config show</button></p>


	<p><input type="color"  Name="color_1" id="color1"><br />
		<input type="color" Name="color_2" id="color2"><br />
		<input type="color" Name="color_3" id="color3"><br />
		<input type="color" Name="color_4" id="color4"><br /></p>
	

		<p><label for="Delay">Delay:</label><br />
<input type="range" step="1" id="DelayId" name="Delay" min="1" max="1000" value="<?php echo $_SESSION["Delay"];?>">
Value: <span id="DelayValue"></span></p>

<script>
var delaySlider = document.getElementById("DelayId");
var delayOutput = document.getElementById("DelayValue");
delayOutput.innerHTML = delaySlider.value;

delaySlider.oninput = function() 
{
	delayOutput.innerHTML = this.value;
}
</script>

	<p><label for="NumLoops">Number Of Loops:</label><br />
<input type="range" step="1" id="NumLoops" name="NumLoops" min="1" max="1000" value="<?php echo $_SESSION["NumLoops"];?>">
Value: <span id="NumLoopsValue"></span></p>

<script>
var numLoopsSlider = document.getElementById("NumLoops");
var numLoopsOutput = document.getElementById("NumLoopsValue");
numLoopsOutput.innerHTML = numLoopsSlider.value;

numLoopsSlider.oninput = function() 
{
	numLoopsOutput.innerHTML = this.value;
}
</script>

		
<p><label for="Brightness">Brightness:</label><br />
	<input type="range" step="1" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="10" max="200">
Value: <span id="BrightnessValue"></span></p>

<script>
	
var brightnessSlider = document.getElementById("Brightness");
var brightnessOutput = document.getElementById("BrightnessValue");
brightnessOutput.innerHTML = brightnessSlider.value;

brightnessSlider.oninput = function() 
{
	brightnessOutput.innerHTML = this.value;
}

</script>
		
		<label for="On">Clear on Start</label>
	<input type="checkbox" name="clearStart">
		<label for="On">Clear on Finish</label>
	<input type="checkbox" name="clearFinish">
		<p>
		<label for="On">Power On</label>
	<input type="checkbox" name="powerOn" value="OFF">
		</p>
	
		<p><button type="submit" name="LightShow">Send Show</button></p>
		
	</div>
	</div>
	<div class="column">
		<div class="ColumnStyles">
		
        



<script>
	
    function testMe()
    {
        var playlistName = document.getElementById("PlayListNameId");
        var playListId = document.getElementById("PlayListId");
        var selectedText = playListId.options[playListId.selectedIndex].text;
        playlistName.value = selectedText;
        
      //  alert(playListId.value);
        //alert(showMap.get(playListId.selectedIndex - 1).name);
        ///for (let key of showMap.keys()) 
        //{
  
			//alert(key);
		//}

    }
</script>

	<form>
		<label>Playlist</label> <br />
		<select id="PlayListId"  name="Playlist" size="7" onChange="testMe();">
		<?php echo $playlistoption;?>
		</select>	
		<p>
			<label>New Playlist Name*</label> <br />
			<input type="text" id="PlayListNameId" name="PlaylistName" max="50" placeholder="Enter a playlist name (50 characters)" style="width: 100%">
			</p>	
		
		<p>
		<button type="submit" name="btnSavelist" style="margin: 3px;">Save Shows</button>
		<button type="submit" name="btnDeletePlaylist" style="margin: 3px;">Delete Show</button>
		<button type="submit" name="btnPlaylist">Play</button>
		</p>
		
		</form>
		</form>
	</form>
	</div>
	</div>
</body>
</html>

