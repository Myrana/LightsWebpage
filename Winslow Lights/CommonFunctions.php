<?php

session_start();
$expireAfter = 30;

if(isset($_SESSION['last_action']))
{
    
    //Figure out how many seconds have passed
    //since the user was last active.
    $secondsInactive = time() - $_SESSION['last_action'];
    
    //Convert our minutes into seconds.
    $expireAfterSeconds = $expireAfter * 60;
    
    //Check to see if they have been inactive for too long.
    if($secondsInactive >= $expireAfterSeconds){
        //User has been inactive for too long.
        //Kill their session.
        session_unset();
        session_destroy();
		header("Location: index.php");
  		exit();
    }
    
}
 //Assings the current timestamp as last activity
$_SESSION['last_action'] = time();

$_SESSION['servername'] = "romoserver.local";
$_SESSION['username'] = "hellweek";
$_SESSION['password'] = "covert69guess";
$_SESSION['dbName'] = "LedLightSystem";


// Create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['username'], $_SESSION['password'] , $_SESSION['dbName']);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} 

function sendMQTT($arg_1, $arg_2)
{
	$client = new Mosquitto\Client();
	$client->connect('romoserver.local', 1883, 5);
	$client->loop();
	$mid = $client->publish($arg_1, $arg_2);
	$client->loop();
}

?>
