<?php  

include_once('commonFunctions.php');

if (!empty($_POST)) 
{
 
    $username= $_POST['username'];
    $password = $_POST['password'];
    $admin = $_POST['admin'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $twitter = $_POST['twitter'];

	//$sql = "INSERT INTO lightSystems(systemName, serverHostName, stripType, stripHeight, stripWidth, dma, gpio) VALUES('')

	//$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType) VALUES('" . '$lightSystemName' . "','" . '$serverHostName' . "', 257)";

	$sql = "INSERT INTO lumaUsers(username, password, email, phonenumber, twitter) VALUES('" . $_POST['username'] . "','" . $_POST['password'] . "', '" . $_POST['email'] . "','" . $_POST['phonenumber'] . "','" . $_POST['twitter'] . "')";


	if ($conn->query($sql) === TRUE) {
	  echo "<h1>Your record was added to the database successfully.</h1>";
	} else {
	  echo "<h1>Error: " . $conn->error . "</h1>";
	}

	$conn->close();


}

?>
	


<!doctype html>
<?php 
include('header.php'); 
?>

<body>

  <?php include("nav.php");  ?>
	  <h1>Registration Page</h1>
<form name="Registraition" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	<p><label for="username">Username:</label><br />
	  <input name="username" type="text" id="username" placeholder="50 characters or less" maxlength="100"></p>
	
	<p><label for="password">Password:</label><br />
	  <input name="password" type="text" id="password" placeholder="50 characters or less" maxlength="50"></p>
	
	<p><label for="admin">Is admin?:</label><br />
	  <input type="checkbox" id="admin" name="admin" value="admin">
		<label for="admin">Yes</label>
	
	</p>
	
	<p><label for="email">Email:</label><br />
	  <input name="email" type="text" id="email" placeholder="100 characters or less" maxlength="50"></p>
	
	<p><label for="phonenumber">Phone Number:</label><br />
	  <input name="phonenumber" type="text" id="phonenumber" placeholder="10 characters or less" maxlength="50"></p>
	
	<p><label for="twitter">Twitter:</label><br />
	  <input name="twitter" type="text" id="twitter" placeholder="50 characters or less" maxlength="50"></p>

	
<button type="submit" name="Submit">Add User</button>
</form>
</body>
	<?php include("footer.php"); ?>
</html>
