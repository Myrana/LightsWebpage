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
    	
	$sql = "INSERT INTO lumaUsers(username, password, isAdmin, authorized) VALUES('" . $_POST['username'] . "','" . $_POST['password'] . "', '" . $isAdmin . "','" . $authorized . "')";
	
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
<?php 

$displayUsername = mysqli_query($conn,"SELECT ID, username FROM lumaUsers ");
$users = '';
while($query_data = mysqli_fetch_array($displayUsername))
{
    $users .="<option value = '".$query_data['ID']."'>".$query_data['username']."</option>";
}


?>
<body>
	<h1>Users Page</h1>
	<div class="column twenty-five">
		<form name="Users" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<p>
			<label for="userID">Light System User:</label><br />
			<select name="userID" id="userID">
			<?php echo $users;?>
			</select>	
			</p>
	</div>
	<div class="column seventy-five ColumnStyles">
			<p>
			<label for="username">Username:</label><br />
			<input name="username" type="text" id="username" placeholder="50 characters or less" maxlength="100">
			</p>

			<p>
			<label for="password">Password:</label><br />
			<input name="password" type="text" id="password" placeholder="50 characters or less" maxlength="50">
			</p>
			
			<p><label for="onLightSystem">Light System:</label><br />
			<select name="LightSystem" id="LightSystem" onChange="setLightSystemSettings(false);">
			<?php echo $systemlistoption;?>
			</select>	
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

			<button type="submit" name="Submit">Add User</button>
			<button type="submit" name="editUser">Edit User</button>
			<button type="submit" name="removeUser">Remove User</button>
		</form>
	</div>
</body>
	<?php include("footer.php"); ?>
</html>
