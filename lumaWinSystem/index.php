
<?php

include_once('commonFunctions.php');

$conn = getDatabaseConnectionLumaWin();


$left = "";
$right = "";

$results = mysqli_query($conn,"SELECT * FROM lumaWinHistory");
if(mysqli_num_rows($results) > 0)
{
    while($row = mysqli_fetch_array($results))
    {
		
	}
	
}

$conn->close();


?>


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Winslow's Illuminous LEDs - Home</title>
<script src="//kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php include("nav.php");  ?>
	
<h1>Home</h1>
<form>
	
		<p>Welcome to Winslow's Illuminous LEDs a powerful at home accent lightning company powered by raspberry pis.</p>
		
		<p>
		Each kit will come with everything needed to hang and set up your light show including the raspberry pi preloaded with several light shows already and down the line, you will be able to create your own shows based on your needs. These kits will need to be powered by 12 volts, which is not included in the kit. These kits are designed to work both indoors and outdoors.
		</p>
	
	</form>
	
	<!--<div class="timeline">
  <div class="container left">
    <div class="content">
      <h2>2017</h2>
      <p>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>2016</h2>
      <p>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</p>
    </div>
  </div>
  <div class="container left">
    <div class="content">
      <h2>2015</h2>
      <p>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>2012</h2>
      <p>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</p>
    </div>
  </div>
  <div class="container left">
    <div class="content">
      <h2>2011</h2>
      <p>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</p>
    </div>
  </div>
  <div class="container right">
    <div class="content">
      <h2>2007</h2>
      <p>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</p>
    </div>
  </div>
</div>
	<h2>About Us</h2>
	<div class="clearfix">
		<div class="column" style="width: 50%">
		
			<div class="ColumnStyles">
			<center><h3>Robert Winslow</h3>
			PICTURE</center>
				<P>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</P>
			</div>
			
		</div>
		
		<div class="column" style="width: 50%">
		
			<div class="ColumnStyles">
			<center><h3>Amanda Winslow</h3>
			PICTURE</center>
				
				<P>Lorem ipsum dolor sit amet, quo ei simul congue exerci, ad nec admodum perfecto mnesarchum, vim ea mazim fierent detracto. Ea quis iuvaret expetendis his, te elit voluptua dignissim per, habeo iusto primis ea eam.</P>
			
			</div>
			
		</div>-->
<!--	</div>-->
	
</body>
	<?php include("footer.php"); ?>
</html>

