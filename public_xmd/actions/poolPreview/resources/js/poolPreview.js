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


//init action in READY FUNCTION
function initPoolPreview(){

	//First, get the labels for the select, in the header and in the navagation for document panel
	getLabels();

    //Add the manageList action in a div
    $('#actionManagerList').load(window.url_root + '?action=manageList&mode=single&params[type]=Label&thickbox=true', {}, function (){
    	var buttonClose = "<input type='button' value='Close' onclick='tb_remove();getLabels();' />";
        $('#actionManagerList').append(buttonClose);

    });

	//Inicializates slideBox top and buttom
	$(".headerPoolPreview").slideBox({
		width: "80%",
		height: "75px",
		position: "top",
		labelOpen: "Spread portal configurations",
		labelClose: "Colapse configuration"

	});
	$(".footerPoolPreview").slideBox({
			width: "80%",
			height: "50px",
			position: "bottom",
			labelOpen: "Spread document versions",
			labelClose: "Colapse versions"

	});

	//inicializate panels
	$('.linksTo').panel({
			'collapseType':'slide-right',
			'collapseSpeed':1000,
			'callFunctionToggle' : "tooglePanel('.linksTo')"
	});
	$('.documentsByTags').panel({
			'collapseType':'slide-right',
			'collapseSpeed':1000,
			'callFunctionToggle' : "tooglePanel('.documentsByTags')"
	});

	$('.previewActual').panel({
		'collapseType':'slide-left',
		'collapseSpeed':1000,
		'callFunctionToggle' : "tooglePanel('.previewActual')"
	});
	$('.linksBy').panel({
		'collapseType':'slide-right',
		'collapseSpeed':1000,
		'callFunctionToggle' : "tooglePanel('.linksBy')"
	});
	$('.navigate').panel({
		'collapseType':'slide-left',
		'collapseSpeed':1000,
		'callFunctionToggle' : "tooglePanel('.navigate')"
	});

	//inicializate slider for the versions in buttom slideBox
	$(".version-slider").slider({
	    animate: true,
	    change: handleSliderChange,
	    slide: handleSliderSlide
	});


	//Doing the panels draggagle
	$(".navigate").draggable();
	$(".navigate").resizable({ autoHide: true });
	$(".linksBy").draggable();
	$(".linksBy").resizable({ autoHide: true });
	$(".previewActual").draggable();
	$(".previewActual").resizable({ autoHide: true });
	$(".documentsByTags").draggable();
	$(".documentsByTags").resizable({ autoHide: true });
	$(".linksTo").draggable();
	$(".linksTo").resizable({ autoHide: true });

}

function tooglePanel(selector){
//	div = $(selector);
//	offset = div.offset();
//	console.log(offset.top);
//	console.log($(document).height());
//	console.log($(document).width());
//	h = $(document).height() - offset.top;
//	$(selector).height($(document).height());
//	console.log(h);


}
//-----------------------------------------------------
//MAIN FUNCTIONS
//-----------------------------------------------------
function loadDivsPreview(idnode, version, subversion) {
	$.blockUI({ message: '<h1> Just a moment...</h1>' });
	$('.xim-treeview-container').treeview('navigate_to_idnode', idnode);
	insertLinksTo(idnode);
	insertLinksBy(idnode);
	loadPreview(idnode, version, subversion);
	insertVersions(idnode);
	 $.unblockUI();
}
/**
 * Run the action, call it from the suggester and the treeview
 * @param event
 * @param params
 * @return
 */
function working(event, params) {
	loadDivsPreview(params.data);
}
/**
 * Collapse or expand all panels
 * @param type boolean --> true = expand and false= collapse
 * @return
 */
function tooglePanels(type){
	 $("div.panel[collapsed="+type+"]").panel('toggle');
}


//-----------------------------------------------------
//AJAX FUNCTIONS
//-----------------------------------------------------
//FOR PREVIEW
/**
 * Do the preview for this node
 * @param idnode
 * @param version
 * @param subversion
 */
