<!doctype html>
<?php 
include('header.php'); 

include_once('commonFunctions.php');

if($_SESSION['authorized'] == 0)
{
  header("Location: registration.php");
  exit();
}

$target_dir = "userArt/";
   

$files = scandir($target_dir, 1);
$imageList = "";

$counter = 0;
foreach($files as $file)
{
	$counter++;
	if(is_file($target_dir . $file ) == true)
		$imageList .= '<a href="' .  $target_dir . $file . '">' . $target_dir . $file . '</a><img src="' . $target_dir . $file . '" width="320" height="160"></image>';
		
	if($counter % 2 == 0)
		$imageList .= "<br>";
    
    
}


?>

<?php include("nav.php");  ?>
</head>

<body>
	
<?php echo $imageList;?>

<!--<img src=”image location” width=”1920” height=”1017” />	-->
	
</body>
<?php include("footer.php"); ?>
</html>
