//node backend -c "tugb191yihmq33b1es8wq8ajqs3qa1" -s "/bwZV8evr5xkgYv2ukuuKDk5fC/ePZd2ESKWEZQ4lQo=" -o "546623929"

/**
 *    Copyright 2018 Amazon.com, Inc. or its affiliates
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

const fs = require('fs');
const Hapi = require('hapi');
const path = require('path');
const Boom = require('boom');
const color = require('color');
const ext = require('commander');
const jsonwebtoken = require('jsonwebtoken');
const request = require('request');

// The developer rig uses self-signed certificates.  Node doesn't accept them
// by default.  Do not use this in production.
process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';

// Use verbose logging during development.  Set this to false for production.
const verboseLogging = true;
const verboseLog = verboseLogging ? console.log.bind(console) : () => { };

// Service state variables
const initialColor = color('#6441A4');      // super important; bleedPurple, etc.
const serverTokenDurationSec = 30;          // our tokens for pubsub expire after 30 seconds
const userCooldownMs = 1000;                // maximum input rate per user to prevent bot abuse
const userCooldownClearIntervalMs = 60000;  // interval to reset our tracking object
const channelCooldownMs = 1000;             // maximum broadcast rate per channel
const bearerPrefix = 'Bearer ';             // HTTP authorization headers have this prefix
const colorWheelRotation = 30;
const channelColors = {};
const channelCooldowns = {};                // rate limit compliance
let userCooldowns = {};                     // spam prevention

const STRINGS = {
  secretEnv: usingValue('secret'),
  clientIdEnv: usingValue('client-id'),
  ownerIdEnv: usingValue('owner-id'),
  serverStarted: 'Server running at %s',
  secretMissing: missingValue('secret', 'EXT_SECRET'),
  clientIdMissing: missingValue('client ID', 'EXT_CLIENT_ID'),
  ownerIdMissing: missingValue('owner ID', 'EXT_OWNER_ID'),
  messageSendError: 'Error sending message to channel %s: %s',
  pubsubResponse: 'Message to c:%s returned %s',
  cyclingColor: 'Cycling color for c:%s on behalf of u:%s',
  colorBroadcast: 'Broadcasting color %s for c:%s',
  sendColor: 'Sending color %s to c:%s',
  cooldown: 'Please wait before clicking again',
  invalidAuthHeader: 'Invalid authorization header',
  invalidJwt: 'Invalid JWT',
};

ext.
  version(require('../package.json').version).
  option('-s, --secret <secret>', 'Extension secret').
  option('-c, --client-id <client_id>', 'Extension client ID').
  option('-o, --owner-id <owner_id>', 'Extension owner ID').
  parse(process.argv);

const ownerId = getOption('ownerId', 'EXT_OWNER_ID');
const secret = Buffer.from(getOption('secret', 'EXT_SECRET'), 'base64');
const clientId = getOption('clientId', 'EXT_CLIENT_ID');

const serverOptions = {
  host: '192.168.1.3',
  port: 8080,
  routes: {
    cors: {
      origin: ['*'],
    },
  },
};

const serverPathRoot = path.resolve(__dirname, '..', 'conf', 'server');
console.log(serverPathRoot);
if (fs.existsSync(serverPathRoot + '.crt') && fs.existsSync(serverPathRoot + '.pem')) {
	console.log('found keys');
  serverOptions.tls = {
    // If you need a certificate, execute "npm run cert".
    cert: fs.readFileSync(serverPathRoot + '.crt'),
    key: fs.readFileSync(serverPathRoot + '.pem'),
  };
}

const server = new Hapi.Server(serverOptions);

(async () => {
  // Handle a viewer request to cycle the color.
  server.route({
    method: 'POST',
    path: '/lumawinTwitch/runshow',
    handler: colorCycleHandler,
  });

  // Handle a new viewer requesting the color.
  server.route({
    method: 'GET',
    path: '/lumawinTwitch/query',
    handler: colorQueryHandler,
  });

  // Start the server.
  await server.start();
  console.log(STRINGS.serverStarted, server.info.uri);

  // Periodically clear cool-down tracking to prevent unbounded growth due to
  // per-session logged-out user tokens.
  setInterval(() => { userCooldowns = {}; }, userCooldownClearIntervalMs);
})();

function usingValue(name) {
  return `Using environment variable for ${name}`;
}

function missingValue(name, variable) {
  const option = name.charAt(0);
  return `Extension ${name} required.\nUse argument "-${option} <${name}>" or environment variable "${variable}".`;
}

// Get options from the command line or the environment.
function getOption(optionName, environmentName) {
  const option = (() => {
    if (ext[optionName]) {
      return ext[optionName];
    } else if (process.env[environmentName]) {
      console.log(STRINGS[optionName + 'Env']);
      return process.env[environmentName];
    }
    console.log(STRINGS[optionName + 'Missing']);
    process.exit(1);
  })();
  console.log(`Using "${option}" for ${optionName}`);
  return option;
}

// Verify the header and the enclosed JWT.
function verifyAndDecode(header) {
  if (header.startsWith(bearerPrefix)) {
    try {
      const token = header.substring(bearerPrefix.length);
      return jsonwebtoken.verify(token, secret, { algorithms: ['HS256'] });
    }
    catch (ex) {
      throw Boom.unauthorized(STRINGS.invalidJwt);
    }
  }
  throw Boom.unauthorized(STRINGS.invalidAuthHeader);
}

function hexToRgb(hex) {
    var bigint = parseInt(hex.replace('#',''), 16);
    console.log(bigint);
    var r = (bigint >> 16) & 255;
    var g = (bigint >> 8) & 255;
    var b = bigint & 255;

    return r + "," + g + "," + b;
}

function colorCycleHandler(req) {
  // Verify all requests.
  console.log(`****************** colorCycleHandler ***************************`);
  const payload = verifyAndDecode(req.headers.authorization);
  const { channel_id: channelId, opaque_user_id: opaqueUserId } = payload;


  console.log(req.url.query);
  
  var JSONObj;
  var colorRGB;
  var onecolor = true;
  JSONObj = '[{"show":"' + req.url.query.show + '"';
  if(req.url.query.color1 || req.url.query.color2 || req.url.query.color3 || req.url.query.color4)
  {
  
  	 //"colors": [{"color1": {"b": 211, "g": 0, "r": 255}}, {"color2": {"b": 107, "g": 250, "r": 109}}]
	 //"colors":[{"color1": {"r":255,"g":101,"b":0},{"color2": {"r":144,"g":107,"b":250}}],"brightness":"127","gammacorrection": 1,"delay":"100","minutes":"5"}]

	
	  console.log("Bob-" + hexToRgb(req.url.query.color1));


  	if(req.url.query.color1) 
  	{
   	       colorRGB =  hexToRgb(req.url.query.color1);
   	       
  		JSONObj += ',"colors":[{"color1": {"r":' + colorRGB.split(',')[0] + ',"g":' + colorRGB.split(',')[1] + ',"b":' + colorRGB.split(',')[2] + '}}';
  	}
 
  	if(req.url.query.color2) 
  	{
   	       colorRGB =  hexToRgb(req.url.query.color2);
   	       
  		JSONObj += ',{"color2": {"r":' + colorRGB.split(',')[0] + ',"g":' + colorRGB.split(',')[1] + ',"b":' + colorRGB.split(',')[2] + '}}';
  	}
  	
  	  if(req.url.query.color3) 
  	    	{
   	       colorRGB =  hexToRgb(req.url.query.color3);
   	       
  		JSONObj += ',{"color3": {"r":' + colorRGB.split(',')[0] + ',"g":' + colorRGB.split(',')[1] + ',"b":' + colorRGB.split(',')[2] + '}}';
  	}


  	  if(req.url.query.color4) 
  	{
   	       colorRGB =  hexToRgb(req.url.query.color4);
   	       
  		JSONObj += ',{"color4": {"r":' + colorRGB.split(',')[0] + ',"g":' + colorRGB.split(',')[1] + ',"b":' + colorRGB.split(',')[2] + '}}';
  	}
  	
  	JSONObj += ']';
  	

  }
  
  
  JSONObj += ',"brightness":"' + req.url.query.brightness + '"';
  
  if(req.url.query.gammacorrection)
    JSONObj += ',"gammacorrection": 1'; 
    
    if(req.url.query.colorevery)
     JSONObj += ',"colorevery":"' + req.url.query.colorevery  + '"'; 

    if(req.url.query.width) 
  	JSONObj += ',"width":"' + req.url.query.width + '"';

    if(req.url.query.delay) 
  	JSONObj += ',"delay":"' + req.url.query.delay + '"';
  	
   if(req.url.query.minutes) 
  	JSONObj += ',"minutes":"' + req.url.query.minutes + '"';

  JSONObj += '}]';
  console.log(JSONObj);

  var j = JSON.parse(JSONObj);

//  if(req.url.query.clearstart)
  //  JSONObj += ',{"clearstart": 1}'; 

//  if(req.url.query.clearfinish)
  //  JSONObj += ',{"clearfinish": 1}'; 
  

  
  // Store the color for the channel.
  let currentColor = channelColors[channelId] || initialColor;

  // Bot abuse prevention:  don't allow a user to spam the button.
  if (userIsInCooldown(opaqueUserId)) {
    throw Boom.tooManyRequests(STRINGS.cooldown);
  }

  // Rotate the color as if on a color wheel.
  verboseLog(STRINGS.cyclingColor, channelId, opaqueUserId);
  currentColor = color(currentColor).rotate(colorWheelRotation).hex();

  // Save the new color for the channel.
  channelColors[channelId] = currentColor;

  // Broadcast the color change to all other extension instances on this channel.
  attemptColorBroadcast(channelId);

  return currentColor;
}

function colorQueryHandler(req) {
  // Verify all requests.
  
   console.log(`****************** colorQueryHandler ***************************`);
    
  const payload = verifyAndDecode(req.headers.authorization);
  // Get the color for the channel from the payload and return it.
  const { channel_id: channelId, opaque_user_id: opaqueUserId } = payload;
  const currentColor = color(channelColors[channelId] || initialColor).hex();
  verboseLog(STRINGS.sendColor, currentColor, opaqueUserId);
  return currentColor;
}

function attemptColorBroadcast(channelId) {
  // Check the cool-down to determine if it's okay to send now.
  const now = Date.now();
  const cooldown = channelCooldowns[channelId];
  if (!cooldown || cooldown.time < now) {
    // It is.
    sendColorBroadcast(channelId);
    channelCooldowns[channelId] = { time: now + channelCooldownMs };
  } else if (!cooldown.trigger) {
    // It isn't; schedule a delayed broadcast if we haven't already done so.
    cooldown.trigger = setTimeout(sendColorBroadcast, now - cooldown.time, channelId);
  }
}

function sendColorBroadcast(channelId) {
  // Set the HTTP headers required by the Twitch API.
  const headers = {
    'Client-ID': clientId,
    'Content-Type': 'application/json',
    'Authorization': bearerPrefix + makeServerToken(channelId),
  };

  // Create the POST body for the Twitch API request.
  const currentColor = color(channelColors[channelId] || initialColor).hex();
  const body = JSON.stringify({
    content_type: 'application/json',
    message: currentColor,
    targets: ['broadcast'],
  });

  // Send the broadcast request to the Twitch API.
  verboseLog(STRINGS.colorBroadcast, currentColor, channelId);
  request(
    `https://api.twitch.tv/extensions/message/${channelId}`,
    {
      method: 'POST',
      headers,
      body,
    }
    , (err, res) => {
      if (err) {
        console.log(STRINGS.messageSendError, channelId, err);
      } else {
        verboseLog(STRINGS.pubsubResponse, channelId, res.statusCode);
      }
    });
}

// Create and return a JWT for use by this service.
function makeServerToken(channelId) {
  const payload = {
    exp: Math.floor(Date.now() / 1000) + serverTokenDurationSec,
    channel_id: channelId,
    user_id: ownerId, // extension owner ID for the call to Twitch PubSub
    role: 'external',
    pubsub_perms: {
      send: ['*'],
    },
  };
  return jsonwebtoken.sign(payload, secret, { algorithm: 'HS256' });
}

function userIsInCooldown(opaqueUserId) {
  // Check if the user is in cool-down.
  const cooldown = userCooldowns[opaqueUserId];
  const now = Date.now();
  if (cooldown && cooldown > now) {
    return true;
  }

  // Voting extensions must also track per-user votes to prevent skew.
  userCooldowns[opaqueUserId] = now + userCooldownMs;
  return false;
}