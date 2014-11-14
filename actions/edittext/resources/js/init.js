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
    var editor;
	//When saving changes, remove the mark
	fn(".validate").click(function(){
		fn(".editor").removeClass("unsave");
		var href= params.actionView.id;
		$tabLink = $("a[href='#"+href+"']");
		$tabLink.removeClass("unsave").parent().removeClass("unsave");
 	});

	var id_editor = fn(".editor_textarea").attr("id");
	var node_ext = fn(".node_ext").val();
    var codemirror_url = fn(".codemirror_url").val();

	var getEditor = function(){
        CodeMirror.modeURL = codemirror_url + "/mode/%N/%N.js";
        var info = CodeMirror.findModeByExtension(node_ext);
        var mode = null;
        if(info){
            mode = info.mode;
        }

	 	editor = CodeMirror.fromTextArea(document.getElementById(id_editor), {
			mode: mode,
			htmlMode: true,
			theme: "default",
			tabSize: 4,
			lineNumbers: true,
			matchBrackets: true,
			lineWrapping: true,
			tabMode: "classic",
            styleActiveLine: true,
            autoCloseBrackets: true,
            autoCloseTags: true,
            foldGutter: true,
            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
		});

        editor.on("change", function(cm){
            fn(".editor").addClass("unsave");
            cm.save();
            var href= params.actionView.id;
            $tabLink = $("a[href='#"+href+"']");
            $tabLink.addClass("unsave").parent().addClass("unsave");
            cm.save();
        })

        CodeMirror.autoLoadMode(editor,mode);

		$(".reset", params.context).bind("click", function(event) {
			var message = $('~ .submit_message', $(this)).length ? $('~ .submit_message', $(this)).val() : _("You are going to clear the current content. It won't be removed until you save the data. Are you sure?");
			var dialog = $('<div class="form_reset_dialog"><div/>').html(message);
			dialog.appendTo('body');

			$(dialog).dialog({
				title: '',
				modal: true,
				buttons: {
					accept: function() {
						editor.setValue('');
						editor.save();
						$(dialog).dialog('destroy');
						$(dialog).remove();
						return false;
					}.bind(this),
					cancel: function() {
						$(dialog).dialog('destroy');
						$(dialog).remove();
						return false;
					}.bind(this)
				}
			});

			return false;

		}); //end reset functionality.
	 	return editor;
	}; //end getEditor
    getEditor();
 });
