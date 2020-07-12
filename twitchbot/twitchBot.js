// Define configuration options
const opts = {
  options: { debug: true },
	connection: {
		reconnect: true,
		secure: true
	},
  identity: {
    username: "<USER>",
    password: "<password>"
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


var clientId = `Twitch:` + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
console.log(clientId);
const mqttClient = mqtt.connect('mqtt://Romoserver.local', {clientId:"${clientId}"});


mqttClient.on('connect', () => {
	console.log('MQTT Is Connected!');
   	
    });    

mqttClient.on("error",function(error){ console.log("Can't connect"+error)});



var mySqlCon = mysql.createConnection({
  host: "<DBSERVER>",
  user: "<USER>",
  password: "<PWD>",
  database: "DB"
});

mySqlCon.connect(function(err) {
  if (err) throw err;
  console.log("MySQL Server Is Connected!");
});



// Create a client with our options
const client = new tmi.client(opts);

// Register our event handlers (defined below)
client.on('message', onMessageHandler);
client.on('join', onJoinedHandler);
client.on('connected', onConnectedHandler);

// Connect to Twitch:
client.connect();

// Called every time a message comes in
function onMessageHandler (channel, user, msg, self) 
{
  if (self) { return; } // Ignore messages from the bot

  // Remove whitespace from chat message
  var commandName = msg.trim();
  var allowSend = 0;
  
  
  console.log(`${user.username}, checking light system subscriber Accesss!`);
  var subscriber = false;
  if(user.badges != null && (user.badges.subscriber != null || user.badges.broadcaster != null || user.badges.moderator != null))
  	subscriber = true;
  
  	mySqlCon.query(`SELECT ID,enabled FROM twitchUsers where twitchUser = "${user.username}"`, function (err, result, fields) 
	{

	if (err) throw err;
	if(result.length == 0 || result[0].enabled == 0)
	{
		var id = 0;
		if(result.length != 0)
		  id = result[0].ID;
		
		var sql = `insert into twitchUsers(ID,twitchUser,enabled) values( ${id},"${user.username}",${subscriber}) ON DUPLICATE KEY UPDATE enabled = VALUES(enabled)`;
		
				mySqlCon.query(sql, function (err, result, fields) 
	{

				if (err) throw err;

		});
		
		
		 
	}


	});
	
	
  
  if(subscriber == false)
  	console.log(`${user.username}, is not a subscriber, limited access to LightSystem!`);		
  	
  if (commandName.split(' ')[0] === '!lumawin')
  {
  
	mySqlCon.query(`SELECT enabled,mqttQueue,allowAllTwitchUsers FROM twitchChannels where channel = "${channel}"`, function (err, result, fields) 
	{

	    if (err) throw err;
	    if(result.length && result[0].enabled == 1)
	    {
		allowSend = result[0].allowAllTwitchUsers;
		mqttQueue = result[0].mqttQueue;
		    mySqlCon.query(`SELECT enabled FROM twitchUsers where twitchUser = "${user.username}"`, function (err, result, fields) {
	       
	        if (err) throw err;
	    	if(result.length && result[0].enabled == 1)
			allowSend = 1;
			
	    

	  	});

	    }
	    else
	    {
		    console.log(`* ${channel} Light System is currently Not Running!`);
		    client.say(target, `${user.username}, sorry, light System is currently Not Running!.  Speak to channel operator!`);
	    }
	    
		if(allowSend == 1)
		 {
			mqttClient.publish(mqttQueue, commandName);   
			console.log(`* Executed ${commandName} channel: ${channel} user: ${user.username} `);

		 }
		 else
		 {
		    console.log(`* ${target} Not Authorized To LumaWinBot`);		    

		 }
	    
	});

	
  }
  else if(commandName.split(' ')[0] === '!help')
  {
  	client.say(target, `${user.username}, sorry, light System help is still under construction!  Speak to channel operator!`);
  
  }
else if(commandName.split(' ')[0] === '!colors')
  {
        client.say(target, `${user.username}, here is a website you can try! https://www.color-hex.com`);
  
  }
  
  
 
}


function dehash(channel) 
{
	return channel.replace(/^#/, '');
}

function capitalize(n) {
	return n[0].toUpperCase() +  n.substr(1);
}

// Called every time the bot connects to Twitch chat
function onJoinedHandler (channel, username) 
{
  if(username == client.getUsername()) 
  {
  

  }

}


// Called every time the bot connects to Twitch chat
function onConnectedHandler (addr, port) 
{
  console.log(`* Connected to ${addr}:${port}`);

}
