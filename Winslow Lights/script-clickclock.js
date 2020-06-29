// JavaScript Document
//<![CDATA[

	/* ============================================ CLICK CLOCK ============================================ *\
		plugin that open & manage a input supposed to received only a time value.
		It is more user-friendly to deal with time.
		
		USE -
			to use it, considering the following html :
			<input type="text" value="00:00" id="foo">
			var clickClok = $("#foo").clickClock({
				dimClock	: 300,
				thickClock	: 10,
				stepHour	: 1,
				stepMinute	: 5
			});
			-or-
			var clickClok = $("#foo").clickClock();
			list of available methods
			clickClok.focus();	// put the focus on it & prepare the clock
			clickClok.close();	// close the current clock
			clickClok.closeAll();	// close all the opened clocks from the current document
			
		
		CREDIT -
			Sebastien Pipet (https://www.facebook.com/sebastien.pipet)
		VERSION -
			1.2
		DISCLAIMER -
			All this code is free : you can use, redistribute and/or modify it without any consentement.
			Please just leave my name on it ;-)
		DEFAULT VALUES -
			you can customise the defaults values below :
	/* ========================================= DEFAULT VALUES ============================================ */

	var	sizeDefaultBox = 200;				// dimensions of the box containing the clock, in pixels. Have to be between 100 and 800 px.
	var	thickDefaultClock = 5;				// thickness of the edge of the clock. Have to be between 0 and 50 px.
	var	hourDefaultStep = 1;				// the step between the displayed hours of the clock. Have to be between 1 and 12.
	var	minuteDefaultStep = 5;				// the step between the displayed minutes of the clock. Have to be between 1 and 60.
	var	linkCSS = "clickclock.css";			// link of the attached CSS file
	var	am = "morning";					// text to display for AM / morning
	var	pm = "afternoon";				// text to display for PM / afternoon
	var	closeMessage = "close";				// text to display for the X (close) button
	var	questionMessage = "To switch AM/PM. simply click on the center of the clock. To select a hour/minute, click on the desired value. Or fill the form";				// text to display for the ? button
	var	formatAMPM = true;				// format 24h (=true) vs. 12h (=false)
	var	isAnim = true;					// when closing the clock, put a brief animation (=true), or not (=false)

	/* =================================================================================================== */


// define the sprintf function
if(typeof sprintf != 'function'){
	window.sprintf = function(i,nbrIntMin){
		var output = i + '';
		while (output.length < nbrIntMin) {
			output = '0' + output;
		}
		return output;
	}

}

