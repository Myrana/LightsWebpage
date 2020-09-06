<?php  

include_once('commonFunctions.php');

if (!empty($_POST)) 
{

	$conn = getDatabaseConnection();
	$qry = "SELECT ID,isAdmin FROM lumaUsers";
	$row = mysqli_query($conn, $qry);
	if(mysqli_num_rows($row) == 0)
	{
		$sql = "INSERT INTO lumaUsers(username, password, isAdmin, authorized) VALUES('" . $_POST['username'] . "','" . $_POST['password'] . "', 1, 1)";

		if ($conn->query($sql) === TRUE) 
		{
		  echo "<h1>Admin User Added to the database successfully.</h1>";
		} else 
		{
		  echo "<h1>Error: " . $conn->error . "</h1>";
		}


	}
	else
	{
		 echo "<h1>Seems you have users in the system.</h1>";
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
	<h1>Setup Page</h1>
		<form name="Setup" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<p>
			<label for="username">Username:</label><br />
			<input name="username" type="text" id="username" placeholder="Admin UserID" maxlength="100">
			</p>

			<p>
			<label for="password">Password:</label><br />
			<input type="password" name="password" type="text" id="password" placeholder="Admin Password" maxlength="50">
			</p>

			<button type="submit" name="Submit">Setup</button>
		</form>
</body>
	<?php include("footer.php"); ?>
</html>
