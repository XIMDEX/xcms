/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision: 7740 $
 */


(function(X) {

    X.Tour = Object.xo_create({

	autoplay: false,
	showtime: 5000,
	step: 0,
	total_steps: 0,
	config: {},
	scope: window.document,
	uname: "",

	_init: function() {
	    this.step = 0;
	},

	start: function(config, command,scope) {
	    if (scope)
		this.scope = scope;
	    $('.tour_mark',this.scope).toggleClass('tour_mark');
	    this.hideControls();
	    this.config = {};
	    this.step = 0;
	    this.config = config;
	    this.total_steps = config.length;
	    this.showControls(command);
	    $('#activatetour',this.scope).unbind();
	    $('#canceltour',this.scope).unbind();
	    $('#endtour',this.scope).unbind();
	    $('#restarttour',this.scope).unbind();
	    $('#nextstep',this.scope).unbind();
	    $('#prevstep',this.scope).unbind();
	    $('#activatetour',this.scope).bind('click',function() {
		this.startTour();
	    }.bind(this));
	    $('#canceltour',this.scope).bind('click',function() {
		this.endTour();
	    }.bind(this));
	    $('#endtour',this.scope).bind('click',function() {
		this.endTour();
	    }.bind(this));
	    $('#restarttour',this.scope).bind('click',function() {
		this.restartTour();
	    }.bind(this));
	    $('#nextstep',this.scope).bind('click',function() {
		this.nextStep();
	    }.bind(this));
	    $('#prevstep',this.scope).bind('click',function() {
		this.prevStep();
	    }.bind(this));
	},

	restartEvents: function(scope){
	    $('#activatetour',scope).unbind();
	    $('#canceltour',scope).unbind();
	    $('#endtour',scope).unbind();
	    $('#restarttour',scope).unbind();
	    $('#nextstep',scope).unbind();
	    $('#prevstep',scope).unbind();
	    $('#activatetour',scope).bind('click',function() {
		this.startTour();
	    }.bind(this));
	    $('#canceltour',scope).bind('click',function() {
		this.endTour();
	    }.bind(this));
	    $('#endtour',scope).bind('click',function() {
		this.endTour();
	    }.bind(this));
	    $('#restarttour',scope).bind('click',function() {
		this.restartTour();
	    }.bind(this));
	    $('#nextstep',scope).bind('click',function() {
		this.nextStep();
	    }.bind(this));
	    $('#prevstep',scope).bind('click',function() {
		this.prevStep();
	    }.bind(this));
	},

	startTour: function(){

	    //Remove hidden class
	    $('#prevstep',this.scope).removeClass("hidden");
	    $('#nextstep',this.scope).removeClass("hidden");

	    $("div#tourcontrols p.subtitle").addClass("step");
	    $("div#tourcontrols p.step").addClass("subtitle");
	    var calculatedTop = $(window).height() - $('#tourcontrols',this.scope).outerHeight();
	    $('#tourcontrols',this.scope).animate({
		"left":"10px",
		"margin-top": calculatedTop+"px"
		},700);
	    $('#activatetour',this.scope).remove();
	    $('#endtour,#restarttour',this.scope).show();
	    if(!this.autoplay && this.total_steps > 1)
		$('#nextstep',this.scope).show();
	    this.showOverlay();

	    

	    this.nextStep();
	},

	nextStep: function(){
	    if(!this.autoplay){
		if(this.step > 0)
		    $('#prevstep',this.scope).removeClass("disabled");
		else
		    $('#prevstep',this.scope).addClass("disabled");
		if(this.step == this.total_steps-1)
		    $('#nextstep',this.scope).hide();
		else
		    $('#nextstep',this.scope).show();
	    }
	    if(this.step >= this.total_steps){
		//if last step then end tour
		this.endTour();
		return false;
	    }
	    ++this.step;
	    this.showTooltip();
	},

	prevStep: function(){
	    if(!this.autoplay){
		if(this.step > 2)
		    $('#prevstep',this.scope).removeClass("disabled");
		else
		    $('#prevstep',this.scope).addClass("disabled");
		if(this.step == this.total_steps)
		    $('#nextstep',this.scope).show();
	    }
	    if(this.step <= 1)
		return false;
	    --this.step;
	    this.showTooltip();
	},

	endTour: function(){
	    this.step = 0;
	    $('.tour_mark',this.scope).toggleClass('tour_mark');
	    if(this.autoplay) clearTimeout(this.showtime);
	    this.removeTooltip();
	    this.hideControls();
	    this.hideOverlay();
	    $(".hbox").hbox("showPanel",0);
	},

	restartTour: function(){
	    this.step = 0;
	    $('.tour_mark',this.scope).toggleClass('tour_mark');
	    if(this.autoplay) clearTimeout(this.showtime);
	    this.nextStep();
	},

	_getStepText: function(){

	    return  _("Step")+ " "+this.step+"/"+this.total_steps;
	},

	showTooltip: function(){
	    this.removeTooltip();

	    var step_config		= this.config[this.step-1];
	    var $elem
	    var frameCount;
	    if (step_config.scope && step_config.scope == "editor"){
		frameCount = window.frames.length;
		$elem			= $(step_config.name,window.frames[frameCount-1].document);
	    }else if (step_config.scope){
		$elem			= $(step_config.name,step_config.scope);
	    }else{
		$elem			= $(step_config.name);
	    }
	    $("div#tourcontrols p.step",this.scope)[0].innerHTML = this._getStepText();
	    $('.tour_mark',this.scope).toggleClass('tour_mark');
	    //We comment this for disable the element
//	    $(step_config.name,this.scope).toggleClass('tour_mark');

	    if(this.autoplay)
		showtime	= setTimeout('this.nextStep',step_config.time);

	    var bgcolor 		= step_config.bgcolor;
	    var color	 		= step_config.color;



	    var $tooltip		= $('<div>');
	    $($tooltip).attr('id', 'tour_tooltip');
	    $($tooltip).attr('class', 'tour_tooltip');
	    $($tooltip).html('<p>'+step_config.text+'</p><span class="tooltip_arrow"></span>');
	    $($tooltip).css({
		'display'			: 'none'
	    });

	    //position the tooltip correctly:

	    //the css properties the tooltip should have
	    var properties		= {};

	    var tip_position 	= step_config.position;

	    //append the tooltip but hide it
	    $('body', this.scope).prepend($tooltip);

	    var extra_top=0;
	    var extra_left=0;
	    if (step_config.scope == "editor"){		
		var elementFrame = $("iframe.action_iframe:last");
		extra_top = elementFrame.offset().top;
		extra_left = elementFrame.offset().left;
	    }else if (!step_config.scope && this.scope != window.document){
		extra_top = $("iframe.action_iframe",this.scope).offset().top;
		extra_left = $("iframe.action_iframe",this.scope).offset().left;
	    }

	    var leftOffset = 0;
            var topOffset = 0;
            if(step_config.leftOffset)
		leftOffset = step_config.leftOffset;
	    if (step_config.topOffset)
		topOffset = step_config.topOffset;

	    //get some info of the element
	    var e_w				= $elem.outerWidth();
	    var e_h				= $elem.outerHeight();
	    var e_l				= $elem.offset().left + extra_left + leftOffset;
	    var e_t				= $elem.offset().top + extra_top + topOffset;
	    var arrowSize			= 7;


	    switch(tip_position){
		case 'TL'	:
		    properties = {
			'left'	: e_l+ 'px',
			'top'	: e_t + e_h + arrowSize + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_TL');
		    break;
		case 'TR'	:
		    properties = {
			'left'	: e_l + e_w - $tooltip.width() + 'px',
			'top'	: e_t + e_h + arrowSize +'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_TR');
		    break;
		case 'BL'	:
		    properties = {
			'left'	: e_l + 'px',
			'top'	: e_t - $tooltip.height() - arrowSize + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_BL');
		    break;
		case 'BR'	:
		    properties = {
			'left'	: e_l + e_w - $tooltip.width() + 'px',
			'top'	: e_t - $tooltip.height() - arrowSize + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_BR');
		    break;
		case 'LT'	:
		    properties = {
			'left'	: e_l + e_w + 'px',
			'top'	: e_t + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_LT');
		    break;
		case 'LB'	:
		    properties = {
			'left'	: e_l + e_w + arrowSize +'px',
			'top'	: e_t + e_h - $tooltip.height() + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_LB');
		    break;
		case 'RT'	:
		    properties = {
			'left'	: e_l - $tooltip.width() - arrowSize + 'px',
			'top'	: e_t + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_RT');
		    break;
		case 'RB'	:
		    properties = {
			'left'	: e_l - $tooltip.width() - arrowSize + 'px',
			'top'	: e_t + e_h - $tooltip.height() + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_RB');
		    break;
		case 'T'	:
		    properties = {
			'left'	: e_l + e_w/2 - $tooltip.width()/2 + 'px',
			'top'	: e_t + e_h + arrowSize + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_T');
		    break;
		case 'R'	:
		    properties = {
			'left'	: e_l - $tooltip.width() - arrowSize + 'px',
			'top'	: e_t + e_h/2 - $tooltip.height()/2 + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_R');
		    break;
		case 'B'	:
		    properties = {
			'left'	: e_l + e_w/2 - $tooltip.width()/2 + 'px',
			'top'	: e_t - $tooltip.height() - arrowSize + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_B');
		    break;
		case 'L'	:
		    properties = {
			'left'	: e_l + e_w  + arrowSize +'px',
			'top'	: e_t + e_h/2 - $tooltip.height()/2 + 'px'
		    };
		    $tooltip.find('span.tooltip_arrow').addClass('tooltip_arrow_L');
		    break;
	    }


	    /*
			if the element is not in the viewport
			we scroll to it before displaying the tooltip
			 */
	    var w_t	= $(window).scrollTop();
	    var w_b = $(window).scrollTop() + $(window).height();
	    //get the boundaries of the element + tooltip
	    var b_t = parseFloat(properties.top,10);

	    if(e_t < b_t)
		b_t = e_t;

	    var b_b = parseFloat(properties.top,10) + $tooltip.height();
	    if((e_t + e_h) > b_b)
		b_b = e_t + e_h;


	    if((b_t < w_t || b_t > w_b) || (b_b < w_t || b_b > w_b)){
		$('html, body').stop()
		.animate({
		    scrollTop: b_t
		}, 500, 'easeInOutExpo', function(){
		    //need to reset the timeout because of the animation delay
		    if(this.autoplay){
			clearTimeout(this.showtime);
			this.showtime = setTimeout(this.nextStep,step_config.time);
		    }
		    //show the new tooltip
		    $tooltip.css(properties).show();
		});
	    }
	    else
		//show the new tooltip
		$tooltip.css(properties).show();

	    if (step_config.callback)
		step_config.callback();

	    if (step_config.title)
		$("div.tourcontrols p.tourtitle", this.scope)[0].innerHTML = step_config.title;

	},

	removeTooltip: function(){
	    $('#tour_tooltip',this.scope).remove();
	},

	showControls: function(command){
	    /*
			we can restart or stop the tour,
			and also navigate through the steps
			 */
	    var that=this;
	    $.getJSON(
		X.restUrl + '?method=getUserName&ajax=json',
		function(data) {
		    that.uname=data.username;

		    var firstTimeTextDescription = _("We're going to show you in a few steps the basic features of Ximdex. Let's go!");
		    var $tourcontrols  = '<div id="tourcontrols" class="tourcontrols">';
		    $tourcontrols += (!command) ? '<p class="tourtitle">'+ _('Hello')+ " " + this.uname + _(', first time here?') + '</p>' : '<p class="tourtitle">' + command + ' action tour</p>';
		    $tourcontrols += '<p class="subtitle">'+firstTimeTextDescription+'</p>';
		    $tourcontrols += '<span class="tour_button" id="activatetour">'+_('Start the tour')+'</span>';
		    if(!this.autoplay){
			$tourcontrols += '<div class="tour_nav"><span class="tour_button hidden" id="prevstep">< '+_('Previous')+'</span>';
			$tourcontrols += '<span class="tour_button hidden" id="nextstep">'+_('Next')+' ></span></div>';
		    }
		    $tourcontrols += '<a id="restarttour" style="display:none;">'+_('Restart the tour')+'</span>';
		    $tourcontrols += '<a id="endtour" style="display:none;">'+_('End the tour')+'</a>';
		    $tourcontrols += '<span class="tour_close" id="canceltour"></span>';
		    $tourcontrols += '</div>';

		    $('BODY', this.scope).prepend($tourcontrols);
		    this.restartEvents();
		    $('#tourcontrols',this.scope).css("margin-top", (($(window).height() - $('#tourcontrols',this.scope).outerHeight()) / 2) + $(window).scrollTop() + "px");
		    $('#tourcontrols',this.scope).css("left", (($(window).width() - $('#tourcontrols',this.scope).outerWidth()) / 2) + $(window).scrollLeft() + "px");
		    $('#tourcontrols',this.scope).draggable();
		    $('#tourcontrols span#prevstep',this.scope).draggable();
		    $('#tourcontrols span#nextstep',this.scope).draggable();
		    $('#tourcontrols a#restarttour',this.scope).draggable();
		    $('#tourcontrols a#endtour',this.scope).draggable();

		}.bind(this)
		);
			
	},

	hideControls: function(){
	    $('#tourcontrols',this.scope).remove();
	},

	showOverlay: function(){
	    var $overlay	= '<div id="tour_overlay" class="tour_overlay"></div>';
	    $('BODY',this.scope).prepend($overlay);
	},

	hideOverlay: function(){
	    $('#tour_overlay',this.scope).remove();
	}
    });



})(com.ximdex);
