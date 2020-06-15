<?PHP

   include_once('CommonFunctions.php');

    $photoGallery = "";
	$videoGallery = "";
    $conn = getDatabaseConnection();
     $results = mysqli_query($conn,"SELECT description, path, isVideo  FROM productMedia WHERE enabled = 1");
         if(mysqli_num_rows($results) > 0)
         {

            while($row = mysqli_fetch_array($results))
            {
                if($row["isVideo"] == 0)
                {
                    
                    $photoGallery .= '<div class="responsive"><div class="gallery">';
                    $photoGallery .= '<a target="_blank" href="' . $row["path"] . '">';
                    $photoGallery .= '<img src="' . $row['path'] . '" alt="' . $row['description'] . '" width="1920" height="1017">';

                    $photoGallery .= '</a></div></div>';
                }
                else
                {
                    $videoGallery .= '<div class="responsive"><div class="gallery">';
                    $videoGallery .= '<video width="400" controls autoplay>';
                    $videoGallery .= '<source src="' . $row['path'] . '" type="video/mp4">';
                    $videoGallery .= '</video></div></div>';

                }

            }

         }

?>

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
<div class="column" style="width: 50%">

<?php echo $photoGallery ?>
	</div>
	
	<div class="column" style="width: 50%">
		
	<?php echo $videoGallery ?>
		</div>

</body>
</html>
