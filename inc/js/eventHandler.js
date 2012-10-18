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
 *  @version $Revision: 7740 $
 */


(function(X) {

	// Example of accelerator/event definition
	/*
	var defaultAccels = [
		{event: 'tab-close', ctrlKey: true, shiftKey: true, char: 'c'},
		{event: 'tab-left', ctrlKey: true, shiftKey: true, keyCode: 37},
		{event: 'tab-right', ctrlKey: true, shiftKey: true, keyCode: 39},
		{event: 'project-view-tree', ctrlKey: true, char: '1'},
		{event: 'project-view-grid', ctrlKey: true, char: '2'},
		{event: 'project-view-list', ctrlKey: true, char: '3'},
		{event: 'tab-dummy-action', ctrlKey: true, char: '0'}
	];
	*/

	X.EventHandler = Object.xo_create({
	
		accels: null,
		
		_init: function(accels) {
			this.accels = accels || [];
		},
		
		/**
		 * Returns the matched event or false
		 */
		match: function(event) {
		
			var match = false;

			for (var i=0,l=this.accels.length; i<l; i++) {
				
				var accel = this.accels[i];
				
				// IE tricks
				var codeChar = String.fromCharCode(event.charCode);
				var keyChar = String.fromCharCode(event.keyCode);
				var button = event.button === 0 ? undefined : event.button;
				// IE tricks
				
				accel = $.extend({
					event: null,
					ctrlKey: false,
					shiftKey: false,
					altKey: false,
					metaKey: false,
					char: '',
					keycode: null,
					charCode: null,
					button: undefined
				}, accel);
				
				match = event.ctrlKey === accel.ctrlKey;
				match = match && event.shiftKey === accel.shiftKey;
				match = match && event.altKey === accel.altKey;
//				match = match && event.metaKey === accel.metaKey;
				match = match && button === accel.button;
				match = match && (event.charCode === accel.charCode
					|| event.keyCode === accel.keyCode
					|| codeChar.toUpperCase() === accel.char.toUpperCase()
					|| keyChar.toUpperCase() === accel.char.toUpperCase()
				);

				/*console.group(item.event);
				console.log('event: ', event.ctrlKey, event.shiftKey, event.charCode, event.keyCode, char);
				console.log('accel: ', accel.ctrlKey, accel.shiftKey, accel.charCode, accel.keyCode, accel.char);
				console.log('result: ', fire, event);
				console.groupEnd(item.event);*/
				
				if (match) {
//					console.info('Firing ', accel.event);
					match = accel;
					break;
				}
			}
			
			return match;
		}
	});

})(com.ximdex);