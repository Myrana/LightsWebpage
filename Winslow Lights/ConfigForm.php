<?php

include_once('CommonFunctions.php');

$conn = getDatabaseConnection();


if($_SESSION['authorized'] == 0)
{
  header("Location: Registration.php");
  exit();
}



if(isset($_REQUEST['Config']))
{

    $motionChecked = 1;
     if (empty($_POST['motionFeature']))
       $motionChecked = 0;

      $lightChecked = 1;
     if (empty($_POST['lightFeature']))
       $lightChecked = 0;
	
	$timeChecked = 1;
     if (empty($_POST['timeFeature']))
     {
         $timeChecked = 0;
         $startTime = "";
         $endTime = "";
     }
    else
    {
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];
    }
    
	$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType,stripHeight, stripWidth, dma, gpio, brightness, enabled, userId, useMotionFeature, motionDelayOff, motionPlaylist, motionFeatureGpio, useLightFeature, lightPlaylist, lightFeatureGpio, useTimeFeature, timeFeatureStart, timeFeatureEnd, timePlaylist, gamma) VALUES('" . $_POST['LightSystemName'] . "','" . $_POST['ServerHostName'] . "', '" . $_POST['StripType'] . "','" . $_POST['StripHeight'] . "','" . $_POST['StripWidth'] . "','" . $_POST['DMA'] . "','" . $GPIO = $_POST['GPIO'] . "','" . $_POST['Brightness'] . "', '1', '" . $_POST['userID'] . "', '" . $motionChecked . "', '" . $_POST['motionDelayOff'] . "', '" . $_POST['motionPlaylist'] . "', '" . $_POST['motionFeatureGPIO'] . "', '" . $lightChecked . "', '" . $_POST['lightPlaylist'] . "', '" . $_POST['lightFeatureGPIO'] . "',  '" . $timeChecked . "', '" . $startTime . "', '" . $endTime . "',  '" . $_POST['timePlaylist'] . "', '" . $_POST['gamma'] . "')";

   
	if ($conn->query($sql) === TRUE)
    {
        echo "<h1>Your record was added to the database successfully.</h1>";
    }
	else 
	{
  	echo "<h1>Error: " . $conn->error . "</h1>";
	echo $sql;	
    }
}




    $displayStrip = mysqli_query($conn,"SELECT ID, stripName FROM lStripType");
    $stripTypes = '';
    while($query_data = mysqli_fetch_array($displayStrip))
    {
        //echo $query_data['stripName'];
        //<option>$query_data['stripName']</option>
        $stripTypes .="<option value = '".$query_data['ID']."'>".$query_data['stripName']."</option>";
    }





    $displayUsername = mysqli_query($conn,"SELECT ID, username FROM registrationTable ");
    $users = '';
    while($query_data = mysqli_fetch_array($displayUsername))
    {
        //echo $query_data['stripName'];
        //<option>$query_data['stripName']</option>
        $users .="<option value = '".$query_data['ID']."'>".$query_data['username']."</option>";
    }
	
$playlistoption = '';
$results = mysqli_query($conn,"SELECT ID, playlistName FROM userPlaylist");
if(mysqli_num_rows($results) > 0)
{
    while($row = mysqli_fetch_array($results))
      $playlistoption .="<option value = '".$row['ID']."'>".$row['playlistName']."</option>";

}

$systemlistoption = '';
$results = mysqli_query($conn,"SELECT ID, systemName FROM lightSystems");
if(mysqli_num_rows($results) > 0)
{
    while($row = mysqli_fetch_array($results))
      $systemlistoption .="<option value = '".$row['ID']."'>".$row['systemName']."</option>";

}

    $conn->close();

?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        $("#motionFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#motionFields").show();
            } else {
                $("#motionFields").hide();
            }
        });
    });
	
	$(function () {
        $("#lightFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#lightFields").show();
            } else {
                $("#lightFields").hide();
            }
        });
    });
	
		$(function () {
        $("#timeFeature").click(function () {
            if ($(this).is(":checked")) {
                $("#timeFields").show();
            } else {
                $("#timeFields").hide();
            }
        });
    });
</script>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Config Page</title>
    <!-- Bootstrap -->
	<link href="../css/bootstrap-4.4.1.css" rel="stylesheet">
	<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>	
	<link href="css/Styles.css" rel="stylesheet" type="text/css">
  </head>
 
