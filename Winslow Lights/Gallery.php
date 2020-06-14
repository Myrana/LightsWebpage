<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
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

<body>
	<div class="responsive">
  <div class="gallery">
    <a target="_blank" href="Gallery Images/Sims 4/backyard.png">
      <img src="Gallery Images/Sims 4/backyard.png" alt="sims 4 backyard" width="1920" height="1017">
    </a>  </div>
</div>


<div class="responsive">
  <div class="gallery">
    <a target="_blank" href="Gallery Images/Sims 4/Beach.png">
      <img src="Gallery Images/Sims 4/Beach.png" alt="sims on a beach" width="1920" height="1017">
    </a>      </div>
</div>

<div class="responsive">
  <div class="gallery">
    <a target="_blank" href="Gallery Images/Sims 4/familyroom.png">
      <img src="Gallery Images/Sims 4/familyroom.png" alt="sims 4 family room" width="1920" height="1017">
    </a>  </div>
</div>

</body>
</html>