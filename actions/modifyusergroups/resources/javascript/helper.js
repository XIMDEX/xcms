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

X.actionLoaded(function(event, fn, params) {
	
	var $form = fn('.form_group_user');
	var action = $form.attr('action');
		

	var btn1 = fn('.addgroupuser').get(0);
	if(btn1) {
		btn1.beforeSubmit.add(function(event, button) {
			$form.attr('action', action + '&method=suscribegroupuser');
		});
	}
	
	var btn2 = fn('.deletegroupuser').get(0);
	if(btn2) {
		btn2.beforeSubmit.add(function(event, button) {
			$form.attr('action', action + '&method=deletegroupuser');
		});
	}
	
	
	var btn3 = fn('.updategroupuser').get(0);
	if(btn3) {
		btn3.beforeSubmit.add(function(event, button) {
			$form.attr('action', action + '&method=updategroupuser');
		});
	}
	
});

