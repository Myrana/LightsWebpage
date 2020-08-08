<?php

include('commonFunctions.php');


$_SESSION["DesignerEditMode"] = 0;

$conn = getDatabaseConnection();

if($_SESSION['authorized'] == 0)
{
  header("Location: registration.php");
  exit();
}

$sendArray['UserID'] = "";
if(!empty($_REQUEST))
{
    $sendArray['UserID'] = $_SESSION['UserID'];
    
    if(!empty($_POST['SystemName']))
        $_SESSION['LightSystemID']  = $_POST['SystemName'];

	if(!empty($_POST['ChannelId']))
		$_SESSION['ChannelId'] = $_POST['ChannelId'];

    if(!empty($_POST['Brightness']))
        $_SESSION['Brightness'] = $_POST['Brightness'];

    if(!empty($_POST['Delay']))
        $_SESSION['Delay'] = $_POST['Delay'];

    if(!empty($_POST['Minutes']))
        $_SESSION['Minutes'] = $_POST['Minutes'];

    if(!empty($_POST['Width']))
        $_SESSION['Width'] = $_POST['Width'];

    if(!empty($_POST['ColorEvery']))
        $_SESSION["ColorEvery"] = $_POST['ColorEvery'];

    if(!empty($_POST['ChgBrightness']))
        $_SESSION['ChgBrightness'] = $_POST['ChgBrightness'];

    if(!empty($_POST['ShowName']))
        $_SESSION['ShowName'] = $_POST['ShowName'];
	
	if(!empty($_POST['startRow']))
		$_SESSION['startRow'] = $_POST['startRow'];
	
	if(!empty($_POST['startColumn']))
		$_SESSION['startColumn'] = $_POST['startColumn'];
	
	if(!empty($_POST['radius']))
		$_SESSION['radius'] = $_POST['radius'];
	
	if(!empty($_POST['length']))
		$_SESSION['length'] = $_POST['length'];
	
	if(!empty($_POST['height']))
		$_SESSION['height'] = $_POST['height'];
	
	if(!empty($_POST['fill']))
		$_SESSION['fill'] = 1;
	else
		$_SESSION['fill'] = 0;

	if(!empty($_POST['position']))
		$_SESSION['position'] = $_POST['position'];
}

if(isset($_REQUEST['Power']))
{

    $onoff = "ON";
    if (empty($_POST['lights']))
      $onoff = "OFF";

    $sendArray['state'] = $onoff;
    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));

}


if(isset($_REQUEST['btnChgBrightness']))
{

	if(!empty($_POST['ChgBrightness']))
	{

		$_SESSION['ChgBrightness'] = $_POST['ChgBrightness'];
		$sendArray['chgBrightness'] = $_POST['ChgBrightness'];

		sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
	}

}




if(isset($_REQUEST['ClearQueue']))
{

    $sendArray['clearQueue'] = 1;
    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));

}




if(isset($_REQUEST['LightShow']))
{

    $r = 5;
    $g = 3;
    $b = 12;
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

        if(!empty($_POST['ShowName']))
            $sendArray['show'] =  $_POST['ShowName'];

		
		$sendArray['brightness'] = $_SESSION["Brightness"];
		$sendArray['channelId'] = $_SESSION["ChannelId"];
		
		

        if(!empty($_POST['Delay']))
            $sendArray['delay'] = $_SESSION['Delay'];

        if(!empty($_POST['Minutes']))
            $sendArray['minutes'] = $_SESSION['Minutes'];

        if(count($sendColors) > 0)
           $sendArray['colors'] = $sendColors;
        
        if (!empty($_POST['clearStart']))
            $sendArray['clearStart'] = 1;

        if (!empty($_POST['Width']))
            $sendArray['width'] = $_POST['Width'];

        if (!empty($_POST['clearFinish']))
            $sendArray['clearFinish'] = 1;
            
        if (!empty($_POST['gammaCorrection']))
			$sendArray['gammaCorrection'] = 1;

        if (!empty($_POST['powerOn']))
           $sendArray['powerOn'] = "OFF";
           
        if (!empty($_POST['ColorEvery']))
           $sendArray['colorEvery'] = $_SESSION['ColorEvery'];
           
        if(!empty($_POST['hasText']))
			$sendArray['matrixText'] = $_POST['hasText'];
			
		if(!empty($_POST['position']))
			$sendArray['position'] = $_POST['position'];
		
		if(!empty($_POST['startRow']))
			$sendArray['startRow'] = $_POST['startRow'];
		
		if(!empty($_POST['startColumn']))
			$sendArray['startColumn'] = $_POST['startColumn'];
		
		if(!empty($_POST['radius']))
			$sendArray['radius'] = $_POST['radius'];
		
		if(!empty($_POST['length']))
			$sendArray['len'] = $_POST['length'];
		
		if(!empty($_POST['height']))
			$sendArray['height'] = $_POST['height'];
		
		if(!empty($_POST['fill']))
			$sendArray['fill'] = 1;
		
		
			
		if(!empty($_POST['matrixData']))
			$sendArray['pixles'] = json_decode($_POST['matrixData']);
		
		
		
		
        $sendArray['systemId'] = $_SESSION['LightSystemID'];
       
       
		
		if(!empty($_POST['saveArt']) && !empty($_POST['saveArtName']))
		{
		   $sql = "insert into matrixArt(userID, artName, showParms, enabled) values(". $_SESSION['UserID'] . ",'" . $_POST['saveArtName'] . "','" . json_encode($sendArray) . "','1')" ;
		   if ($conn->query($sql) != TRUE)
			{
				echo "<h1>Ooops Error Saving Art!: " . $conn->error . "</h1>";
				echo $sql;	
			}
		}
		
		sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
		
		
		
    }
    
}


