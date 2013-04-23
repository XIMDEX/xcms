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


 X.actionLoaded(function(event, fn, params)  {


 	$(".validate").click(function(){
		//$(".editor").css("border","0px");
		$(".editor").removeClass("unsave");
		var href= params.actionView.id;
		$("a[href='#"+href+"']").removeClass("unsave");
		$("a[href='#"+href+"']").css("font-style","normal");

 	});




	 var id_editor = fn(".editor_textarea").attr("id");
	 var max_height = ( action_dimension.height - 275 )+"px";
	 var max_width = ( action_dimension.width - 100 )+"px";
	 var node_ext = fn(".node_ext").val();
	 var dependencies = [];


	//Forcing js load for codemirror
    var js = $('ul.js_to_include li').map(function(index, item) {
        var url = $(item).html();
        url = Object.urldecode(url.replace(/&amp;/g, '&', url));
        return url;
    }.bind(this));


    var editor;
    var buildDependencies = function(){

    	if (node_ext == "php"){
    			dependencies.push("xml");
    			dependencies.push("javascript");
    			dependencies.push("css");
    			dependencies.push("clike");
    	}

    };


    var loadNodeExtensionJs = function(){
    	var found=false;
		for(var i in js){
			if (js[i].indexOf(node_ext+".js") > -1){
				found = true;
				$.getScript(js[i], function(){
					//The editor is created only when script is loaded
					editor = getEditor();
				});
				break;
			}
		}

		//if node_ext doesnt exist, the editor is loaded anyway
		if (!found)
			editor = getEditor();
    }

    var isInDependencies = function(jsPath){

    	var result = false;
    	for (var i in dependencies){
    		if (jsPath.indexOf(dependencies[i])>-1){
    			return true;
    		}
    	}
    	return result;
    }

	var loadDependenciesJs = function(indice){

		var i;
		for ( i = indice; i < js.length; i++) {

			if (isInDependencies(js[i])){
				$.getScript(js[i], function(){
					loadDependenciesJs(i+1);
				});
				break;
			}
		};
		if (i == js.length)
			loadNodeExtensionJs();
	}


         var getEditor = function(){

	 		var hlLine = 0;
	 		var editor = CodeMirror.fromTextArea(document.getElementById(id_editor), {
				mode: node_ext,
				htmlMode: true,
				theme: "default",
				lineNumbers: true,
				matchBrackets: true,
				tabMode: "classic",
				onCursorActivity: function(ins){
					editor.setLineClass(hlLine, null);
					hlLine = editor.setLineClass(editor.getCursor().line, "currentLine");

				},
			 	onGutterClick: function(cm, n) {
					var info = cm.lineInfo(n);
					if (info.markerText)
						cm.clearMarker(n);
					else
						cm.setMarker(n, "<span style=\"color: #900\">&bull;</span> %N%");
				},
				onChange: function(cm) {
					//$(".editor").css("border","1px solid red");
					$(".editor").addClass("unsave");
					var href= params.actionView.id;
					$("a[href='#"+href+"']").addClass("unsave");
					$("a[href='#"+href+"']").css("font-style","italic");
					cm.save();
				}
			});

	$(".reset", params.context).bind("click", function(event) {
		var message = $('~ .submit_message', $(this)).length ? $('~ .submit_message', $(this)).val() : _("You are going to clear the current content. It won't be removed until you save the data. Are you sure?");
		var	dialog = $('<div class="form_reset_dialog"><div/>').html(message);
		dialog.appendTo('body');

		$(dialog).dialog({
				title: '',
				modal: true,
				buttons: {
					_('Accept'): function() {
						editor.setValue('');
						editor.save();
						$(dialog).dialog('destroy');
						$(dialog).remove();
						return false;
					}.bind(this),
					_('Cancel'): function() {
						$(dialog).dialog('destroy');
						$(dialog).remove();
						return false;
					}.bind(this)
				}
		});

		return false;

	});
	$(".CodeMirror").css("max-height", max_height);
	$(" .CodeMirror-scroll").css("max-height", max_height);
	$(".CodeMirror").css("max-width", max_width);
	$(" .CodeMirror-scroll").css("max-width", max_width);

	$(window).bind("action_resize", function(event, params) {
		var max_height = ( params.dimension.height - 275 )+"px";
		var max_width = ( params.dimension.width - 100 )+"px";

		fn(".CodeMirror").css("max-height", max_height);
		fn(".CodeMirror-scroll").css("max-height", max_height);
		$(".CodeMirror").css("max-width", max_width);
		$(" .CodeMirror-scroll").css("max-width", max_width);
	 });
	 return editor;
	};


	buildDependencies();
	for(var i in js){
    		//First, it's loaded codemirror.js
    		if (js[i].indexOf("codemirror.js")>-1){
    			$.getScript(js[i],function(){

    				if (dependencies.length){
    					loadDependenciesJs(0);
    				}
    				else{
    					loadNodeExtensionJs();
    				}

    			});
    			break;
    		}

    	}


 });
