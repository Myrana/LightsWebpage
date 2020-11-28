<?php  

include_once('commonFunctions.php');


$conn = getDatabaseConnection();

if(isset($_REQUEST['btnAddUser']))
{
 
    $authorized = 0;
    $isAdmin = 0;
    if(!empty($_POST['authorized']))
		$authorized = 1;
	
    if(!empty($_POST['admin']))
		$isAdmin = 1;
    	
	$sql = "INSERT INTO lumaUsers(username, password, isAdmin, authorized) VALUES('" . $_POST['username'] . "','" . $_POST['password'] . "', '" . $isAdmin . "','" . $authorized . "')";
	
	if ($conn->query($sql) === TRUE) 
	{
	  echo "<h1>User " . $_POST['username'] . " was Added successfully.</h1>";
	} 
	else 
	{
	  echo "<h1>Error: " . $conn->error . "</h1>";
	}

}


	
if(isset($_REQUEST['btnEditUser']))
{
 
    $authorized = 0;
    $isAdmin = 0;
    if(!empty($_POST['authorized']))
		$authorized = 1;
	
    if(!empty($_POST['admin']))
		$isAdmin = 1;
    	
	$sql = "update lumaUsers set username='" . $_POST['username'] . "',password='" . $_POST['password'] . "',isAdmin='" . $isAdmin . "', authorized='" . $authorized . "' where ID = '" . $_POST['userID'] . "'";
	
	if ($conn->query($sql) === TRUE) 
	{
	  echo "<h1>User " . $_POST['username'] . " was updated successfully.</h1>";
	} 
	else 
	{
	  echo "<h1>Error: " . $conn->error . "</h1>";
	}

}



	
if(isset($_REQUEST['btnRemoveUser']))
{
 
    $sql = "DELETE FROM lumaUsers WHERE ID ='" . $_POST['userID'] . "'";
	if ($conn->query($sql) != TRUE)
	{
		echo "<h1>Error: " . $conn->error . "</h1>";
		echo $sql;	
	}
	if ($conn->query($sql) === TRUE) 
	{
	  echo "<h1>User " . $_POST['username'] . " was Removed successfully.</h1>";
	} 
	else 
	{
	  echo "<h1>Error: " . $conn->error . "</h1>";
	}

}



$users = '';
$usersScript = "let usersMap = new Map();\r\n";;
$usersResults = mysqli_query($conn,'SELECT username,ID,password, authorized, isAdmin, defaultLightSystem FROM lumaUsers');
if(mysqli_num_rows($usersResults) > 0)
{
	while($userRow = mysqli_fetch_array($usersResults))
	{
		$usersScript .= "var user = new Object(); \r";
		$usersScript .= "    user.id = " . $userRow['ID'] . ";\r";
		$usersScript .= "    user.userName = '" . $userRow['username'] . "';\r";
		$usersScript .= "    user.password = '" . $userRow['password'] . "';\r";
		$usersScript .= "    user.authorized = " . $userRow['authorized'] . ";\r";
		$usersScript .= "    user.isAdmin = " . $userRow['isAdmin'] . ";\r";
		$usersScript .= "    user.defaultLightSystem = " . $userRow['defaultLightSystem'] . ";\r";	
		$usersScript .= "usersMap.set(" . $userRow['ID'] . ", user);\r";
				 
		$users .="<option value = '".$userRow['ID']."'>".$userRow['username']."</option>";
		
	}
}

$systemlistoption = "";
$systemResults = mysqli_query($conn,'SELECT ID, systemName from lightSystems');
if(mysqli_num_rows($systemResults) > 0)
{
	while($systemRow = mysqli_fetch_array($systemResults))
	{
				 
		$systemlistoption .= "<option value = '".$systemRow['ID']."'>".$systemRow['systemName']."</option>";
		
	}
}


$conn->close();

?>
	


<!doctype html>
<?php 
include('header.php'); 
include('nav.php');
?>



<body onLoad="setUserInfo();">
	
	<script>

	<?php echo $usersScript;?>

	

	function setUserInfo()
	{
		var userID = document.getElementById("userID");
		var userName = document.getElementById("username");
		var password = document.getElementById("password");
		var lightSystem = document.getElementById("LightSystem");
		var admin = document.getElementById("admin");
		var authorized = document.getElementById("authorized");
		
		
		var user = usersMap.get(parseInt(userID.value));
		
		admin.checked = user.isAdmin;
		authorized.checked = user.authorized;
		
		userName.value = user.userName;
		password.value = user.password;
		lightSystem.value = user.defaultLightSystem;
		
			

	}

	</script>



	<h1>Users Page</h1>
	<div class="column twenty-five">
		<form name="Users" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<p>
			<label for="userID">Light System User:</label><br />
			<select name="userID" id="userID" onchange="setUserInfo()">
			<?php echo $users;?>
			</select>	
			</p>
	</div>
	<div class="column seventy-five">
	<div class="ColumnStyles">	
			<p>
			<label for="username">Username:</label><br />
			<input name="username" type="text" id="username" placeholder="50 characters or less" maxlength="100">
			</p>

			<p>
			<label for="password">Password:</label><br />
			<input name="password" type="password" id="password" placeholder="50 characters or less" maxlength="50">
			</p>
			
			<p><label for="onLightSystem">Light System:</label><br />
			<select name="LightSystem" id="LightSystem">
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

			<button type="submit" name="btnAddUser">Add User</button>
			<button type="submit" name="btnEditUser">Edit User</button>
			<button type="submit" name="btnRemoveUser">Remove User</button>
		</form>
	</div>
	</div>
</body>
	<?php include("footer.php"); ?>
</html>