if(isset($_REQUEST['btnPlayArtShow']))
{
	$sendArray['playArtShow'] = 1;
	$sendArray['artShowId'] = intval($_POST['PlayArtShow']);
	$sendArray['UserID'] = intval($_SESSION['UserID']);
	sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
}



if(isset($_REQUEST['btnPlaylist']))
{
	if(!empty($_POST['Playlist']))
	{
		$sendArray['playPlaylist'] = 1;
		$sendArray['playlistName'] = intval($_POST['Playlist']);
		$sendArray['UserID'] = intval($_SESSION['UserID']);
	    sendMQTT(getServerHostName($_SESSION["LightSystemID"]), json_encode($sendArray));
		
	}
}






$playlistoption = '';
$results = mysqli_query($conn,"SELECT ID, playlistName FROM userPlaylist where userId =" . $_SESSION['UserID'] . " or userId =1");
if(mysqli_num_rows($results) > 0)
{
    while($row = mysqli_fetch_array($results))
      $playlistoption .="<option value = '".$row['ID']."'>".$row['playlistName']."</option>";

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


$conn->close();

?>

<!doctype html>
<?php 
include('header.php'); 
?>

<?php include("nav.php");  ?>

<body>
<div class="clearfix">
	<div class="column twenty-five">
    	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<center><img src="System-Control.png" id="systemControlPic" alt="System Control"/></center>
   				<p>
				<label for="ChgBrightness">Change Brightness:</label>
        		<input type="number" value="<?php echo $_SESSION["ChgBrightness"];?>" id="ChgBrightnessId" name="ChgBrightness" min="1" max="255">
        		<button type="submit" name="btnChgBrightness">Change</button>
    			</p>
			
				<label for="On">On</label>
				<input type="checkbox" name="lights"  value="ON" checked>
				<button type="submit" name="Power">Power</button>
				<button type="submit" name="ClearQueue">Clear Queue</button>
		
				
<script>

	setSystemSettings();
	function setPlaylistName()
	{
		var playlistName = document.getElementById("PlayListNameId");
		var playListId = document.getElementById("PlayListId");
		var selectedText = playListId.options[playListId.selectedIndex].text;
		playlistName.value = selectedText;


	}
</script>
		<p>		
				<label>Playlist</label>
				<select id="PlayListId"  name="Playlist" onChange="setPlaylistName();"><?php echo $playlistoption;?></select>
				<button type="submit" name="btnPlaylist">Play</button>
				<p>
				<label>Art</label>
				<select id="PlayArtShow"  name="PlayArtShow" ><?php echo $_SESSION['userArtOptions'];?></select>
				<button type="submit" name="btnPlayArtShow">Play Art</button>
				</p>			
				<p>		
				<label>Playlist Editor</label>	
				<button onclick="location.href='editShows.php'; return false" name="btnEditist">Editor</button>
				</p>
			
		</p>
		
	</div>
		
<?php include_once('showDesigner.php'); ?>

 </form>
       	
</body>
</html>

