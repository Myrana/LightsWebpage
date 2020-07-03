<?php

include_once('CommonFunctions.php');

$conn = getDatabaseConnection();




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

<body>
	<?php include('Nav.php'); ?>
	<div class="clearfix">
	<div class="column" style="margin-top: 15px;"><label for="dad">Dad's Test select</label>
	<select id="dad" name="dad">
	<option id="dadd" value = "dad">Have fun!</option>
	
	</select>
		
		<p>
		
	<label for="dad2">Dad's Second Test select</label>
	<select id="dad2" name="dad2">
	<option id="dadd2" value = "dad2">Have fun Part 2!</option>
	
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
