var token = "";
var tuid = "";
var ebs = "";

// because who wants to type this every time?
var twitch = window.Twitch.ext;

// create the request options for our Twitch API calls
var requests = {
    set: createRequest('POST', 'runshow'),
    get: createRequest('GET', 'query')
};



function createRequest(type, method) {

    return {
        type: type,
        url: 'https://lumawin.com:8080/lumawinTwitch/' + method,
        success: updateBlock,
        error: logError

    }
}



function setAuth(token) {
    Object.keys(requests).forEach((req) => {
        twitch.rig.log('Setting auth headers');
        requests[req].headers = { 'Authorization': 'Bearer ' + token }
    });
}

twitch.onContext(function(context) {
    twitch.rig.log(context);
});


//Here is where we want to send them a payload, at some point, just not now.
//for now we will just comment out the get call as it is not needed.
twitch.onAuthorized(function(auth) {
    // save our credentials
twitch.rig.log('user-' + auth.userId + '-token ' + auth.token);
    token = auth.token;
    tuid = auth.userId;

    setAuth(token);
    $.ajax(requests.get);
});


function updateBlock(hex) {
    //twitch.rig.log('Updating block color');
    $('#color').css('background-color', hex);
    twitch.rig.log(hex);
}

function logError(_, error, status) {
  twitch.rig.log('EBS request returned '+status+' ('+error+')');
}

function logSuccess(hex, status) {
  // we could also use the output to update the block synchronously here,
  // but we want all views to get the same broadcast response at the same time.
  twitch.rig.log('EBS request returned '+hex+' ('+status+')');
}

$(function() {

    // when we click the cycle button
    $('#btnSendShow').click(function() {
        if(!token) { return twitch.rig.log('Not authorized'); }
        twitch.rig.log('Requesting To Play A Show');

	var showNameId = document.getElementById("ShowNameId");
        
	var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");
        var brightness = document.getElementById("Brightness");
	var gammaCorrection = document.getElementById("gammaCorrection");
        var clearStart =  document.getElementById("clearStart");
        var clearFinish =  document.getElementById("clearFinish");
	
        
        var index = parseInt(showNameId.value);     
        requests.set.url =  'https://lumawin.com:8080/lumawinTwitch/runshow';
           
        requests.set.url += '?show=' +index;
	requests.set.url += '&brightness=' + brightness.value;
	
        if(showMap.get(index).numColors >= 1)
           requests.set.url += '&color1=' + encodeURIComponent(color1.value);
       
        if(showMap.get(index).numColors >= 2)
       
	    requests.set.url += '&color2=' + encodeURIComponent(color2.value);
       
        if(showMap.get(index).numColors >= 3)
           requests.set.url += '&color3=' + encodeURIComponent(color3.value);
	
        if(showMap.get(index).numColors === 4)
           requests.set.url += '&color4=' + encodeURIComponent(color4.value);
           
        if(showMap.get(index).hasWidth === 1)
           requests.set.url += '&width=' + width.value;
       
        if(showMap.get(index).hasMinutes === 1)
           requests.set.url += '&minutes=' + minutes.value;
       
        if(showMap.get(index).hasDelay === 1)
           requests.set.url += '&delay=' + delay.value;
           
        if(showMap.get(index).colorEvery === 1)
	 requests.set.url += '&colorevery=' + colorEvery.value;

	if(gammaCorrection.checked)
          requests.set.url += '&gammacorrection=1';
          
          
        if(clearStart.checked)
          requests.set.url += '&clearstart=1';
        
     	if(clearFinish.checked)
          requests.set.url += '&clearfinish=1';
                  
      
        
        twitch.rig.log('*******:' + requests.set.url);
        
        $.ajax(requests.set);
    });

    // listen for incoming broadcast message from our EBS Where we will process the recieved message.
    twitch.listen('broadcast', function (target, contentType, color) {
        twitch.rig.log('Received broadcast color');
        updateBlock(color);
    });
});



/*

         if(powerOn.checked())
           qryString += '&poweron=1';
*/

