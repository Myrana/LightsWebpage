<?php  

include_once('commonFunctions.php');

if (!empty($_POST)) 
{
 
     
	$conn = getDatabaseConnection();
 
    $authorized = 0;
    $isAdmin = 0;
    if(!empty($_POST['authorized']))
		$authorized = 1;
	
    if(!empty($_POST['admin']))
		$isAdmin = 1;
    	
	$sql = "INSERT INTO lumaUsers(username, password, email, phonenumber, twitter, isAdmin, authorized) VALUES('" . $_POST['username'] . "','" . $_POST['password'] . "', '" . $_POST['email'] . "','" . $_POST['phonenumber'] . "','" . $_POST['twitter'] . "','" . $isAdmin . "','" . $authorized . "')";
	
	if ($conn->query($sql) === TRUE) 
	{
	  echo "<h1>Your record was added to the database successfully.</h1>";
	} 
	else 
	{
	  echo "<h1>Error: " . $conn->error . "</h1>";
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
	<h1>Registration Page</h1>
		<form name="Registraition" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
			<p>
			<label for="username">Username:</label><br />
			<input name="username" type="text" id="username" placeholder="50 characters or less" maxlength="100">
			</p>

			<p>
			<label for="password">Password:</label><br />
			<input name="password" type="text" id="password" placeholder="50 characters or less" maxlength="50">
			</p>

			<p>
			<label for="admin">Is admin?:</label><br />
			<input type="checkbox" id="admin" name="admin" >
			<label for="admin">Yes</label>
			</p>

			<p>
			<label for="authorized">Authorized:</label><br />
			<input type="checkbox" id="authorized" name="authorized">
			<label for="authorized">Yes</label>
			</p>
			

			<p>
			<label for="email">Email:</label><br />
			<input name="email" type="text" id="email" placeholder="100 characters or less" maxlength="50">
			</p>

			<p>
			<label for="phonenumber">Phone Number:</label><br />
			<input name="phonenumber" type="text" id="phonenumber" placeholder="10 characters or less" maxlength="50">
			</p>

			<p>
			<label for="twitter">Twitter:</label><br />
			<input name="twitter" type="text" id="twitter" placeholder="50 characters or less" maxlength="50">
			</p>

			<button type="submit" name="Submit">Add User</button>
		</form>
</body>
	<?php include("footer.php"); ?>
</html>
