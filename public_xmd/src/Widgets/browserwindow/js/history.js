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

	X.browser.History = Object.xo_create(X.Collection, {
		_init: function(options) {
			X.browser.History._construct(this, options);
			this.options.maxItems = (isNaN(this.options.maxItems) || this.options.maxItems < 1)
				? 100
				: this.options.maxItems;
			this.index = -1;
		},
		add: function(item) {
			if (this.size() > this.options.maxItems) {
	//			console.info(this.size() + ' =? ' + this.options.maxItems);
				this.clear();
			}
			X.browser.History._super(this, 'add', item);
			this.index = this.size() - 1;
			return this;
		},
		getPrevious: function() {
			var item = this.get(this.index-1);
			if (item !== null) {
				this.index--;
			}
			return item;
		},
		getNext: function() {
			var item = this.get(this.index+1);
			if (item !== null) {
				this.index++;
			}
			return item;
		}
	});

})(com.ximdex);
