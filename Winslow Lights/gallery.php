<?PHP

   include_once('commonFunctions.php');

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
<link href="css/Styles.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php include("nav.php");  ?>
<div class="column" style="width: 50%">

<?php echo $photoGallery ?>
	</div>
	
	<div class="column" style="width: 50%">
		
	<?php echo $videoGallery ?>
		</div>

</body>
	<?php include("footer.php"); ?>
</html>
