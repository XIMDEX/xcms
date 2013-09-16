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

	X.Collection = Object.xo_create({
	
		collection: null,
		
		_init: function(options) {
			this.options = options || {unique: false};
			this.options.unique = Object.isBoolean(this.options.unique) ? this.options.unique : false;
			// NOTE: You can overwrite the comparation method to your needs
			this.contains = (Object.isFunction(this.options.contains) ? this.options.contains.bind(this) : null) || this.contains;
			this.collection = [];
		},
		
		add: function(item) {
			if (this.options.unique && this.collection.contains(item) !== false) {
				return this;
			}
			this.collection.push(item);
			return this;
		},
		
		remove: function(item) {
			var index = this.collection.contains(item);
			if (index !== false) {
				this.collection.splice(index, 1);
			}
			return this;
		},
		
		clear: function() {
			this.collection = [];
			return this;
		},
		
		contains: function(item) {
			return this.collection.contains(item);
		},
		
		size: function() {
			return this.collection.length;
		},
		
		get: function(index) {
			if (isNaN(index)) {
				return this.asArray();
			} else {
				return this.collection[index] || null;
			}
		},
		
		asArray: function() {
			return this.collection.clone();
		},
		
		setArray: function(selection) {
			this.collection = selection.clone();
			return this;
		}
	});

})(com.ximdex);