
<?php

include_once('CommonFunctions.php');

?>


<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Winslow's Illuminous LEDs - Home</title>
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
	<form>
	
		<p>Welcome to Winslow's Illuminous LEDs a powerful at home accent lightning company powered by raspberry pis.</p>
		
		<p>
		Each kit will come with everything needed to hang and set up your light show including the raspberry pi preloaded with several light shows already and down the line, you will be able to create your own shows based on your needs. These kits will need to be powered by 12 volts, which is not included in the kit. These kits are designed to work both indoors and outdoors.
		</p>
	
	</form>
</body>
</html>

