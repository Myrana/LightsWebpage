<?php include_once("commonFunctions.php"); ?>

<center><img src="Images/winslowBanner.png" alt="LumaWin LED Systems"></center>
	<nav>
	   <?php if($_SESSION['isAdmin'] == 1) 
	    echo '<ul><a href="registration.php" title="Registration"><i class="fas fa-user-plus" style="color: #FF0004"></i></a></ul>';
		echo '<ul><a href="configForm.php" title="Configuration"><i class="fas fa-cogs" style="color: #F97400"></i></a></ul>';
	  ?>
	  
	  <ul><a href="lightShows.php" title="Light Shows"><i class="fas fa-network-wired" style="color: #FFFD00"></i></a></ul>
	  <ul><a href="editShows.php" title="Edit Shows"><i class="fas fa-edit" style="color: #225900"></i></a></ul>
	  <ul><a href="editArt.php" title="Edit Art"><i class="fa fa-palette" style="color: #0200D4"></i></a></ul>
	  <ul><a href="artGallery.php" title="Art Gallery"><i class="far fa-images" style="color: #82CD1E"></i></a></ul>	
	 
	 <?php if($_SESSION['isAdmin'] == 1) 
		echo '<ul><a href="testPage.php" title="Test Page" ><i class="fas fa-exclamation-circle" style="color: #500887"></i></a></ul>';
	 ?>
	  <ul style="float: right"><a href="index.php" title="Login"><i class="fas fa-unlock-alt" style="color: #B312E7; margin-right: 20px"></i></a></ul>	
</nav>



