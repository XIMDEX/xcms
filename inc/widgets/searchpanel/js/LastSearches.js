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

(function(X) {

	X.searchpanel.LastSearches = Object.xo_create(X.Collection, {

		key: null,
		cookieExpires: null,
		container: null,

		_init: function(options) {

			options = $.extend(
				{
					container: null,
					template: '<li>%s</li>'
				},
				options,
				{
					unique: false
				},
				{maxSize:10}
			);

			this.key = 'last.searches';
			this.cookieExpires = '1d';
			this.container = options.container;

			X.searchpanel.LastSearches._construct(this, options);

			var filters = X.session.get(this.key) || [];
			this.setArray(filters);
		},

		add: function(title, query) {
			var item = {title: title, query: query};
			X.searchpanel.LastSearches._super(this, 'add', item);
			X.session.set(this.key, this.asArray(), this.cookieExpires);
			this.addToContainer(item);
		},

		addToContainer: function(item) {
			if (Object.isEmpty(this.container)) return;
			var li = $(this.options.template.printf(item.title))
				.click(function(event) {
					this.container.trigger('last-searches-select', [item.query]);
				}.bind(this));
			this.container.prepend(li);
			if(this.container.children().length>10){
				this.container.children().last().remove();
			}
		},

		refreshContainer: function() {
			if (Object.isEmpty(this.container)) return;
			this.container.unbind().empty();
			var items = this.asArray();
			for (var i=0, l=items.length; i<l; i++) {
				this.addToContainer(items[i]);
			}
		}
	});

})(X);