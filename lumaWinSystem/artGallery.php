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
foreach($files as $file)
{
	if(is_file($target_dir . $file ) == true)
		$imageList .= '<a target="_blank" href="' . $target_dir . $file . '"><img src="' . $target_dir . $file . '" width="320" height="160" /></a>';
    
    
}
home/hellweek/code/uploadArt
//print_r($files);


?>

<?php include("nav.php");  ?>
</head>

<body>
	
<?php echo $imageList;?>


	
</body>
<?php include("footer.php"); ?>
</html>
