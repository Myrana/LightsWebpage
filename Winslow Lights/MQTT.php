<?php 

$client = new Mosquitto\Client();
$client->connect('romoserver.local', 1883, 5);
$client->loop();
$mid = $client->publish($query_data['serverHostName'], json_encode($sendArray));
$client->loop();

?>