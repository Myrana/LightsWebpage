<?php
include_once("commonFunctions.php");


$_SESSION['authorized'] = 0;			
if(isset($_REQUEST['Login']))
{ 	
			
	$conn = getDatabaseConnection();
	$qry = "SELECT ID,isAdmin FROM lumaUsers WHERE username = '" . $_POST['Username'] . "' and password = '" . $_POST['Password'] ."' and authorized = 1";
	$row = mysqli_query($conn, $qry);
	if(mysqli_num_rows($row) == 1)
	{
		$query_data = mysqli_fetch_array($row);

		$_SESSION['LightSystemID'] = -1;
		$_SESSION['User'] = $_POST['Username'];
		$_SESSION['UserID'] = $query_data['ID'];
		$_SESSION['isAdmin'] = $query_data['isAdmin'];  
		$_SESSION['Brightness'] = 60;
		$_SESSION['matrixHTML'] = "";
		$_SESSION['ChgBrightness'] = 20;

		$_SESSION['Delay'] = 10;
		$_SESSION['Minutes'] = 1;
		$_SESSION['Width'] = 1;
		$_SESSION['ColorEvery'] = 2;
		
		$_SESSION['startRow'] = 6;
		$_SESSION['startColumn'] = 18;
		$_SESSION['radius'] = 4;
		
		$_SESSION['length'] = 5;
		$_SESSION['height'] = 5;
		$_SESSION['fill'] = 0;
		$_SESSION['ChannelId'] = 0;
		$_SESSION['position'] = 1;
		$_SESSION['direction'] = 1;
		$_SESSION['ShowName'] = 0; 
		
		$sysResults = mysqli_query($conn, "SELECT ID FROM lightSystems where userId =" . $_SESSION['UserID'] . " or userId = 1");
		if(mysqli_num_rows($sysResults) > 0)
		{
			$sysRow = mysqli_fetch_array($sysResults);
			$_SESSION['LightSystemID'] = $sysRow['ID'];
			

		}
		
		$_SESSION['authorized'] = 1;
		header('Location:lightShows.php');
	}
			
	$conn->close();		

}


?>

<!doctype html>
<?php 
include('header.php'); 
?>

<?php include("nav.php");  ?>

<body>
	<h1>Home</h1>
		<form name="login" id="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<p>
			<label>Username:</label> <br />
			<input type="text" name="Username" id="Username">
			</p>
			
			<p>
			<label>Password:</label> <br />
			<input type="password" name="Password" id="Password">
			</p>
			
			<button type="submit" name="Login">Login</button>
		</form>
</body>
	<?php include("footer.php"); ?>
</html>
