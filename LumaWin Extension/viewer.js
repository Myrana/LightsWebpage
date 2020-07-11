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


function createRequestPost(type, method) {

    return {
        type: type,
        url: 'https://lumawin.com:8080/lumawinTwitch/' + method + '?show=1&color1=0xff6377',
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

twitch.onAuthorized(function(auth) {
    // save our credentials
twitch.rig.log('user-' + auth.userId + '-token ' + auth.token);
    token = auth.token;
    tuid = auth.userId;


  
    setAuth(token);
    $.ajax(requests.get);
});

function updateBlock(hex) {
    twitch.rig.log('Updating block color');
    $('#color').css('background-color', hex);
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
        twitch.rig.log('Requesting a color cycle');
        requests.set.url += '?show=1&color1=0xff6777';
        twitch.rig.log('rew-' + requests.set.url);
        
        $.ajax(requests.set);
    });

    // listen for incoming broadcast message from our EBS
    twitch.listen('broadcast', function (target, contentType, color) {
        twitch.rig.log('Received broadcast color');
        updateBlock(color);
    });
});
