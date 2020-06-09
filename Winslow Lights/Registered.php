<?php  

$servername = "romoserver.local";
$username = "hellweek";
$password = "covert69guess";
$dbName = "LedLightSystem";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbName);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} 

if (isset($_POST['submit'])) {
    $username= $_POST['username'];
	$password = $_POST['password'];
	$admin = $_POST['admin'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
	$twitter = $_POST['twitter'];
	
}

//$sql = "INSERT INTO lightSystems(systemName, serverHostName, stripType, stripHeight, stripWidth, dma, gpio) VALUES('')

//$sql = "INSERT INTO lightSystems(systemName,serverHostName, stripType) VALUES('" . '$lightSystemName' . "','" . '$serverHostName' . "', 257)";

$sql = "INSERT INTO registrationTable(username, password, email, phonenumber, twitter) VALUES('" . $_POST['username'] . "','" . $_POST['password'] . "', '" . $_POST['email'] . "','" . $_POST['phonenumber'] . "','" . $_POST['twitter'] . "')";


if ($conn->query($sql) === TRUE) {
  echo "<h1>Your record was added to the database successfully.</h1>";
} else {
  echo "<h1>Error: " . $conn->error . "</h1>";
}

$conn->close();

	

?>
	

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>User Submitted</title>
</head>

<script>
function includeHTML() {
  var z, i, elmnt, file, xhttp;
  /*loop through a collection of all HTML elements:*/
  z = document.getElementsByTagName("*");
  for (i = 0; i < z.length; i++) {
    elmnt = z[i];
    /*search for elements with a certain atrribute:*/
    file = elmnt.getAttribute("w3-include-html");
    if (file) {
      /*make an HTTP request using the attribute value as the file name:*/
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4) {
          if (this.status == 200) {elmnt.innerHTML = this.responseText;}
          if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
          /*remove the attribute, and call this function once more:*/
          elmnt.removeAttribute("w3-include-html");
          includeHTML();
        }
      }      
      xhttp.open("GET", file, true);
      xhttp.send();
      /*exit the function:*/
      return;
    }
  }
};
</script>
<body>
	<div w3-include-html="Nav.html"></div>
	
<script>
includeHTML();
</script>
	<label>Username:</label>
	<span><?php echo $_POST['username']; ?></span> <br />
	
	<label>Password:</label>
	<span><?php echo $_POST['password']; ?></span> <br />
	
	<label>Email:</label>
	<span><?php echo $_POST['email']; ?></span> <br />
	
	<label>Phone Number:</label>
	<span><?php echo $_POST['phonenumber']; ?></span> <br />
	
	<label>Twitter:</label>
	<span><?php echo $_POST['twitter']; ?></span> <br />
	
	
</body>
</html>
</body>
</html>