function loadPreview(idnode, version, subversion){

	selectedChannel = $('.channel:options:selected').attr('value');
	urlAction = window.url_root +"?action=rendernode&mode=dinamic&nodeid="+idnode+"&channel=" + selectedChannel;
	if (version >= 0 && subversion >= 0) {
		urlAction = urlAction + '&version=' + version + '&sub_version=' + subversion;
	}
	$('.preview_loader').attr('src', urlAction);
	updateInfoAboutDocPreview(idnode, version, subversion, selectedChannel);
}


//FOR LINKS
/**
 *
 * @param idnode
 * @return
 */
function insertLinksTo(idnode){

	urlAction = window.url_root +"?action=poolPreview&idnode="+idnode+"&ajax=json&method=getLinkedNodes";
	$.ajax({
        type: "POST",
        url: urlAction,
        success: function(data){
		   data = eval('(' + data + ')');
	       links = data['links'];
	       insertLinksHtml(links, "ul-linksTo");
	    },
		error: function(datos){
//	    	alert("error");
	    }
	});

}
/**
 * Update the linksby div for this idnode
 * @param idnode
 * @return
 */
function insertLinksBy(idnode){

	urlAction = window.url_root +"?action=poolPreview&idnode="+idnode+"&ajax=json&method=getLinkNodes";
	$.ajax({
        type: "POST",
        url: urlAction,
        success: function(data){
		   data = eval('(' + data + ')');
	       links = data['links'];
	       insertLinksHtml(links, "ul-linksBy");
	    },
		error: function(datos){
//	    	alert("error");
	    }
	});
}


//FOR LABELS & VERSIONS
 /**
 * update de versions div for this idNode
 * @param idnode
 */
function insertVersions(idnode){
	urlAction = window.url_root +"?action=poolPreview&idnode="+idnode+"&ajax=json&method=getVersionsNode";
	$.ajax({
        type: "POST",
        url: urlAction,
        success: function(data){
		   data = eval('(' + data + ')');
		   insertVersionsHtml(data['verAndSubVerList'],idnode);
	    },
		error: function(datos){
	    	alert("error");
	    }
	});
}
/**
 * Get all labels
 */
function getLabels(){

	 urlAction = window.url_root +"?action=poolPreview&ajax=json&method=getLabels";
		$.ajax({
	        type: "POST",
	        url: urlAction,
	        success: function(data){

			   data = eval('(' + data + ')');
			   insertLabelsHtml(data['labels']);
		    },
			error: function(datos){
//		    	alert("error");
		    }
		});

}
/**
 * Return a version list associate to a label
 * @param idLabel
 */
function getVersionsByLabel(idLabel){
	selectedLabel = $('.labelPoolPreview:options:selected').attr('value');
	urlAction = window.url_root +"?action=poolPreview&idnode="+selectedLabel+"&ajax=json&method=getVersionsForLabel";
	$.ajax({
        type: "POST",
        url: urlAction,
        success: function(data){
		   data = eval('(' + data + ')');
		   insertVersionsForLabelHtml(data);
	    },
		error: function(datos){
//	    	alert("error");
	    }
	});

}
/**
 *
 */
function updateLabelsForVersions(selector) {

	var allCheckboxes = $('.labelCheckBoxesContainer').find('input');
	var labelsSelected = [];
	allCheckboxes.each(function(index) {
		 var checked = $(this).attr("checked");
		 if (checked){
			 labelsSelected.push($(this).val());
		 }
    });
	var idNode = $("[name=idnodePreview]").val();
	var idVersion = $("[name=idversionPreview]").val();
	var idSubVersion = $("[name=idsubversionPreview]").val();

	urlAction = window.url_root +"?action=poolPreview&ajax=json&method=asociateNodeToLabel";
	urlAction += "&idnode="+idNode+"&idversion="+idVersion+"&idsubversion="+idSubVersion+"&labels="+labelsSelected;
	$.blockUI({ message: '<h1> Just a moment...</h1>' });
	$.ajax({
        type: "POST",
        url: urlAction,
        success: function(data){
		   data = eval('(' + data + ')');
		   $.unblockUI();

		   var sms="";
		   $.each(data.sms, function(key, value){
			   sms +=value+"\n";
		   });
		   //TODO: change this alert
		   alert(sms);

	    },
		error: function(datos){
	    	alert("error");
	    	$.unblockUI();
	    }
	});
}