<body>
<?php include("Nav.php");  ?>
	  <h1>Config Page</h1>
	<div class="column" style="width: 33%">
		<form name="Config Page" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		
			<p><label for="userID">Light System:</label><br />
			<select name="userID">
			<?php echo $systemlistoption;?>
			</select>	
	</p>
			<button type="submit" name="Config">Add Record</button> 
			<button type="submit" name="Edit">Edit Record</button>
		
		</div>
	
	          <div class="column" style="width: 33%">
				  <div class="ColumnStyles">
	
	<p><label for="LightSystemName">Light System Name:</label><br />
	  <input name="LightSystemName" type="text" id="LightSystemName" placeholder="100 characters or less" maxlength="100"></p>
	
	<p><label for="ServerHostName">Server Host Name:</label><br />
	  <input name="ServerHostName" type="text" id="ServerHostName" placeholder="50 characters or less" maxlength="50"></p>
	

	<p><label for="StripType">Strip Type:</label><br />
	<select name="StripType">
		<?php echo $stripTypes;?>
		</select>	
	</p>	
	



<p><label for="StripHeight">Strip Height:</label><br />
	  <input type="number" id="StripHeight" name="StripHeight" min="1" value="1"></p>

<p><label for="StripWidth">Strip Width:</label><br />
	  <input type="number" id="StripWidth" name="StripWidth" min="1" value"10"></p>

<p><label for="DMA">DMA:</label><br />
	  <select name="DMA">
		  <option value="5">5</option>
		  <option value="10">10</option>
		  <option value="12">12</option>
	  </select>


</p>

<p><label for="GPIO">GPIO Pin:</label><br />
	  <input type="number" id="GPIO" name="GPIO" min="1" max="52" value="18"></p>
	
	<p><label for="Brightness">Brightness:</label><br />
	  <input type="number" id="Brightness" name="Brightness" min="1" max="255" value="60">
		</p>
					  
	<p><label for="gamma">Gamma:</label><br />
	  <input type="number" id="gamma" name="gamma" step=".1" value="0">
		</p>
				  

	<p><label for="userID">Light System User:</label><br />
	<select name="userID">
		<?php echo $users;?>
		</select>	
	</p>	
	
	</div>
		</div>		  
<div class="column" style="width: 33%">
<div class="ColumnStyles">	
<p><label for="motionFeature">Use a motion sensor?</label>
	
	<input type="checkbox" id="motionFeature" /></p>
	
	<div id="motionFields" style="display: none">
	
		<label>Motion Delay:</label><br />
		<input type="number" id="motionDelay" name="motionDelayOff" min="5" value="10">
	<p>
	<label for="motionPlaylist">Motion Playlist:</label>
		<select id="PlayListId"  name="motionPlaylist">
        <?php echo $playlistoption;?>
        </select>

	
	</p>
	
	<P>
	
	<label for="motionFeatureGPIO">Motion GPIO Pin:</label><br />
	  <input type="number" id="motionFeatureGPIO" name="motionFeatureGPIO" min="1" max="52" value="18">	
	
	</P>
	
	</div>
	</div>
	<div class="ColumnStyles">
	
	<p><label for="lightFeature">Use a light sensor?</label>
	
	<input type="checkbox" id="lightFeature" /></p>
	
	<div id="lightFields" style="display: none">
	
		<label for="lightPlaylist">Light Playlist:</label>
		<select id="PlayListId"  name="lightPlaylist">
        <?php echo $playlistoption;?>
        </select>
		
		<P>
		
		<label for="lightFeatureGPIO">Motion GPIO Pin:</label><br />
	  <input type="number" id="lightFeatureGPIO" name="lightFeatureGPIO" min="1" max="52" value="18">
		
		</P>
	
	</div>
			  
</div>
	
	<div class="ColumnStyles">
		
		<p><label for="timeFeature">Use time of day?</label>
	
	<input type="checkbox" id="timeFeature" /></p>
		

		<div class="timeFields" style="display: none;">
	<label>Start Time:</label> <br />
		<input type="time" id="startTime" name="startTime" />
	<p>
		
	<label>End Time:</label> <br />
		<input type="time" id="endTime" name="endTime" />
		
	</p>
		<p>
		
			<label for="timePlaylist">Light Playlist:</label>
		<select id="PlayListId"  name="timePlaylist">
        <?php echo $playlistoption;?>
        </select>
		
		</p>
			</div>
	</div>
	</form>
	</div>
	
	
	
	
			  
				  
	

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
	<script src="../js/jquery-3.4.1.min.js"></script>

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="../js/popper.min.js"></script> 
	<script src="../js/bootstrap-4.4.1.js"></script>
  </body>

</html>
 
