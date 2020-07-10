   var showMap = new Map();
var show = new Object(); 
    show.id = 16;
    show.showName = 'Display Color';
    show.numColors = 1;
    show.hasDelay = 0;
  show.hasWidth = 0;
  show.hasMinutes = 0;
  show.colorEvery = 0;
    showMap.set(16, show);
var show = new Object(); 
    show.id = 3;
    show.showName = 'Theater Chase';
    show.numColors = 0;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(3, show);
var show = new Object(); 
    show.id = 4;
    show.showName = 'Theater Chase Rainbow';
    show.numColors = 0;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(4, show);
var show = new Object(); 
    show.id = 10;
    show.showName = 'Rainbow Cycle';
    show.numColors = 0;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(10, show);
var show = new Object(); 
    show.id = 11;
    show.showName = 'Neorand';
    show.numColors = 0;
    show.hasDelay = 0;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(11, show);
var show = new Object(); 
    show.id = 15;
    show.showName = 'Tri-Color Chaser';
    show.numColors = 3;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(15, show);
var show = new Object(); 
    show.id = 9;
    show.showName = 'Rainbow';
    show.numColors = 0;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(9, show);
var show = new Object(); 
    show.id = 12;
    show.showName = 'Flame';
    show.numColors = 1;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(12, show);
var show = new Object(); 
    show.id = 8;
    show.showName = 'Half n Half';
    show.numColors = 2;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(8, show);
var show = new Object(); 
    show.id = 13;
    show.showName = 'Color 1/3 ';
    show.numColors = 3;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(13, show);
var show = new Object(); 
    show.id = 14;
    show.showName = 'Color 1/4';
    show.numColors = 4;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(14, show);
var show = new Object(); 
    show.id = 17;
    show.showName = 'Color Every';
    show.numColors = 1;
    show.hasDelay = 0;
  show.hasWidth = 0;
  show.hasMinutes = 0;
  show.colorEvery = 1;
    showMap.set(17, show);
var show = new Object(); 
    show.id = 2;
    show.showName = 'Chaser';
    show.numColors = 1;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(2, show);
var show = new Object(); 
    show.id = 5;
    show.showName = 'Color 3 Reverse';
    show.numColors = 3;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(5, show);
var show = new Object(); 
    show.id = 6;
    show.showName = 'Cylon';
    show.numColors = 1;
    show.hasDelay = 1;
  show.hasWidth = 1;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(6, show);
var show = new Object(); 
    show.id = 7;
    show.showName = 'Color Wipe';
    show.numColors = 1;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(7, show);
var show = new Object(); 
    show.id = 18;
    show.showName = 'Twinkle Overlay';
    show.numColors = 2;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(18, show);
var show = new Object(); 
    show.id = 1;
    show.showName = 'Blink';
    show.numColors = 0;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(1, show);
var show = new Object(); 
    show.id = 19;
    show.showName = 'Pulse Overlay';
    show.numColors = 0;
    show.hasDelay = 1;
  show.hasWidth = 0;
  show.hasMinutes = 1;
  show.colorEvery = 0;
    showMap.set(19, show);


$(function() {

    // when we click the cycle button
    $('#ShowNameId').click(function() {
        var showNameId = document.getElementById("ShowNameId");
        var index = parseInt(showNameId.value);


        var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");

        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		
        if(showMap.get(index).hasWidth == 1)
        {
            width.setAttribute('disabled', false);
            width.disabled = false;

        }

        if(showMap.get(index).hasMinutes == 1)
        {
            minutes.setAttribute('disabled', false);
            minutes.disabled = false;
        }

        if(showMap.get(index).hasDelay == 1)
        {
            delay.setAttribute('disabled', false);
            delay.disabled = false;
        }

        if(showMap.get(index).numColors >= 1)
        {
            color1.setAttribute('disabled', false);
            color1.disabled = false;
        }

        if(showMap.get(index).numColors >= 2)
        {

            color2.setAttribute('disabled', false);
            color2.disabled = false;
        }

        if(showMap.get(index).numColors >= 3)
        {
            color3.setAttribute('disabled', false);
            color3.disabled = false;
        }

        if(showMap.get(index).numColors == 4)
        {
            color4.setAttribute('disabled', false);
            color4.disabled = false;
        }
        
        
        if(showMap.get(index).colorEvery == 1)
        {
            colorEvery.setAttribute('disabled', false);
            colorEvery.disabled = false;
        }
    });

});


    
    function setShowSettings()
    {
        var showNameId = document.getElementById("ShowNameId");
        var index = parseInt(showNameId.value);


        var color1 = document.getElementById("Color1");
        var color2 = document.getElementById("Color2");
        var color3 = document.getElementById("Color3");
        var color4 = document.getElementById("Color4");
        var delay = document.getElementById("DelayId");
        var width = document.getElementById("WidthId");
        var minutes = document.getElementById("NumMinutesId");
        var colorEvery = document.getElementById("ColorEveryId");

        color1.setAttribute('disabled', true);
        color2.setAttribute('disabled', true);
        color3.setAttribute('disabled', true);
        color4.setAttribute('disabled', true);
        delay.setAttribute('disabled', true);
        width.setAttribute('disabled', true);
        minutes.setAttribute('disabled', true);
		colorEvery.setAttribute('disabled', true);
		
        if(showMap.get(index).hasWidth == 1)
        {
            width.setAttribute('disabled', false);
            width.disabled = false;

        }

        if(showMap.get(index).hasMinutes == 1)
        {
            minutes.setAttribute('disabled', false);
            minutes.disabled = false;
        }

        if(showMap.get(index).hasDelay == 1)
        {
            delay.setAttribute('disabled', false);
            delay.disabled = false;
        }

        if(showMap.get(index).numColors >= 1)
        {
            color1.setAttribute('disabled', false);
            color1.disabled = false;
        }

        if(showMap.get(index).numColors >= 2)
        {

            color2.setAttribute('disabled', false);
            color2.disabled = false;
        }

        if(showMap.get(index).numColors >= 3)
        {
            color3.setAttribute('disabled', false);
            color3.disabled = false;
        }

        if(showMap.get(index).numColors == 4)
        {
            color4.setAttribute('disabled', false);
            color4.disabled = false;
        }
        
        
        if(showMap.get(index).colorEvery == 1)
        {
            colorEvery.setAttribute('disabled', false);
            colorEvery.disabled = false;
        }
        
    }