(function ( $ ) {

	// loading CSS file
	if(!document.getElementById("stylesCSS")){
		var head  = document.getElementsByTagName('head')[0];
		var link  = document.createElement('link');
		link.id   = "clickClockCSS";
		link.rel  = 'stylesheet';
		link.type = 'text/css';
		link.href = linkCSS;
		link.media = 'all';
		head.appendChild(link);
	}
	// loading all occurs of the plugin into an object
	var listClickClock = {};

	$.fn.clickClock = function(params) {

		// if multiple elements, we split them. Only once they are "alone", we keep doing further
		if (this.length > 1){
			this.each(function() { $(this).clickClock(params) });
			return this;
		}


		// ================= PRIVATE PROPERTIES =================


		var clickClock = this;

		// customizable parameters available, with the default values
		params = $.extend({
			dimClock	: sizeDefaultBox,
			thickClock	: thickDefaultClock,
			stepHour	: hourDefaultStep,
			stepMinute	: minuteDefaultStep
		}, params);

		var	dc = Math.min(800,Math.max(params.dimClock,100)),			// hauteur = largeur de l'ensemble	(100-800)
			thickClock = Math.min(50,Math.max(params.thickClock,0)),	// epaisseur trait horloge		(0-50)
			stepHour = Math.min(12,Math.max(params.stepHour,1)),			// le pas d'affichage entre les heures	(1-12)
			stepMinute = Math.min(60,Math.max(params.stepMinute,1)),			// le pas d'affichage entre les minutes	(1-60)

			focusOnHours	 = false,
			focusOnMinutes	 = false,
			inputHasFocus	 = false,
			currentValue	 = '00:00',		// default value

			iname,					// name of the (unique) class of the item (= ID)
			clockGlobalContainer,			// div englobing the input + clock
			clockAppendice,				// small arrow that connect graphicly the clock to the input
			clockContainer,				// div that contain the clock + close button + textlegend
			clockClose,				// close button (right top corner)
			clockLegend,				// show some informations
			clock,					// the clock
			clockCenter,				// center of the clock
			hourHand,				// hour hand
			minuteHand,				// minute hand
			clockChangeAMPM;			// invisible div that allows to change hours AM <-> PM


		// ================= PUBLIC PROPERTIES =================


		clickClock.credit = 'sebastien pipet';


		// ================= PRIVATE FUNCTIONS =================


		// init the plugin : instanciation
		var CC_intialize = function() {
			// we create a custom ID (wich is actually a (unique) class)
			counter = CC_countItems();
			iname = "clickClock_input_"+counter;
			clickClock.addClass(iname)

			// the plugin HAS TO BE used on input (of text type). We first check it.
			if(! $("."+iname).is('input:text') ){
				console.log("clickClock plugin : fail, it has to be used on a input of type text.");
				return false;
			}else{
				// we store the element into the clickClockList
				listClickClock[counter] = clickClock;

				// the value of it HAS TO BE "HH:MM"
				currentValue = CC_getTime(true)+':'+CC_getTime(false);
				clickClock.val(currentValue);

				// we block any entry that is not a number
				clickClock.keypress(function(event){
					var charCode = (event.keyCode ? event.keyCode : event.which);
					if(	charCode==8	/* backspace	*/
					||	charCode==9	/* tab		*/
					||	charCode==13	/* enter	*/
					||	charCode==16	/* shift	*/
					||	charCode==37	/* arrow left	*/
					||	charCode==38	/* arrow up	*/
					||	charCode==39	/* arrow right	*/
					||	charCode==40	/* arrow down	*/
					||	charCode==46	/* del		*/ ){
						return true;
					}else{
						if((charCode >= 48 && charCode <= 57)||(charCode == 45)){
							// numbers
							return true;
						}else{
							// something else, we block
							return false ;
						}
					}
				});

				return clickClock;
			}
		};
		// creating the clock
		var CC_initClock = function(){
			// first, we close all existing clocks
			CC_destroyAll();
			
			// we warp the input into a brand-new-div to be able to manage the absolute positionning
			$("."+iname).wrap('<div id="'+iname+'_globalContainer" class="clockGlobalContainer"></div>');

			// creating the skeleton of the clock -
			clockGlobalContainer = $("#"+iname+"_globalContainer");
			clockGlobalContainer.append('<div class="clockAppendice"></div><div class="clockContainer"><div class="clockClose" title="'+closeMessage+'">x</div><div class="clock"><div class="clockCenter"></div><div class="changeAMPM hidden"></div><div class="hourHand"></div><div class="minuteHand"></div><input type="hidden" class="js-format-hour" value="am" /></div><div class="clockLegend">?<span>'+questionMessage+'</span></div></div>');
			clockAppendice		= clockGlobalContainer.find(".clockAppendice");
			clockContainer		= clockGlobalContainer.find(".clockContainer");
			clockClose		= clockGlobalContainer.find(".clockClose");
			clockLegend		= clockGlobalContainer.find(".clockLegend");
			clock			= clockGlobalContainer.find(".clock");
			clockCenter		= clockGlobalContainer.find(".clockCenter");
			clockHourHand		= clockGlobalContainer.find(".hourHand");
			clockMinuteHand		= clockGlobalContainer.find(".minuteHand");
			clockChangeAMPM		= clockGlobalContainer.find(".changeAMPM");

			// set the correct value of js-format-hour
			clockGlobalContainer.find(".js-format-hour").val((CC_getTime(true)<13&&CC_getTime(true)!=00)?'am':'pm');

			// editing the dimentions -
			var valApp = Math.min(30,(dc/8));
			clockGlobalContainer.css({
				"height" :	$("."+iname).outerHeight(false)+"px",
				"width" :	$("."+iname).outerWidth(false)+"px"
			});
			clockAppendice.css({
				"height" :	valApp+"px",
				"width" :	valApp+"px",
				"left" :	valApp+"px"
			});
			clockContainer.css({
				"height" :	(dc)+"px",
				"width" :	(dc)+"px",
				"top" :		$("."+iname).outerHeight(false)+(valApp/2)+"px",
				"left" :	0+"px"
			});
			clockClose.css({
				"height" :	(dc/8)+"px",
				"width"	:	(dc/8)+"px",
				"line-height" :	(dc/8)+"px",
				"font-size" :	(dc/8)+"px"
			});
			clockLegend.css({
				"height" :	(dc/8)+"px",
				"width"	:	(dc/8)+"px",
				"line-height" :	(dc/8)+"px",
				"font-size" :	(dc/8)+"px"
			});
			clockLegend.find("span").css({
				"width"	:	(dc+2*10-2*5)+"px",
				"top" :		(dc/8+2)+"px"
			});
			clock.css({
				"height" :	(dc-2*thickClock)+"px",
				"width" :	(dc-2*thickClock)+"px",
				"border-width" :thickClock+"px"
			});
			clockChangeAMPM.css({
				"height":(dc*2/3-2*thickClock)+"px",
				"width":(dc*2/3-2*thickClock)+"px",
				"margin-left":"-"+(dc/3-thickClock)+"px",
				"margin-top":"-"+(dc/3-thickClock)+"px"
			});
			
			// we put a listener on the clockClose
			clockClose.click(function() {
				CC_destroy();
			});

			// once all is done, we load the hours
			CC_getHour();
		};
		// creating the hours part
		var CC_getHour = function() {
			var	nombreHeures = 24,
				currentHeure = CC_getTime(true);
			if(!focusOnHours){
				// reset settings & prepare hours
				focusOnHours = true;
				focusOnMinutes = false;
				// check input + select
				CC_setSelect(currentHeure+':'+CC_getTime(false),true);
				clockContainer.find(".clockMinutes").remove();
				// adding the hours
				for (var i=0; i<nombreHeures; i+=stepHour) {
					clock.append('<div id="'+iname+'_h_'+i+'" class="clockHour'+((i>0&&i<13)?' am':' pm')+((sprintf(i,2)===currentHeure)?' clockSelected':'')+'">'+(sprintf((i%((formatAMPM)?24:12)),2))+'</div>');
					var	angle = Math.PI / (nombreHeures/2) * 2,
						h = $("#"+iname+"_h_"+i+"");
						/*
							x = x0 + r*cos(t)
							y = y0 + r*sin(t)
						*/
					h.css({
						"height":(dc/6)+"px",
						"width":(dc/6)+"px",
						"line-height":(dc/6)+"px",
						"font-size":(dc/10)+"px",
						"left":( (dc/2-dc/12+10) + ((dc/2-dc/12)-thickClock) * Math.cos(angle * i - Math.PI/2) )+"px",
						"top":(  (dc/2-dc/12+10) + ((dc/2-dc/12)-thickClock) * Math.sin(angle * i - Math.PI/2) )+"px"
					});
				}
				// we switch the inforation AM/PM
				if(currentHeure>(nombreHeures/2)){
					clockContainer.find(".am").addClass("hidden");
					clock.find('.changeAMPM').removeClass("hidden").html("<span>"+pm+"</span>");
				}else{
					clockContainer.find(".pm").addClass("hidden");
					clock.find('.changeAMPM').removeClass("hidden").html("<span>"+am+"</span>");
				}
				// trigger the switching AM/PM
				clock.find('.changeAMPM').click(function() {
					var ampm = clock.find('.js-format-hour');
					if(ampm.val()=="am"){
						ampm.val("pm");
						$(this).html("<span>"+pm+"</span>");
						clockContainer.find(".am").addClass("hidden");
						clockContainer.find(".pm").removeClass("hidden");
					}else{
						ampm.val("am");
						$(this).html("<span>"+am+"</span>");
						clockContainer.find(".pm").addClass("hidden");
						clockContainer.find(".am").removeClass("hidden");
					}
					// we reset the focus on the input, lost with the click
					CC_setSelect(currentHeure+':'+CC_getTime(false),true);
				});
				// listener on the hours
				clock.find('.clockHour').click(function() {
					var	currentHeure = sprintf($(this).html(),2);
					// we reset the focus on the input, lost with the click
					CC_setSelect(currentHeure+':'+CC_getTime(false),true);
					clock.find('.changeAMPM').addClass("hidden");
					// we take off the hours, then load the minutes
					clockContainer.find(".clockHour").fadeOut(((isAnim)?"slow":1),function(){
						clockContainer.find(".clockHour").remove();
						CC_getMinutes();
					});
				});
			}
		};
		// creating the minutes hands
		var CC_getMinutes = function() {
			var	nombreMinutes = 60,
				currentMinute = CC_getTime(false);
			if(!focusOnMinutes){
				// reset settings & prepare hours
				focusOnHours = false;
				focusOnMinutes = true;
				// check input + select
				CC_setSelect(CC_getTime(true)+':'+currentMinute,false);
				clockContainer.find(".clockHour").remove();
				clock.find('.changeAMPM').addClass("hidden");
				// adding the minutes
				for (var i=0; i<nombreMinutes; i+=stepMinute) {
					clock.append('<div id="'+iname+'_m_'+i+'" class="clockMinutes'+((sprintf(i,2)===currentMinute)?' clockSelected':'')+'">'+(sprintf(i,2))+'</div>');
					var	angle = Math.PI / nombreMinutes * 2,
						m = $("#"+iname+"_m_"+i+"");
					m.css({
						"height":(dc/6)+"px",
						"width":(dc/6)+"px",
						"line-height":(dc/6)+"px",
						"font-size":(dc/10)+"px",
						"left":( (dc/2-dc/12+10) + ((dc/2-dc/12)-thickClock) * Math.cos(angle * i - Math.PI/2) )+"px",
						"top":(  (dc/2-dc/12+10) + ((dc/2-dc/12)-thickClock) * Math.sin(angle * i - Math.PI/2) )+"px"
					});
				}
				// listener on the minutes
				clock.find('.clockMinutes').click(function() {
					var	currentMinute = sprintf($(this).html(),2);
					// we reset the focus on the input, lost with the click
					CC_setSelect(CC_getTime(true)+':'+currentMinute,false);
					// we take off the minutes, then load the closing
					clockContainer.find(".clockMinutes").fadeOut(((isAnim)?"slow":1),function(){
						clockContainer.find(".clockMinutes").remove();
						CC_destroy();
					});
				});
			}

		};
		// get the hours/minutes from a input
		var CC_getTime = function(isHour) {
			var currentValue = $('.'+iname).val();
			if(!currentValue.match(/^([01]?[0-9]|2[0-3]):[0-5]?[0-9]$/)){
				// if value is syntaxically wrong, we force updating it
				currentValue = "00:00";
				 $('.'+iname).val(currentValue);
			}
			// now we can retreive the wanted values
			var	cvs = currentValue.split(":"),
				currentHeure = sprintf(cvs[0],2);
				currentMinute = sprintf(cvs[1],2);
			return (isHour)?currentHeure:currentMinute;
		};
		// function that counts the number of items (for old browsers)
		var CC_countItems = function() {
			var count = 0;
			for (var i in listClickClock) {
				if (listClickClock.hasOwnProperty(i)) {
					count++;
				}
			}
			return count;
		};
		// set a mouse-selection of the minutes/hours into the current input
		var CC_setSelect = function(v,isHour) {
			var	input = document.getElementsByClassName(iname);
			input[0].value = v;
			// focus given to console, a small timer to get back on the windows
			setTimeout(function() {
				input[0].focus();
			}, 1);
			// hack - to give the focus
			if(isHour){
				input[0].setSelectionRange(0,2);
			}else{
				input[0].setSelectionRange(3,5);
			}
			// and updating the needles of the clock
			CC_updateHand();
		};
		// rotation of the clock's hands from a defined angle
		var CC_updateHand = function() {
			var	angle;
			// first, the hour -
			var h = CC_getTime(true);
			angle = (h%12)*360/12;
			clock.find(".hourHand").css({
				'-webkit-transform'	:'rotateZ('+angle+'deg)',
				'transform'		:'rotateZ('+angle+'deg)'
			});
			// then, the minutes
			var m = CC_getTime(false);
			angle = (m%60)*360/60;
			clock.find(".minuteHand").css({
				'-webkit-transform'	:'rotateZ('+angle+'deg)',
				'transform'		:'rotateZ('+angle+'deg)'
			});
		};
		// function that destroys the clock
		var CC_destroy = function() {
			inputHasFocus	 = false;
			focusOnHours	 = false;
			focusOnMinutes	 = false;
			if(clockAppendice !== undefined){
				clockAppendice.fadeOut(((isAnim)?200:2),function(){
					$(this).remove();
				});
			}
			if(clockContainer !== undefined){
				clockContainer.find(".clockHour").fadeOut(((isAnim)?"fast":1));
				clockContainer.find(".clockMinutes").fadeOut(((isAnim)?"fast":1));
				clockContainer.slideUp(((isAnim)?"fast":1),function(){
					$(this).remove();
					clockGlobalContainer.contents().unwrap();
				});
			}
			clickClock.blur();
		};
		// function that destroys ALL the clock
		var CC_destroyAll = function() {
			for (var i in listClickClock) {
				if(listClickClock[i]!==clickClock){
					listClickClock[i].close();
				}
			}
		};


		// ================= PUBLIC FUNCTIONS =================


		// when the input get the focus, we display the clock if she's not here yet
		clickClock.focus(function() {
			if(!inputHasFocus){
				inputHasFocus = true;
				CC_initClock();
			}
		});
		// fonction called from the DOM to close/destroy the clock
		clickClock.close = function() {
			CC_destroy();
		};
		// fonction called from the DOM to close/destroy ALL the clocks
		clickClock.closeAll = function() {
			CC_destroyAll();
		};
		// we deal with the direct entries
		clickClock.keypress(function(e){
			if(e.which===9){	// TAB
				CC_destroy();
			}else if(e.which===13){	// ENTER
				CC_destroy();
			}else if(e.which>=48 && e.which<=57){
				// we check the position of the entry :
				var start = e.target.selectionStart;
				//var end = e.target.selectionEnd;
				var	h1 = (CC_getTime(true)).charAt(0),
					h2 = (CC_getTime(true)).charAt(1),
					m1 = (CC_getTime(false)).charAt(0),
					m2 = (CC_getTime(false)).charAt(1);
				var entryInput = e.which - 48,
					input = document.getElementsByClassName(iname);
				switch(start) {
					case 0:	// first number of the hour
						switch(entryInput){
							case 0:
							case 1:
							case 2:
								// may be followed by another number, we leave space for it.
								setTimeout(function() {
									CC_setSelect(entryInput+':'+CC_getTime(false),true);
									CC_getHour();
									// we put the focus straight after, to welcome another number
									input[0].setSelectionRange(1, 1);
								}, 1);
							break;
							default:
								// other case, we add a 0 in front of the hour, then move to the minutes
								setTimeout(function() {
									CC_setSelect('0'+entryInput+':'+CC_getTime(false),true);
									CC_getMinutes();
									// set focus just after, for the minutes
									input[0].setSelectionRange(3, 5);
								}, 1);
							break;
						}
					break;
					case 1:	// second number of the hours.
						var h = parseInt(h2,10)*10 + entryInput;	// h1 is actually n2, because we set 0+h1
						// if the final hour/number is >24, we split the values into the hours&minutes, if not, just switch to the minutes
						if(h < 24){
							// focus on the minutes!
							setTimeout(function() {
								CC_setSelect(CC_getTime(true)+':'+CC_getTime(false),false);
								CC_getMinutes();
							}, 1);
						}else{
							// in that case, we assume that the first number of the hour is actually the hour, so we move the new value into the minutes
							setTimeout(function() {
								// checking if the number is a 10th or a simple minute
								CC_getMinutes();
								if(entryInput<6){
									// 10th
									CC_setSelect('0'+h2+':'+entryInput+'0',false);
									// we move the focus just after, to welcome the last number
									input[0].setSelectionRange(4, 5);
								}else{
									// we have a total
									CC_setSelect('0'+h2+':0'+entryInput,false);
									CC_destroy();
								}
							}, 1);
						}
					break;
					case 3:	// first number of the minutes
					case 5: // in case we have too many chars, we crop & focus on the minutes
						if(entryInput>5){
							// we consider it like as under 10 (cant be more)
							setTimeout(function() {
								$("."+iname).val(CC_getTime(true)+':'+'0'+entryInput);
								CC_destroy();
							}, 1);
						}else{
							// 10thm we wait for the unit. In case we are writing a 6th char(or more), we force a checkup
							CC_setSelect(h1+h2+':'+entryInput,false);
							CC_getMinutes();
						}
					break;
					case 4: // second minutes's number, can be anything
						CC_getMinutes();
						CC_destroy();
					break;
					case 2: // the separator. We set the focus on the minutes
							CC_getMinutes();
							setTimeout(function() {
								CC_setSelect(h1+h2+':'+m1+m2,false);
							}, 1);
					break;
					default:
						CC_setSelect(h1+h2+':'+m1+m2,true);
					break;
				}
			}
		});

		return CC_intialize();
	}
}( jQuery ));


 // ]]>