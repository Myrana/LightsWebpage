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

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Home</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="Styles.css" rel="stylesheet" type="text/css">
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
	
<h1>Home</h1>
	<form name="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<p><label>Username:</label> <br />
			<input type="text" name="Username"></p>
		<p><label>Password:</label> <br />
			<input type="password" name="Password">
		</p>
	<button type="submit" name="Login">Login</button>
	</form>
</body>
</html>
