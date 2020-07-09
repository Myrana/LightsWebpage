// Define configuration options
const opts = {
  identity: {
    username: "lumawin",
    password: "ukw4ezwxkpblqtd18kazulky7mnm26"
  },
  channels: [
    "lumawin",
    "snowleopard__x"
  ]
};


const tmi = require('tmi.js');
const mqtt = require('mqtt');
const mysql = require('mysql');
var mqttQueue = "";

const mqttClient = mqtt.connect('mqtt://Romoserver.local', {clientId:"BenchPiTwitter"});


mqttClient.on('connect', () => {
	console.log('MQTT Is Connected!');
   	
    });    

mqttClient.on("error",function(error){ console.log("Can't connect"+error)});



var mySqlCon = mysql.createConnection({
  host: "Romoserver.local",
  user: "hellweek",
  password: "covert69guess",
  database: "LedLightSystem"
});

mySqlCon.connect(function(err) {
  if (err) throw err;
  console.log("MySQL Server Is Connected!");
});



// Create a client with our options
const client = new tmi.client(opts);

// Register our event handlers (defined below)
client.on('message', onMessageHandler);
client.on('connected', onConnectedHandler);

// Connect to Twitch:
client.connect();

// Called every time a message comes in
function onMessageHandler (target, context, msg, self) 
{
  if (self) { return; } // Ignore messages from the bot

  // Remove whitespace from chat message
  var commandName = msg.trim();
  var allowSend = 0;
  
  
  console.log(`${context.username}, checking light system subscriber Accesss!`);
  var subscriber = false;
  if(context.badges.subscriber != undefined || context.badges.broadcaster != undefined || context.badges.moderator != undefined)
  	subscriber = true;
  
  	mySqlCon.query(`SELECT ID,enabled FROM twitchUsers where twitchUser = "${context.username}"`, function (err, result, fields) 
	{

	if (err) throw err;
	if(result.length == 0 || result[0].enabled == 0)
	{
		var id = 0;
		if(result.length != 0)
		  id = result[0].ID;
		
		var sql = `insert into twitchUsers(ID,twitchUser,enabled) values( ${id},"${context.username}",${subscriber}) ON DUPLICATE KEY UPDATE enabled = VALUES(enabled)`;
		
				mySqlCon.query(sql, function (err, result, fields) 
	{

				if (err) throw err;

		});
		
		
		 
	}


	});
	
	
  
  if(subscriber == false)
  	console.log(`${context.username}, is not a subscriber, limited access to LightSystem!`);		
  	
  if (commandName.split(' ')[0] === '!lumawin')
  {
  
	mySqlCon.query(`SELECT enabled,mqttQueue,allowAllTwitchUsers FROM twitchChannels where channel = "${target}"`, function (err, result, fields) 
	{

	    if (err) throw err;
	    if(result.length && result[0].enabled == 1)
	    {
		allowSend = result[0].allowAllTwitchUsers;
		mqttQueue = result[0].mqttQueue;
		    mySqlCon.query(`SELECT enabled FROM twitchUsers where twitchUser = "${context.username}"`, function (err, result, fields) {
	       
	        if (err) throw err;
	    	if(result.length && result[0].enabled == 1)
			allowSend = 1;
			
	    

	  	});

	    }
	    else
	    {
		    console.log(`* ${target} Light System is currently Not Running!`);
		    client.say(target, `${context.username}, sorry, light System is currently Not Running!.  Speak to channel operator!`);
	    }
	    
		if(allowSend == 1)
		 {
			mqttClient.publish(mqttQueue, commandName);   
			console.log(`* Executed ${commandName} channel: ${target} user: ${context.username} `);

		 }
		 else
		 {
		    console.log(`* ${target} Not Authorized To LumaWinBot`);		    

		 }
	    
	});

	
  }
  else if(commandName.split(' ')[0] === '!help')
  {
  	client.say(target, `${context.username}, sorry, light System help is still under construction!  Speak to channel operator!`);
  
  }
else if(commandName.split(' ')[0] === '!colors')
  {
        client.say(target, `${context.username}, here is a website you can try! https://www.color-hex.com`);
  
  }
  
  
 
}

// Called every time the bot connects to Twitch chat
function onConnectedHandler (addr, port) 
{
  console.log(`* Connected to ${addr}:${port}`);

}
