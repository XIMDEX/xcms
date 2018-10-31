/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

function showOrHideContent(divId, name, extra)
{
	if (isVisibleAnyContents(name) && extra == 'all') {
		hideContent(name, divId);
		document.getElementById(divId).style.display = 'none';
		return;
	}
	hideContent(name, 'none');
	var element = document.getElementById(divId);
	var state = document.getElementById(divId).style.display;
	if (state == 'none') {
		document.getElementById(divId).style.display = 'block';
	} else {
		document.getElementById(divId).style.display = 'none';
	}
}

function hideContent(divName, excluded)
{
	var elements = document.getElementsByName(divName);
	if (elements.length > 0) {
		for (i = 0; i < elements.length; i ++){
			if (elements[i].id != excluded || excluded == 'none') {
				elements[i].style.display = 'none';
			}
		}
	}
}

function isVisibleAnyContents(divName)
{
	var elements = document.getElementsByName(divName);
	if (elements.length > 0) {
		for (i = 0; i < elements.length; i ++){
			if (elements[i].style.display == 'block') {
				return true;
			}
		}
	}
	return false;
}
