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

 	X.taglist = Object.xo_create({ 
 		text: '',
 		typeTag: 'generics',
 		url: '#',
 		description: '',
 		list: null,
 		element: null,
 		container: null,
 		
		_init: function(options) {
 			
 			try {
 				//adding data
 				this.container = options.container;
 				this.list = options.list;
 				this.text = options.text;
 				this.typeTag = options.typeTag || 'generics';
 				this.url = options.url || '#';
 				this.description = options.description || '';
 				this.createTag();
 			}catch(e) {
 				//parsing element
 				 			
 			}	
 		},
 		
 		_html: function() {
	      return '<li class="xim-tagsinput-taglist xim-tagsinput-type-'+this.typeTag+'">'+
	               '<span>'+this.text+'</span>&nbsp;'+
   	         '</li>';
		},
 		
 		
 		createTag: function() {			
 			 this.list.append(this._html());
 			 this.element = this.container.tagsinput('getLastTagList');
 			 this.element.one('click', function() {
 			 	this.container.tagsinput('createTag',{text: this.text, typeTag: this.typeTag, url: this.url, description:this.description});
 			 	this.remove();
 			 }.bind(this) );
 		},
 		
 		remove: function() {
 			this.element.remove();
 		},
 		
 		getText: function() {
 			return this.text;
 		}

	});

})(X);
