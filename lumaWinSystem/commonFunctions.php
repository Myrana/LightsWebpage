<?php

session_start();
$expireAfter = 120;


if(!isset($_SESSION['DBServer']))
{
	$confFile = realpath("/etc") . "/rpilightsystem.conf";
	if(file_exists($confFile))
	{
	
		if(file_exists($confFile))
		{	
			$ini_array = parse_ini_file($confFile);	
			$_SESSION['DBServer'] = $ini_array["DBServer"];
			$_SESSION['DBUserID'] = $ini_array["DBUserID"];
			$_SESSION['DBPassword'] = $ini_array["DBPassword"];
			$_SESSION['DataBase'] = $ini_array["DataBase"];
			$_SESSION['MQTTBroker'] = $ini_array["MQTTBroker"];
			$_SESSION['UploadArtDir'] = $ini_array["UploadArtDir"];
		}
	}
}
	
	
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


function getDatabaseConnection()
{
	// Create connection
	$conn = new mysqli($_SESSION['DBServer'], $_SESSION['DBUserID'], $_SESSION['DBPassword'] , $_SESSION['DataBase']);
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


function buildUserArt()
{
	$_SESSION['userArtScript'] = "";
	$_SESSION['userArtOptions'] = "<option value = '0'>-- Select One --</option>";
	$con = getDatabaseConnection();
	//$artresults = mysqli_query($con,"SELECT * FROM  matrixArt where userID =" . $_SESSION['UserID'] . " or userID = 1 orderby artName desc");
	$artresults = mysqli_query($con,"SELECT * FROM  matrixArt where userID =" . $_SESSION['UserID'] . " or userID = 1");

	if(mysqli_num_rows($artresults) > 0)
	{
		$_SESSION['userArtScript']  = "let artListMap = new Map();\r";
		while($artRow = mysqli_fetch_array($artresults))
		{
		
			$_SESSION['userArtScript']  .= "var art = new Object(); \r";

			$_SESSION['userArtScript']  .= "    art.id = " . $artRow['ID'] .";\r";
			$_SESSION['userArtScript']  .= "    art.userId = " . $artRow['userID'] .";\r";
			$_SESSION['userArtScript']  .= "    art.artName = '" . $artRow['artName'] ."';\r";
			$_SESSION['userArtScript']  .= "    art.origWidth = '" . $artRow['savedPixalsWidth'] ."';\r";
			$_SESSION['userArtScript']  .= "    art.showParms = JSON.parse('" . $artRow['showParms'] . "');\r";       
			$_SESSION['userArtScript']  .= "    artListMap.set(" . $artRow['ID'] . ", art);\r";
			
			$_SESSION['userArtOptions']  .="<option value = '".$artRow['ID']."'>".$artRow['artName']."</option>";
		}

	}
}


function sendMQTT($arg_1, $arg_2)
{
	$client = new Mosquitto\Client();
	$client->connect($_SESSION['MQTTBroker'], 1883, 5);
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
	$client->connect($_SESSION['MQTTBroker'], 1883, 5);

	$queue = $arg_1 . '/SystemStatus';
	$client->subscribe($queue,1);
	
	$date1 = time();
	$GLOBALS['rcv_message'] = '';
	
	while (true) 
	{
			$client->loop();
			$date2 = time();
			
			if (($date2 - $date1) > 2) break;
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

