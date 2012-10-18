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
 *  @version $Revision: 7842 $
 */


X.browser.UsersList = Object.xo_create({

	_init: function(options) {
		this.options = Object.extend({
			multi: true,
			element: 'body',
			setid: null,
			rest: {
				action: '',
				method: ''
			}
		}, options);
	},
	
	show: function() {
		$.post(
			X.restUrl,
			{
				action: this.options.rest.action,
				method: this.options.rest.method,
				setid: this.options.setid
			},
			function(data, textStatus) {
			
				data = eval(data);
				$(this.options.element).addClass('userslist');
				
				$.each(data, function(index, item) {
					var div = $('<div/>')
						.addClass('userslist-user')
						.html(item.name)
						.data('data', Object.clone(item))
						.click(function() {
							$(this).toggleClass('userslist-user-selected');
						});
					if (item.selected === true) {
						$(div).addClass('userslist-user-selected');
					}
					if (item.owner === true) {
						$(div).addClass('userslist-user-owner');
					}
					$(this.options.element).append(div);
				}.bind(this));
		
			}.bind(this)
		);
	},
	
	getSelected: function() {
		return $('.userslist-user-selected', this.options.element).map(function(index, item) {
			return $(item).data('data');
		});
	}

});

