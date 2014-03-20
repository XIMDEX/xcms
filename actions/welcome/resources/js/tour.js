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
 *  @version $Revision$
 */

window.X.actionLoaded(function(event, fn, params) {
    $('h3.action_tour',params.context).click(function() {
	start_tour(params.action.command);
    }.bind(this));
});
 
function start_tour(command) {
    var config_action = [
    {
	"name" 		: "li.ui-state-active a.browser-action-view:parent:parent",
	"position"	: "L"	
    }, //Explaining the help guide (1/1)
/*    {
	"name" 		: "li.ui-state-active a.browser-action-view:parent:parent",
	"position"	: "L"
    },//Explaining the help guide (2/2) */
    {
	"name" 		: "div.session-info div.language span.current-language",
	"position"	: "RT"
    },//language form
   /* {
	"name" 		: "div.mini-spotlight div.mini-spotlight-advanced",
	"position"	: "TR"
    },//Advanced Search */
    {
	"name" 		: "div.hbox-panel-container-0",
	"position"	: "L",
	"callback"	: function(){
	    var closeIco = $("div.advanced-search").parent();
	    $("a.ui-dialog-titlebar-close",closeIco).click();

	    $.getJSON(
		X.restUrl + '?method=getDefaultNode&ajax=json',
		function(data) {
		    var node_list = data['nodes'];
		    nodes = new Array();

		    if(node_list && node_list.length>0 && node_list[0]["IdNode"]) {
			nodes[0] = node_list[0]["IdNode"];
		    }
		    $("div.xim-treeview-container").treeview("navigate_to_idnode",nodes[0]);

		}.bind(this)
		);
	}
			
    },//Left Panel
    {
	"name" 		: "div.hbox-panel-container-0",
	"position"	: "L"

    },//Left Panel
    {
	"name" 		: "div.hbox-panel-container-1",
	"position"	: "R",
	"callback"	: function(){		
		
		if ($(".hbox").hbox("isHidden",0)){
			$(".hbox").hbox("showPanel",0);
			var newLeft = $(".hbox").hbox("getPanel",0).dimension().width - 250;		
			$("#tour_tooltip").animate({"left":newLeft},500);
		}
		$.getJSON(
			X.restUrl + '?method=getDefaultNode&ajax=json',
			function(data) {
				var node_list = data["nodes"];
				var idNode = node_list[0]["IdNode"];				
				var labelValue = _("Edit XML document")
				$("#bw1").browserwindow("openAction",{
                	                        	label: labelValue,
	                                	        name: labelValue,
        	                	                command:'xmleditor2',
                	                        	params:'',
	                        	                bulk:'0'
	                                	}, [idNode]);				

			}.bind(this)
		);
	}
    },//Editor
    {
	"name" 		: "div.hbox-panel-separator-0",
	"position"	: "L",
	"callback"	: function(){
		if (!$(".hbox").hbox("isHidden",0)){
			$(".hbox").hbox("hidePanel", 0);
			$("#tour_tooltip").animate({"left":25},500);
		}
	}
    },//10
    {
	"name" 		: "div.kupu-fulleditor iframe.kupu-editor-iframe",
	"position"	: "B",
	"scope"		: "editor"	
    },//Editor
    {
	"name" 		: "div.kupu-fulleditor div.kupu-tb",
	"position"	: "B",
	"scope"		: "editor"
    },//Editor
    {
	"name" 		: "button.kupu-treeview",		
	"position"	: "TL",
	"scope"		: "editor",
	"leftOffset"	: -21,
	"callback"	:   function(){
	    var frameCount = window.frames.length;
	    $( "button.kupu-treeview", window.frames[frameCount-1].document).click();
	}
    },//3
    {
	"name" 		: "button.kupu-designview",
	"position"	: "T",
	"scope"		: "editor",
	"callback"	:   function(){
	    var frameCount = window.frames.length;
	    $( "button.kupu-designview",window.frames[frameCount-1].document).click();
	}
    },//4
    {
	"name" 		: "button.kupu-prevdoc",		
	"position"	: "TL",
	"scope"		: "editor"
    },//5
    {
	"name" 		: "button.kupu-toolbox-tags-button",
	"position"	: "R",
	"scope"		:"editor"
    },//6
		
    {
	"name" 		: "button.kupu-toolbox-tags-button",
	"position"	: "R",
	"scope"		: "editor",
	"callback"	:   function(){
	    var frameCount = window.frames.length;
	    var display = $("div.kupu-toolbox-container div.xim-tagsinput-container",window.frames[frameCount-1].document).css("display");    
	    if (display == "none"){		
		$( "button.kupu-toolbox-tags-button",window.frames[frameCount-1].document).click();
	    }
	    var text = "Tutorial";
	    var index = -1;
	    var addValue = function(text,button){
				    
		if (index >= text.length){
		    clearInterval(interval);
		}else if (index >= 0){
		    var value = button.val();
		    value += text[index];
		    button.val(value);					
		}
		index++;
	    }
	    var button = $("li.xim-tagsinput-newtag input.xim-tagsinput-input:first",window.frames[frameCount-1].document);
	    button.css("width","46px");
	    button.val("");
	    var interval = setInterval(addValue, 200, text,button);
	}
    },//7
    {
	"name" 		: "div#xedit-attributes-toolbox div.xedit-element-name",
	"position"	: "B",
	"scope"		: "editor"
    },//9
    {
	"name" 		: "button.xedit-annotations-toolbox-button",
	"position"	: "R",
	"scope"		: "editor",
	"callback"	:   function(){
	    var frameCount = window.frames.length;
	    var display = $("div.kupu-toolbox-container div#xedit-annotations-toolbox",window.frames[frameCount-1].document).css("display");
	    if (display == "none")
		$( "button.xedit-annotations-toolbox-button:parent", window.frames[frameCount-1].document).click();
	}
    },//8

    {
	"name" 		: "div#xedit-annotations-toolbox button.kupu-annotation",
	"position"	: "R",
	"scope"		: "editor"
    },//8
    {
	"name" 		: "iframe.kupu-editor-iframe",	
	"position"	: "B",
	"scope"		: "editor",	
	"callback"	: function(){
	    var frameCount = window.frames.length;
	    var f = $("iframe#kupu-editor", window.frames[frameCount-1].document)[0];
	    var doc = f.contentWindow ? f.contentWindow.document :
	    f.contentDocument ? f.contentDocument : f.document;
	    $("div.item div.body p:first",doc).addClass("rng-element-selected");
	    $("div.item div.body",doc).addClass("rng-parent-selected");
	}
    }
   
		
    ];

    var arrayText = new Array;
    var arrayTitle = new Array;

    var textDescription =  "";
    var titleDescription =  "";
    textDescription = _('Welcome to the Ximdex Help Guide.<br/>In a few steps you will discover the basic features of Ximdex.');
    titleDescription = _("Ximdex Demo Tour.");
    arrayText.push(textDescription);
    arrayTitle.push(titleDescription);
    
/*    textDescription = _('Now you are going to take a look at the main parts of the Ximdex layout.');
    arrayText.push(textDescription);*/
    textDescription = _('In this bar you can choose your language for the Ximdex interface. Also you can use the search form and open the advanced search panel by clicking on the plus sign.');
    arrayText.push(textDescription);
   /* textDescription = _('This is the search bar. The plus sign opens the advanced search panel.');
    arrayText.push(textDescription);   */
    textDescription = _("In the left panel you will find the tree with all your documents, images, links and other files.");
    arrayText.push(textDescription);
    textDescription = _("Now we expand the node tree for you until we reach the picasso document.");
    arrayText.push(textDescription);    
    textDescription = _("Use Xedit, the XML editor<br/> to edit your document in a WYSIWYG way.");
    arrayText.push(textDescription);
    textDescription = _('Double-click here to close the left panel.');
    arrayText.push(textDescription);
    textDescription = _("This is the document area.<br/>Hey, it looks like a html document. Actually it's a XML file!!");
    arrayText.push(textDescription);
    textDescription = _('The editor toolbar allows you to access the basic controls: undo, redo, save, views, copy, paste...');
    arrayText.push(textDescription);    
    textDescription = _('Press the Tree View button in order to change to XML view.');
    arrayText.push(textDescription);
    textDescription = _('Press the Design View button in order to return to WYSIWYG view.');
    arrayText.push(textDescription);
    textDescription = _("We've been editing a XML file. <br/><br/>The preview button allows you to see it in the final format for yout web portal (html5, php, jsf, ...) or for other platform (digital TV, smartphone apps, etc.)<br/><br/>It's really easy to extend it with templates for oncoming technologies.");
    arrayText.push(textDescription);
    textDescription = _('On the right you will find the extra tools.');
    arrayText.push(textDescription);    
    textDescription = _('In Tags you can add tags to your document. This is useful to search the document in Ximdex.');
    arrayText.push(textDescription);
    textDescription = _("Use this box to see and modify the attributes of the XML element in use.<br/><br/>Ximdex templates allow you to freely define the structure of your XML documents and to associate semantic to elements.");
    arrayText.push(textDescription);
    textDescription = _('Find resources and semantic links related to this document.');
    arrayText.push(textDescription);    
    textDescription = _('With Xowl module you will get automagically links (to images, maps, videos) and semantic tags (annotations, references).');
    arrayText.push(textDescription);    
    textDescription = _("Now it's time to change your document.");
    arrayText.push(textDescription);    


	
    for(var i in config_action){
	config_action[i].text = arrayText[i];
	config_action[i].title = arrayTitle[i];
    }

    X.getTourInstance().start(config_action, command, false);
}
