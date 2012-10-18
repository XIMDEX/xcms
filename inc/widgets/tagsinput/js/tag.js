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

	var numTags = 0;

 	X.tag = Object.xo_create({ 
 		text: '',
 		typeTag: 'generics',
 		url: '#',
 		description: '',
 		element: null,
 		container: null,
 		numimage: 0,
 		
		_init: function(options) {
		
			this.numimage = numTags;
			numTags++;
			 this.element = options.element || null;	
			 if(null == this.element ) {
			 	this._addTag(options);
			 }else {
				this.container = options.container;
			 	this._parseTag();
			 }

 		},
 		
 		
 		_html: function() {

	      return '<li class="xim-tagsinput-tag">'+
	      			'<input type="hidden" name="tags['+this.numimage+'][text]" value="'+this.text+'" />'+
	      			'<input type="hidden" name="tags['+this.numimage+'][type]" value="'+this.typeTag+'" />'+
  	      			'<input type="hidden" name="tags['+this.numimage+'][url]" value="'+this.url+'" />'+
  	      			'<input type="hidden" name="tags['+this.numimage+'][description]" value="'+this.description+'" />'+
	               '<span>'+this.text+'</span>&nbsp;'+
     	          //  '<a href="#"  class="xim-tagsinput-tag-properties"> &infin; </strong></a>'+
   	            '<a href="#"  class="xim-tagsinput-tag-remove"> &times; </strong></a>'+
   	         '</li>';
		},
		
		getDataUrl: function() {
			var url = "&tags["+this.numimage+"][text]="+encodeURIComponent(this.text)+"&tags["+this.numimage+"][type]="+encodeURIComponent(this.typeTag)+"&tags["+this.numimage+"][url]="+encodeURIComponent(this.url)+"&tags["+this.numimage+"][description]="+encodeURIComponent(this.description);
			return url;
		
		},
 		
 		
 		_addTag: function(options) {
 		 	try {
 				//adding data
 				this.container = options.container;
 				this.text = options.text;
 				this.typeTag = options.typeTag || 'generics';
 				this.url = options.url || '#';
 				this.description = options.description || '';
 				this.createTag();
 			}catch(e) {
 				alert("error al intentar leer la etiqueta");	
 			}	
 		},
 		
 		_parseTag: function() {

 			var fields       = $(':input', this.element);
 			this.text 	     = $(fields[0]).val();
		 	this.typeTag     = $(fields[1]).val() || 'generics';
		 	this.url         = $(fields[2]).val() || '#';
	 		this.description = $(fields[3]).val() || '';
	 	
	 		//Add remove tag event 
          $('a.xim-tagsinput-tag-remove', this.element).click(function(event) {
              event.preventDefault();
  	           //$(this.element).trigger('removingtag', [{tag: this.element, text:this.text}] );
              	this.container.tagsinput('onRemovingTag', this.text);
	           this.remove();
   		}.bind(this) );

		   $(this.element).trigger("creatingtag", [{tag: this.element, text:this.text}] ); 	

 		},

 		
 		createTag: function() {			
		
			$(this._html()).insertBefore($('.xim-tagsinput-newtag', this.container));
			this.element =  $('.xim-tagsinput-tag:last', this.element);

 		/*
	 		no funciona en editor: 
	 		descomentar esta linea(y eliminar las anteriores) cuando se actualice jquery en editor
	 		
 			 $(this._html()).insertBefore(this.container.tagsinput('getInputNewTag')); 
  			 this.element = this.container.tagsinput('getLastTag');
 		*/

 			 
			 //Add remove tag event 
          $('a.xim-tagsinput-tag-remove', this.element).click(function(event) {
              event.preventDefault();
  	          // $(this.element).trigger('removingtag', [{tag: this.element, text:this.text}] );
  	           	this.container.tagsinput('onRemovingTag', this.text);
	           this.remove();
   		}.bind(this) );

		   $(this.element).trigger("creatingtag", [{tag: this.element, text:this.text}] ); 
 		},
 		
 		remove: function() {
 			$(this.element).remove();
 		},
 		
 		getText: function() {
 			return this.text;
 		}

	});

})(X);
