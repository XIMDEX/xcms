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

	 var id_editor = fn(".editor_textarea").attr("id");
	 var max_height = ( action_dimension.height - 275 )+"px";
	 var max_width = ( action_dimension.width - 100 )+"px";
	 var node_ext = fn(".node_ext").val();


	//Forcing js load for codemirror	
         var js = $('ul.js_to_include li').map(function(index, item) {
         var url = $(item).html();
         url = Object.urldecode(url.replace(/&amp;/g, '&', url));
               return url;
         }.bind(this));
	
	var editor;
	for(var i in js){
		//First, it's loaded codemirror.js
		if (js[i].indexOf("codemirror.js")>-1){
			$.getScript(js[i],function(){
				var found=false;
				for(var i in js){
					//At second, it's loaded the specific extension js
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
			});
			break;
		}
			
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
		}
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
	}



 });
