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


$(document).ready(function() {
	CKEDITOR.replaceAll(function(textarea, config){
		if (CKEDITOR.instances[textarea.name] !== undefined) {
			return false;
		}
//		return true;
	});
});


$('.xxx').keyup(function(e) {
	var currentElement = e.currentTarget;
	var autocompleted = $(currentElement).parent('.autocomplete_container').next('.autocompleted');
	console.log(currentElement);
	console.log(autocompleted);
	//delete and backspace

	if (e.which == 46 || e.which == 8 || e.which == 27) {
		$(autocompleted).hide()
	}
	textValue = $(this).val();

	if (textValue == '') {
		return false;
	}


	autocompleted.show();
	$('.test_autors', autocompleted).hide();

	$(".test_autors", autocompleted).each(function(i, item) {
		
		var expr = new RegExp("^" + textValue + "", "img");

		if (expr.exec($(item).text())) {
			//console.info($(item).text());
			$(item).show().click(function(){
				$(currentElement).val($(this).text());
				$('.test_autors', autocompleted).hide(); 
				$(autocompleted).hide();
			});
		}
	});

	return true;
});
