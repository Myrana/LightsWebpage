<?php

include_once('CommonFunctions.php');


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
		<input type="color"  Name="color_1" id="Color1" value ="#6512e0">
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
    <input type="checkbox" name="clearStart">
        <label for="On2">Clear on Finish</label>
    <input type="checkbox" name="clearFinish">
     <label for="On3">Gamma Correction</label>
    <input type="checkbox" name="gammaCorrection">
   
        <label for="On">Power On</label>
    <input type="checkbox" name="powerOn" value="OFF">
        </p>

        <p><button type="submit" name="LightShow">Send Show</button>
			<button type="submit" name="ClearQueue">Clear Queue</button></p>

    </div>
    </div>
