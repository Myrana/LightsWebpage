const tmi = require('tmi.js');

const mqtt = require('mqtt');
const mqttClient = mqtt.connect('mqtt://Romoserver.local', {clientId:"BenchPiTwitter"});


mqttClient.on('connect', () => {
	console.log('connected');
   	
    });    

mqttClient.on("error",function(error){ console.log("Can't connect"+error)});


// Define configuration options
const opts = {
  identity: {
    username: <username>,
    password: <KEY>
  },
  channels: [
    "lumawin"
  ]
};
// Create a client with our options
const client = new tmi.client(opts);

// Register our event handlers (defined below)
client.on('message', onMessageHandler);
client.on('connected', onConnectedHandler);

// Connect to Twitch:
client.connect();

// Called every time a message comes in
function onMessageHandler (target, context, msg, self) {
  if (self) { return; } // Ignore messages from the bot

  // Remove whitespace from chat message
  const commandName = msg.trim();

  // If the command is known, let's execute it
  if (commandName === '!dice') 
  {
    const num = rollDice();
    client.say(target, `You rolled a ${num}`);
    console.log(`* Executed ${commandName} command`);
  } 
  else if (commandName.split(' ')[0] === '!runshow')
  {
  
    mqttClient.publish(<QUEUE>, commandName);   
    client.say(target, `going To Run A Show`);
    console.log(`* Executed ${commandName} command`);
    
  }
  else
  {
	  console.log(`* Unknown command ${commandName}`);
  }
}
// Function called when the "dice" command is issued
function rollDice () {
  const sides = 6;
  return Math.floor(Math.random() * sides) + 1;
}
// Called every time the bot connects to Twitch chat
function onConnectedHandler (addr, port) {
  console.log(`* Connected to ${addr}:${port}`);
}