//FOR INFO
/**
 * Update the info in the info div for the actual doc preview
 */
function updateInfoAboutDocPreview(idnode, version, subversion, selectedChannel){
	urlAction = window.url_root +"?action=poolPreview&ajax=json&method=getInfoForPreview&idnode="+idnode;
	$.ajax({
        type: "POST",
        url: urlAction,
        success: function(data){
		   data = eval('(' + data + ')');
		   updateInfoAboutDocPreviewHtml(data.info, idnode, version, subversion);
	    },
		error: function(datos){
//	    	alert("error");
	    }
	});


}

//-----------------------------------------------------
//FUNCTIONS FOR INSERT HTML
//-----------------------------------------------------
//Create a links list
function insertLinksHtml(links,selector){
	var sel = $("."+selector)[0];
	var string = '<div class="'+selector+' listPoolPreview"><ul>';
	$.each(links, function(i) {
		string += '<li><a href="#" onclick="loadDivsPreview(' + i + ')">'+this+'</a></li>';
	});
	string += "</ul></div>";
	$("."+selector).replaceWith(string);
}
function insertVersionsHtml(versions,idnode){
	var vers='<div class="version-holder">';
	$.each(versions, function(keyVersion, valVersion){
		$.each(valVersion, function(keySubVersion, valSubVersion){
			vers += '<div class="version-item"><a href=# onclick="loadPreview('+idnode+','+ keyVersion + ','+ valSubVersion+ ')">'+keyVersion +"-"+valSubVersion+'</a></div> ';
		});
	});
	vers += '</div>';
	$(".version-holder").replaceWith(vers);

}
/**
 * Inserts the html code for the labels and inicializate the dropdown
 * @param labels
 * @return
 */
function insertLabelsHtml(labels){


	var html = "<div class='labelsForPoolPreview'><select name='labelPoolPreview' onchange='getVersionsByLabel($(this).val())' class='labelPoolPreview'>";
	html += _("<option>Select a label</option>");
	var htmlForDropDown = "<select class='labelsDropDown'>";

	$.each(labels, function(keyLabel, valueLabel){
		html += "<option value="+valueLabel.id+" >"+valueLabel.Name+"</option>";
		htmlForDropDown += "<option value="+valueLabel.id+">"+valueLabel.Name+"</option>";
	});

	html += "</select></div>";
	htmlForDropDown += "</select>";
	//insert labels in the navigate for labels panel
	$(".labelsForPoolPreview").replaceWith(html);
	//insert labels in the labels dropdown
	$(".labelsDropDown").replaceWith(htmlForDropDown);


	//inicializate dropdownwithchecklist with this labels
	var actionsList = [{
 			label: 'Apply',
 			method: "updateLabelsForVersions('.labelsDropDown')"
 		},{
 			href: true,
 			link: "<a href='#TB_inline?height=250&width=400&inlineId=actionManagerList&modal=true' class='thickbox'>Configure tags</a>"
 		}
 	];
    $(".labelsDropDown").dropdownchecklist({ maxDropHeight: 100, actions:true ,width:150, listActions: actionsList, selector:"labelCheckBoxesContainer"});

}
/**
 * Updates the Versions ul associate to a label, in the navigate for labels panel
 * @param array versions
 */
