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


$('.colectorname').focusout(function() {

	var colectorName = $(this).val();
	var hasWhiteSpaces = colectorName.indexOf(' ');
	
	if (hasWhiteSpaces != -1) {
		if(confirm(_('Blank spaces has been detected. Do you want to replace them by the characteer \"_\"?'),'')) {
			while(colectorName.indexOf(" ") != -1) {
				var newColectorName = colectorName.replace(" ","_");
				colectorName = newColectorName;
			}
			$(this).attr('value', colectorName);
		} else {
			$(this).attr('value', '');
		}		
	}
});

$('.lockpagination').click(function() {

	$('input#newsperbull').attr('disabled', 'true');
	$('li.numnotbulletin').hide();
});

$('.unlockpagination').click(function() {

	$('li.numnotbulletin').show();
	$('input#newsperbull').attr('disabled', '');
});

$('.newsperpage').keypress(function(e) {

	tecla = (document.all) ? e.keyCode : e.which; // 2
	if (tecla==8) return true; // 3
	patron = /\d/;  // 4
	te = String.fromCharCode(tecla); // 5
	return patron.test(te); // 6
});

$('.newsperpage').blur(function(obj) {

	if($(obj).attr('value') == 0)
		$(obj).attr('value', 25);
});

$('.emailbulls').change(function() {
	var value = $(this).children('option:selected').text();
	var text = $('input[name=listaid]').attr('value');
	var index_first = text.indexOf(value);
	var index_others = text.indexOf(","+value);

	if(value != "Seleccionar" && value != "" && index_first != 0 && index_others == -1  ) {
		if(text != "") {
			text += ","; 
		}
		text += value;
		$('input[name=listaid]').attr('value', text);
	}
});

$('.timetogenerate').click(function() {
	setEnabling(this);
});

$('.newstogenerate').click(function() {
	setEnabling(this);
});

function setEnabling(checkfield) {

	var inputfield = 'input[name="' + $(checkfield).attr('class') + '"]';

	if ($(checkfield).attr('checked') === true) {
		$(inputfield).css('backgroundColor', '#bbb').attr('disabled', 'true');
	} else {
		$(inputfield).css('backgroundColor', '#fff').attr('disabled', '');
	}
}

$(document).ready(function() {

	if ($('.newsperpage').val() == '10000') {
		$('.lockpagination').click();
	}

	if ($('#inactive').val() == '1' || $('#inactive').val() == '3') {
		$('.timetogenerate').attr('checked', true);
		setEnabling('.timetogenerate');
	}

	if ($('#inactive').val() == '2' || $('#inactive').val() == '3') {
		$('.newstogenerate').attr('checked', true);
		setEnabling('.newstogenerate');
	}

	// disabling the deletion of languages

	//$('input[name^="langidlst"]:checked').attr('disabled', 'true');
});


