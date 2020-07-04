<?php

include_once('CommonFunctions.php');


$conn = getDatabaseConnection();

$lightShowsoption = '';
$_SESSION['lightShowsScript'] = '';
$results = mysqli_query($conn,"SELECT ID,showName,numColors,hasDelay,hasWidth, hasMinutes, colorEvery FROM lightShows WHERE enabled = 1 order by showOrder asc");
if(mysqli_num_rows($results) > 0)
{
    $_SESSION['lightShowsScript'] .= "let showMap = new Map();\r";

    while($row = mysqli_fetch_array($results))
    {
        
        $lightShowsoption .="<option value = '".$row['ID']."'>".$row['showName']."</option>";


        $_SESSION['lightShowsScript'] .= "var show = new Object(); \r";

        $_SESSION['lightShowsScript'] .= "    show.id = " . $row['ID'] .";\r";
        $_SESSION['lightShowsScript'] .= "    show.showName = '" . $row['showName'] ."';\r";
        $_SESSION['lightShowsScript'] .= "    show.numColors = " . $row['numColors'] .";\r";
        $_SESSION['lightShowsScript'] .= "    show.hasDelay = " . $row['hasDelay'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.hasWidth = " . $row['hasWidth'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.hasMinutes = " . $row['hasMinutes'] .";\r";
        $_SESSION['lightShowsScript'] .= "  show.colorEvery = " . $row['colorEvery'] .";\r";

        $_SESSION['lightShowsScript'] .= "    showMap.set(" . $row['ID'] . ", show);\r";




    }

}

$conn->close();

?>

<script>

    <?php echo $_SESSION['lightShowsScript'];?>


    function setShowSettings()
    {

        var showNameId = document.getElementById("ShowNameId");
        var index = parseInt(showNameId.value);


        var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");

        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		
        if(showMap.get(index).hasWidth == 1)
        {
            width.setAttribute('disabled', false);
            width.disabled = false;

        }

        if(showMap.get(index).hasMinutes == 1)
        {
            minutes.setAttribute('disabled', false);
            minutes.disabled = false;
        }

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
        
        
        if(showMap.get(index).colorEvery == 1)
        {
            colorEvery.setAttribute('disabled', false);
            colorEvery.disabled = false;
        }
        
    }
</script>


<div class="column">
    <div class="ColumnStyles">
		
		<img src="Images/Show-Designer.png" alt="Show Designer" width="100%" />

    <p><label for="ShowName">Show Name</label><br /><select id="ShowNameId" name="ShowName" onChange="setShowSettings();">
    <?php echo $lightShowsoption;?></select>
</p>

    <p><label for="colors">Colors:</label>
		<input type="color"  Name="color_1" id="Color1" value ="#ff6500">
        <input type="color" Name="color_2" id="Color2" value="#906bfa">
        <input type="color" Name="color_3" id="Color3" value="#2c2367">
        <input type="color" Name="color_4" id="Color4" value="#ad5e8c"></p>

        <p><label for="Width">Width:</label>
<input type="number" id="WidthId" name="Width" min="1" max="300" value="<?php echo $_SESSION["Width"];?>"></p>

<p><label for="ColorEvery">Color Every X Led:</label>
<input type="number" id="ColorEveryId" name="ColorEvery" min="1" max="300" value="<?php echo $_SESSION["ColorEvery"];?>">
</p>




        <p><label for="Delay">Delay:</label>
<input type="number" id="DelayId" name="Delay" min="1" max="100000" value="<?php echo $_SESSION["Delay"];?>">
</p>



    <p><label for="NumMinutes">Number Of Minutes:</label>
<input type="number" id="NumMinutesId" name="Minutes" min="1" value="<?php echo $_SESSION["Minutes"];?>">
</p>




<p><label for="Brightness">Brightness:</label>
    <input type="number" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="1" max="255">
</p>


        <p><label for="On1">Clear on Start</label>
    <input type="checkbox" name="clearStart" id="clearStart">
        <label for="On2">Clear on Finish</label>
    <input type="checkbox" name="clearFinish" id="clearFinish">
     <label for="On3">Gamma Correction</label>
    <input type="checkbox" name="gammaCorrection" id="gammaCorrection">
   
        <label for="On">Power On</label>
    <input type="checkbox" name="powerOn" id="powerOn" value="OFF">
        </p>

<?php
    if($_SESSION["DesignerEditMode"]  == 0)
    {
        echo '<p>
			<button type="submit" name="LightShow">Send Show</button>
			<button type="submit" name="ClearQueue">Clear Queue</button>
        </p>';
    }
    else
    {
        echo '<p>
		<button onClick="addShowSettings();return false" name="AddShow">Add Show</button>
		</p>
		
		<p>
		<button onClick="saveShowSettings();return false" name="SaveShow">Save Show Settings</button>
			<button onClick="removeShowSettings();return false" name="RemoveShow">Remove Show Settings</button>
		</p>
		
		<p>
		<button type="submit" onClick="savePlayList();" name="CommitPlayList" id="CommitPlayList">Commit PlayList</button>
		</p>';
			
    }
    ?>

    </div>
    </div>
