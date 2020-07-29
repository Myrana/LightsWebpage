<?php

include_once('commonFunctions.php');


$conn = getDatabaseConnection();

$lightShowsoption = '';
$_SESSION['lightShowsScript'] = '';
$results = mysqli_query($conn,"SELECT ID,showName,numColors,hasDelay,hasWidth, hasMinutes, colorEvery, isMatrix FROM lightShows WHERE enabled = 1 order by showOrder asc");
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
		$_SESSION['lightShowsScript'] .= "  show.isMatrix = " . $row['isMatrix'] .";\r";

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
		var isMatrix = document.getElementById("isMatrix");

        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		isMatrix.setAttribute('disabled', true);
		
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
		
		
		if(showMap.get(index).isMatrix == 1)
        {
            isMatrix.setAttribute('disabled', false);
            isMatrix.disabled = false;

        }
        
    }
</script>


<div class="column" style="width: 75%">
    <div class="ColumnStyles" style="width: 30%">
		
		<center><img src="Images/Show-Designer.png" alt="Show Designer"/></center>

    <center><p><label for="ShowName">Show name</label><br /><select id="ShowNameId" name="ShowName" onChange="setShowSettings();" style="width: 25%">
    <?php echo $lightShowsoption;?></select>
</p></center>
		<div class="clearfix">
<div style="width: 50%; float: left;">
    <p><label for="colors">Colors:</label>
		<input type="color"  Name="color_1" id="Color1" value ="#ff6500">
        <input type="color" Name="color_2" id="Color2" value="#906bfa">
        <input type="color" Name="color_3" id="Color3" value="#2c2367">
        <input type="color" Name="color_4" id="Color4" value="#ad5e8c"></p>

        <p><label for="Width">Width:</label>
<input type="number" id="WidthId" name="Width" min="1" max="300" value="<?php echo $_SESSION["Width"];?>"></p>

<p><label for="ColorEvery">X led:</label>
<input type="number" id="ColorEveryId" name="ColorEvery" min="1" max="300" value="<?php echo $_SESSION["ColorEvery"];?>">
</p>
</div>


<div style="width: 50%; float: left;">
        <p><label for="Delay">Delay:</label>
<input type="number" id="DelayId" name="Delay" min="1" max="100000" value="<?php echo $_SESSION["Delay"];?>">
</p>



    <p><label for="NumMinutes">Minutes:</label>
<input type="number" id="NumMinutesId" name="Minutes" min="1" value="<?php echo $_SESSION["Minutes"];?>">
</p>




<p><label for="Brightness">Brightness:</label>
    <input type="number" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="1" max="255">
</p>
</div>
	</div>

        <center>
			
			<p><input type="text" name="isMatrix" id="isMatrix" placeholder="Scrolling text" /></p>
			
			
			<p><label for="On1" style="font-size: 14px">Clear start</label>
    <input type="checkbox" name="clearStart" id="clearStart">
			<label for="On2" style="font-size: 14px">Clear finish</label>
    <input type="checkbox" name="clearFinish" id="clearFinish">
    
		<label for="On3" style="font-size: 14px">Gamma correction</label>
		<input type="checkbox" name="gammaCorrection" id="gammaCorrection"></p>
		</center>

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
        echo '<p style="margin-bottom: -14px;">
		<button onClick="addShowSettings();return false" name="AddShow">Add Show</button>
		</p>
		
		<p>
		<button onClick="saveShowSettings();return false" name="SaveShow">Store Changes</button>
			<button onClick="removeShowSettings();return false" name="RemoveShow">Remove Show</button>
			
		</p>';

			
    }
    ?>

    </div>
    </div>
