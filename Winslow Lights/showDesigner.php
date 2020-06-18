<?php

include_once('CommonFunctions.php');

$_SESSION["Brightness"] = 20;
$_SESSION["LightSystemID"] = -1;
$_SESSION["Delay"] = 10;
$_SESSION["NumLoops"] = 1;
$_SESSION["Width"] = 1;
$_SESSION["ColorEvery"] = 2;

?>

<div class="column">
    <div class="ColumnStyles">
		
		<img src="Show-Designer.png" alt="Show Designer" width="100%" />

    <p><label for="ShowName">Show Name</label><br /><select id="ShowNameId" name="ShowName" onChange="setShowSettings();">
    <?php echo $lightShowsoption;?></select>
</p>

    <p><label for="colors">Colors:</label>
		<input type="color"  Name="color_1" id="Color1" value ="#6512e0">
        <input type="color" Name="color_2" id="Color2" value="#906bfa">
        <input type="color" Name="color_3" id="Color3" value="#2c2367">
        <input type="color" Name="color_4" id="Color4" value="#ad5e8c"></p>

        <p><label for="Width">Width:</label>
<input type="number" id="WidthId" name="Width" min="1" max="300" value="<?php echo $_SESSION["Width"];?>"><br />

<label for="ColorEvery">Color Every X Led:</label>
<input type="number" id="ColorEveryId" name="ColorEvery" min="1" max="300" value="<?php echo $_SESSION["ColorEvery"];?>">
</p>




        <p><label for="Delay">Delay:</label>
<input type="number" id="DelayId" name="Delay" min="1" max="100000" value="<?php echo $_SESSION["Delay"];?>">
</p>



    <p><label for="NumLoops">Number Of Loops:</label>
<input type="number" id="NumLoopsId" name="NumLoops" min="1" max="100000" value="<?php echo $_SESSION["NumLoops"];?>">
</p>




<p><label for="Brightness">Brightness:</label>
    <input type="number" value="<?php echo $_SESSION["Brightness"];?>" id="Brightness" name="Brightness" min="1" max="200">
</p>


        <p><label for="On">Clear on Start</label>
    <input type="checkbox" name="clearStart">
        <label for="On">Clear on Finish</label>
    <input type="checkbox" name="clearFinish">
   
        <label for="On">Power On</label>
    <input type="checkbox" name="powerOn" value="OFF">
        </p>

        <p><button type="submit" name="LightShow">Send Show</button>
			<button type="submit" name="ClearQueue">Clear Queue</button></p>

    </div>
    </div>
