<?php

if (!empty($_POST))
{ 
	$test = $_POST['test'];

	$client = new Mosquitto\Client();
	$client->connect("Romoserver.local", 1883, 5);
	$client->loop();
	$mid = $client->publish('patio1.local', 'ON');
	$client->loop();
	echo "true";
}
else
{
	echo "false";
}


?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Test page</title>
<script src="https://kit.fontawesome.com/4717f0a393.js" crossorigin="anonymous"></script>
<link href="css/Styles.css" rel="stylesheet" type="text/css">	
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
        <form name="testform" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

		
	<p><label for="test">test area:</label><br />
		<textarea name="test" rows="4" cols="50"></textarea></p>
		<p><button type="submit" name="Submit">Test Button</button></p>
	
		</form>
	
	
</body>
</html>
