<?php
include_once("commonFunctions.php");

if(isset($_REQUEST['Login']))
{ 
	$conn = getDatabaseConnection();
	$_SESSION['authorized'] = 0;
	$qry = "SELECT ID,isAdmin FROM lumaUsers WHERE username = '" . $_POST['Username'] . "' and password = '" . $_POST['Password'] ."' and authorized = 1";
	
	$row = mysqli_query($conn, $qry);
	if(mysqli_num_rows($row) == 1)
	{
		  $query_data = mysqli_fetch_array($row);
		  $_SESSION['authorized'] = 1;
		  $_SESSION['User'] = $_POST['Username'];
		  $_SESSION['UserID'] = $query_data['ID'];
		  $_SESSION['isAdmin'] = $query_data['isAdmin'];  
		  $_SESSION['Brightness'] = 60;
		  $_SESSION['matrixHTML'] = "";
		  $_SESSION['ChgBrightness'] = 20;
	  
		header('Location:lightShows.php');
	}

	$conn->close();
}
else if(isset($_SESSION['authorized']))
{
	killUserSession();
}



?>

<!doctype html>
<?php 
include('header.php'); 
?>

<body>
<?php include("nav.php");  ?>
	
<h1>Home</h1>
	<form name="login" id="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<p><label>Username:</label> <br />
			<input type="text" name="Username" id="Username"></p>
		<p><label>Password:</label> <br />
			<input type="password" name="Password" id="Password">
		</p>
	<button type="submit" name="Login">Login</button>
	</form>
</body>
	<?php include("footer.php"); ?>
</html>
