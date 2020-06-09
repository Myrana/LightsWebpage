<?php

$test = $_POST['test'];

$client = new Mosquitto\Client();
$client->connect("patio1.local", 1883, 5);
$client->loop();
$mid = $client->publish('Test', 'ON');
$client->loop();
$client->connect("patio2.local", 1883, 5);
$client->loop();
$mid = $client->publish('Test', 'ON');
$client->loop();



?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>success</title>
</head>

<body>
	<p>success!!!!</p>
</body>
</html>