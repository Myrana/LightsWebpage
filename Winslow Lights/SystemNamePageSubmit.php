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


$systemName = $_POST['SystemName'];

$onoff = "ON";
if (empty($_POST['lights']))
	$onoff = "OFF";
//$systemName = $_POST['Lights'];

foreach($_POST['ShowName'] as $selectedOption)
{

       $showArray[] = $selectedOption;
      // echo $selectedOption;
}

$sendArray['state'] = $onoff;;
$sendArray['shows'] = $showArray;
$sendArray['brightness'] = $_POST['Brightness'];

echo json_encode($showArray);
echo json_encode($sendArray);


$displayStrip = mysqli_query($conn,"SELECT serverHostName FROM lightSystems WHERE ID = ".$systemName);
$option = '';
$query_data = mysqli_fetch_array($displayStrip);

$client = new Mosquitto\Client();
$client->connect('romoserver.local', 1883, 5);
$client->loop();
$mid = $client->publish($query_data['serverHostName'], json_encode($sendArray));
$client->loop();



?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
	<span><?php echo $_POST['SystemName']; ?></span>
	<span><?php echo $onoff; ?></span>
	<?php 
	foreach($_POST['ShowName'] as $selectedOption)
		echo $selectedOption. "\n"; 
	
	?>
</body>
</html>
