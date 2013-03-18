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

	var htmleditor = fn('.htmleditor');
	var id_editor = htmleditor.attr("id");
	try {
		var max_height = ( action_dimension.height - 450 )+"px" ;
		var max_width = ( action_dimension.width - 120 )+"px";
	}catch(e) {
		var max_height = "340px";
		var max_width = "300px";
	}
	$("#"+id_editor).css("width", max_width);
	$("#"+id_editor).css("height", max_height);

	var lang = locale.split("_")[0];

		
		CKEDITOR.replace( id_editor,{

		toolbar:
		[
		{ name: 'document',    items : [ 'Source','-','NewPage','DocProps','Preview','Print','-' ] },
		{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-', 'Scayt' ] }, '/',
		{ name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
		{ name: 'insert',      items : [ 'Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak' ] },

		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
		'/',
		{ name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
		{ name: 'tools',       items : [ 'Maximize', 'ShowBlocks' ] }
		],

		language: lang
	});

	var max_height = max_height+"px" ;

	$("#cke_contents_"+id_editor).css("height", max_height);



	$(window).bind("action_resize", function(event, params) {
		var max_height = ( params.dimension.height - 450 )+"px";
		var max_width = ( params.dimension.width - 120 )+"px";

		$("#"+id_editor).css("width", max_width);
		$("#cke_contents_"+id_editor).css("height", max_height);
	});
});