function insertVersionsForLabelHtml(versions){
	var html = '<div class="ul-DocumentsBylabels listPoolPreview"><ul>';
	$.each(versions.relations, function(keyVersion, valueVersion){
		//keyVersion = idVersion in the table Versions
		$.each(valueVersion, function(key, value){
			//Key = idNode and value = url for this node
			html += '<li><a href="#" onclick="loadDivsPreview(' + key + ')">'+value+'</a></li>';
		})

	});
	html += "</ul></div>";
	$(".ul-DocumentsBylabels").replaceWith(html);

}
function updateInfoAboutDocPreviewHtml(info, idnode, version, subversion){
	var html = "<div class='infoPreview'><ul>";
	html += "<li>URL = "+info.url+"</li>";
	html += "<li> IdNode = "+idnode+"</li></ul></div>";
	$('.infoPreview').replaceWith(html);

	//updates the hidden fields with the info about the actual doc preview
	$("[name=idnodePreview]").val(idnode);
	$("[name=idversionPreview]").val(version);
	$("[name=idsubversionPreview]").val(subversion);
}

//HANDLE FUNCTIONS FOR SLIDER VERSIONS
function handleSliderChange(e, ui){
  var maxScroll = $(".version-scroll").attr("scrollWidth") -
                  $(".version-scroll").width();
  $(".version-scroll").animate({scrollLeft: ui.value * (maxScroll / 100) }, 1000);
}

function handleSliderSlide(e, ui){
  var maxScroll = $(".version-scroll").attr("scrollWidth") -
                  $(".version-scroll").width();
  $(".version-scroll").attr({scrollLeft: ui.value * (maxScroll / 100) });
}


//-----------------------------------------------------
//INIT WIDGET
//-----------------------------------------------------
//Suggester widget
function my_suggester(options) {

	var suggesterOptions = {
		queryBuilder: function(text) {
			var qp = new QProcessor({
				parentid: 10000,
				depth: 0,
				items: 50,
				page: 1,
				condition: 'and',
				filters: [],
				sorts: []
			});
			qp.addFilter({
				comparation: 'contains',
				content: text,
				field: 'name'
			});
			//Filters for get structured document
			//
			//XmlDocument
			qp.addFilter({
				comparation: 'in',
				content:  nodeTypes.XML_DOCUMENT,
				field: 'nodetype'
			});
			qp.addSort({
				field: 'Name',
				order: 'ASC'
			});
			var query = qp.getQuery('xml');
			return {
				handler: 'SQL',
				output: 'JSON',
				query: query
			};
		},
		parse: function(data) {
			var ret = [];
			$(data.data).each(function(id, elem) {
				ret.push({
					data: [elem],
					value: elem.name,
					result: this.formatResult && this.formatResult(elem, elem.name) || elem.name
				});
			}.bind(this));
			return ret;
		},
		formatItem: function(data) {
			var ret = '';
			ret += '<div style="float: left; margin-right: 10px;"><img src="'+window.url_root + 'assets/images/icons/' + data[0].icon+'" /></div>';
			//ret += '<div>NodeID: '+data[0].id+'</div>';
			ret += '<div>'+data[0].name+'</div>';
			ret += '<div>('+data[0].nodetype+')</div>';
			return ret;
		},
		formatResult: function(result, name) {
			var ret = result.nodeid;
			//var ret = result.name;

			return ret;
		},
		width: '300px',
		max: 100,
		label: "xxxxxxxxxxxxx",
		ajax: {
			url: X.restUrl + '?action=browser&method=search',
			type: 'post',
			dataType: 'json'
		}
	};

	$(options.element).suggester(suggesterOptions).bind('itemSelected', working);
}
//Tree widget
function my_treeview(options) {
	$(options.element).treeview({
		datastore: treeview_DataStore(),
		paginator: {
			show: true
		},
		colModel: treeview_ColModel(),
		url_base: window.url_root + '/',
		img_base: window.url_root + 'assets/images/icons/'
	})

	.bind('itemClick', function(event, params) {
		if (params.data.isdir.value == 0){
			loadDivsPreview(params.data.nodeid.value);
		}
	})

	var tds = $(options.element).treeview('getDatastore');
	tds.clear();
	tds.append({
		name: {value: 'ximdex', visible: true},
		nodeid: {value: 1, visible: false},
		icon: {value: 'root.png', visible: true, type: 'image'},
		children: {value: 2, visible: false},
		isdir: {value: 1, visible: false},
		path: {value: '/', visible: false}
	});
	$(options.element).treeview('setModel', tds.get_model(), null, true);
}
