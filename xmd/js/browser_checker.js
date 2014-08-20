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


$(document).ready(function(){
	var allowedBrowsers = {mozilla: {'Firefox 3.x' : '^1\\.9.*'}, 
			msie: {'Internet Explorer 8.0': '8\\.0'}};
	var version = '';
	var browser = '';
	for (key in $.browser) {
		if (key == 'version') {
			version = $.browser['version'];
		}
		if ($.browser[key] == true) {
			browser = key;
		}
	}
	
	var isVersionAllowed = false;
	if (($.browser.mozilla && parseFloat($.browser.version) >= 2) ||
	($.browser.webkit) || $.browser.opera){
	    isVersionAllowed = true;
		$('#password').attr('disabled', false);
		$('#user').attr('disabled', false);
		$('#login').attr('disabled', false);
	}
	
	if (!isVersionAllowed) {

	     var incompatibilityBrowserText = '<div class="browserAdvice">';
	     incompatibilityBrowserText += _('We have noticed some compatibility issues with your current browser. For now, some features just work fully fine in Firefox 4 or higher.<br/>Please, log in with Mozilla Firefox Browser.');
//	     incompatibilityBrowserText += 'We have noticed some compatibility issues with your current browser. For now, some features just work fully fine in Firefox 4 or higher.<br/>Please, log in with Mozilla Firefox Browser.';
	     incompatibilityBrowserText +='</div>';
	     $('div#acceso form').prepend(incompatibilityBrowserText);
		$('#password').attr('disabled', true);
		$('#user').attr('disabled', 'disabled');
		$('#login').attr('disabled', 'disabled');
		
	}
});

function capLock(e){
    kc = e.keyCode?e.keyCode:e.which;
    sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
    if(((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk))
        document.getElementById('capsLockAdvice').style.visibility = 'visible';
    else
        document.getElementById('capsLockAdvice').style.visibility = 'hidden';
}

