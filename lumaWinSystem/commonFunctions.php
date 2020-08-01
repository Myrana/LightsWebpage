<?php

session_start();
$expireAfter = 120;

if(isset($_SESSION['last_action']))
{
    
    //Figure out how many seconds have passed
    //since the user was last active.
    $secondsInactive = time() - $_SESSION['last_action'];
    
    //Convert our minutes into seconds.
    $expireAfterSeconds = $expireAfter * 60;
    
    //Check to see if they have been inactive for too long.
    if($secondsInactive >= $expireAfterSeconds)
    {
      killUserSession();
    }
    
}
 //Assings the current timestamp as last activity
$_SESSION['last_action'] = time();

$_SESSION['servername'] = "romoserver.local";
$_SESSION['username'] = "hellweek";
$_SESSION['password'] = "covert69guess";
$_SESSION['dbName'] = "LedLightSystem";


function getDatabaseConnection()
{
	// Create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['username'], $_SESSION['password'] , $_SESSION['dbName']);
	// Check connection
	if ($conn->connect_error) 
	{
	  die("Connection failed: " . $conn->connect_error);
	} 
	
	return $conn;
}


function getServerHostName($arg_1)
{
	$retVal = "localhost";
	$con = getDatabaseConnection();
	$results = mysqli_query($con ,"SELECT serverHostName FROM lightSystems WHERE ID = ".$arg_1);
    if(mysqli_num_rows($results) > 0)
	{
		$row = mysqli_fetch_array($results);
		$retVal = $row['serverHostName'];
	}
	
	return $retVal;		
}

function sendMQTT($arg_1, $arg_2)
{
	$client = new Mosquitto\Client();
	$client->connect('romoserver.local', 1883, 5);
	$client->loop();
	$mid = $client->publish($arg_1, $arg_2);
	$client->loop();
	$client->loop();
}

function killUserSession()
{
	session_unset();
	session_destroy();
	header("Location: index.php");
	exit();
}


function read_topic($arg_1) 
{
	$client = new Mosquitto\Client();
	$client->onConnect('connect');
//	$client->onDisconnect('disconnect');
	$client->onSubscribe('subscribe');
	$client->onMessage('message');
	$client->connect('romoserver.local', 1883, 5);

	$queue = $arg_1 . '/SystemStatus';
	$client->subscribe($queue,1);
	
	$date1 = time();
	$GLOBALS['rcv_message'] = '';
	
	while (true) 
	{
			$client->loop();
			$date2 = time();
			
			if (($date2 - $date1) > 5) break;
			if(!empty($GLOBALS['rcv_message'])) break;
	}
	 
	$client->disconnect();
	unset($client);						
} 


/*****************************************************************
 * Call back functions for MQTT library
 * ***************************************************************/					
function connect($r) 
{
	
	/*	if($r == 0) echo "{$r}-CONX-OK|";
		if($r == 1) echo "{$r}-Connection refused (unacceptable protocol version)|";
		if($r == 2) echo "{$r}-Connection refused (identifier rejected)|";
		if($r == 3) echo "{$r}-Connection refused (broker unavailable )|";      */
}
 
function publish() {
        global $client;
        echo "Mesage published:";
}
 
function disconnect() {
        echo "Disconnected|";
}


function subscribe() 
{
	    //**Store the status to a global variable - debug purposes 
		$GLOBALS['statusmsg'] = $GLOBALS['statusmsg'] . "SUB-OK|";
}

function message($message) 
{
	    //**Store the status to a global variable - debug purposes
		$GLOBALS['statusmsg']  = "RX-OK|";
		
		//**Store the received message to a global variable
		$GLOBALS['rcv_message'] =  $message->payload;
}

function requestSystemInfo($arg_1)
{
	$sendArray["systemInfo"] = 1;		
	sendMQTT($arg_1, json_encode($sendArray));	
	read_topic($arg_1);
}

