<img src="winslowBanner.png" alt="Winslow's Illuminous LEDs" width="100%">
	<nav>
	  <ul><a href="index.php" title="Home" ><i class="fas fa-home" style="color: #34eb86"></i></a></ul>
	  <ul><a href="Gallery.php" title="Gallery" style="color: cornflowerblue"><i class="fas fa-images"></i></a></ul>
	  <ul><a href="shopIndex.php" title="Shop" style="color: deeppink"><i class="fas fa-shopping-bag"></i></a></ul>	
	  <ul><a href="ConfigForm.php" title="Configuration"><i class="fas fa-cogs" style="color: #ff3399"></i></a></ul>
	  <ul><a href="Registration.php" title="Registration"><i class="fas fa-user-plus" style="color: #ffff00"></i></a></ul>
	  <ul><a href="TestPage.php" title="Test Page" ><i class="fas fa-exclamation-circle" style="color: #6beb34"></i></a></ul>
	  <ul><a href="SystemManager.php" title="System Manager"><i class="fas fa-network-wired" style="color: #cc33ff"></i></a></ul>	
	  <ul><a href="productMarketing.php" title="ProductMarketing"><i class="fas fa-user-shield" style="color: khaki"></i></a></ul>	
	  <ul style="float: right"><button id="btnLogin" style="background-color: transparent; border: none;"><i class="fas fa-unlock-alt" style="color: #ff0000"></i> Login |<i class="fas fa-user" style="color: aquamarine"></i> Register</button></ul>	
</nav>

<div id="LoginModal" class="Loginmodal">

  <!-- Modal content -->
  <div class="Loginmodal-content">
    <span class="close">&times;</span>
    <form name="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<p><label>Username:</label> <br />
			<input type="text" name="Username"></p>
		<p><label>Password:</label> <br />
			<input type="password" name="Password">
		</p>
	<button type="submit" name="Login">Login</button>
	</form>
	  
	  <?php
include_once("CommonFunctions.php");

if(isset($_REQUEST['Login']))
{ 
	$conn = getDatabaseConnection();
	$_SESSION['authorized'] = 0;
	$qry = "SELECT ID,isAdmin FROM registrationTable WHERE username = '" . $_POST['Username'] . "' and password = '" . $_POST['Password'] ."' and authorized = 1";
	
	$row = mysqli_query($conn, $qry);
	if(mysqli_num_rows($row) == 1)
	{
	  $query_data = mysqli_fetch_array($row);
	  $_SESSION['authorized'] = 1;
	  $_SESSION['User'] = $_POST['Username'];
	  $_SESSION['UserID'] = $query_data['ID'];
	  $_SESSION['isAdmin'] = $query_data['isAdmin'];  
	  echo "Hello, " . $_SESSION['User'];
	}

	$conn->close();
}
else if(isset($_SESSION['authorized']))
{
	killUserSession();
}



?>
	  
  </div>

</div>

<script>
// Get the modal
var modal = document.getElementById("LoginModal");

// Get the button that opens the modal
var btn = document.getElementById("btnLogin");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

