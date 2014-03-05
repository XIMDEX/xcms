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
 		namespace:'custom',
 		isEmbed: false,
 		selectWrap:false, //html to wrap the select input

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

 			$selects = $('select', this.element);
			$selects.html(X.ontologyTypeLikeOptions);
			$selects.inputSelect();
			$selects.inputSelect("select",this.typeTag);

			$(this.element).click(function(event){
				$select = $("select", this.element);
				$select.inputSelect();
				event.preventDefault();
			})
 		},

		_editCurrentTag: function(event){
			var $span = $(event.currentTarget);
			if (event.originalEvent.type =="blur" || (event.originalEvent.type="keydown" && (event.which == 9  || event.which == 13))){
	                         if (this.container.tagsinput("isUniqueText",$span.text().trim(), this.numimage)){
        	                         $('.xim-tagsinput-newtag input#tag_input', this.container).focus();
                                         var fields = $('input', this.element);
					 this.text = $span.text().trim();
                                         $(fields[0]).val($span.text().trim());
                                 }else{
                                         $span.text(this.text);
                                         $(this.element).addClass("error");
                                 }
                               event.preventDefault();
                	}

		},

 		_html: function() {

		  if (this.typeFixed){
                        return '<li style="display:none" class="xim-tagsinput-tag xim-tagsinput-type-'+this.typeTag+'" hidden>'+
                                                '<div class="type-selector">'+
                                '<div name="type" class="selection icon type-'+this.typeTag+'" data-value="'+this.typeTag+'"></div></div>'+
                                '<input type="hidden" name="tags['+this.numimage+'][text]" value="'+this.text+'" />'+
                                '<input type="hidden" name="tags['+this.numimage+'][type]" value="'+this.typeTag+'" />'+
                                '<input type="hidden" name="tags['+this.numimage+'][url]" value="'+this.url+'" />'+
                                '<input type="hidden" name="tags['+this.numimage+'][description]" value="'+this.description+'" />'+
                                '<span class="xim-tagsinput-text" data-tooltip="'+this.text+'">'+this.text+'</span>'+
                                '<a href="#"  class="xim-tagsinput-tag-remove icon"> &times; </strong></a>'+
                        '</li>';

                  }else{
                        return '<li style="display:none" class="xim-tagsinput-tag xim-tagsinput-type-'+this.typeTag+'" hidden>'+
                                '<select name="type" class="hidden ximdexInput icon button type-selector vertical collapsable"></select>'+
                                '<input type="hidden" name="tags['+this.numimage+'][text]" value="'+this.text+'" />'+
                                '<input type="hidden" name="tags['+this.numimage+'][type]" value="'+this.typeTag+'" />'+
                                '<input type="hidden" name="tags['+this.numimage+'][url]" value="'+this.url+'" />'+
                                '<input type="hidden" name="tags['+this.numimage+'][description]" value="'+this.description+'" />'+
                                '<span class="xim-tagsinput-text" data-tooltip="'+this.text+'">'+this.text+'</span>'+
                                '<a href="#"  class="xim-tagsinput-tag-remove icon"> &times; </strong></a>'+
                        '</li>';
                  }		


		},

		getDataUrl: function() {
			var url = "&tags["+this.numimage+"][text]="+encodeURIComponent(this.text)+"&tags["+this.numimage+"][type]="+encodeURIComponent(this.typeTag)+"&tags["+this.numimage+"][url]="+encodeURIComponent(this.url)+"&tags["+this.numimage+"][description]="+encodeURIComponent(this.description)+"&tags["+this.numimage+"][namespace]="+encodeURIComponent(this.namespace);
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
 				this.namespace = options.namespace || 'custom';
				this.typeFixed = options.typeFixed;
 				this.createTag();

 			}catch(e) {
 				alert("error al intentar leer la etiqueta");
 			}
 		},

 		_parseTag: function() {

 			var fields       = $('input', this.element);
 			this.text 	     = $(fields[0]).val();
		 	this.typeTag     = $(fields[1]).val() || 'generics';
		 	this.url         = $(fields[2]).val() || '#';
	 		this.description = $(fields[3]).val() || '';
	 		this.namespace = $("select", this.element).val();

	 		//Add remove tag event
        	$('a.xim-tagsinput-tag-remove', this.element).click(function(event) {
        		event.preventDefault();
//$(this.element).trigger('removingtag', [{tag: this.element, text:this.text}] );
				this.container.tagsinput('onRemovingTag', this.text);
				var $ul = $(this.element).parent();
                var numChildren = $ul.children("li").length;
				this.container.trigger("removingtag", [{tag: this.element, text:this.text}] );
				this.remove();

				if (numChildren == 1){
                    $ul.append($("<p/>").text("There aren't any tags defined yet."));                                     
            	}



			}.bind(this) );

		   $(this.element).trigger("creatingtag", [{tag: this.element, text:this.text}] );

 		},

 		createTag: function() {

			var $li = $(this._html());
                        this.container.find('.xim-tagsinput-list').append($li);
                        this.container.find('.xim-tagsinput-list').children("p").remove();
                        $li.slideDown(200, function(){
                                $(this).css( {"overflow": "visible"});
                        });

			this.element =  this.container.find('.xim-tagsinput-tag:last', this.element);

			/*
	 		no funciona en editor:
	 		descomentar esta linea(y eliminar las anteriores) cuando se actualice jquery en editor

 			 $(this._html()).insertBefore(this.container.tagsinput('getInputNewTag'));
  			 this.element = this.container.tagsinput('getLastTag');
 		*/


			 //Add remove tag event
			this.element.find('.xim-tagsinput-tag-remove').click(function(event) {
				event.preventDefault();
				// $(this.element).trigger('removingtag', [{tag: this.element, text:this.text}] );
				this.container.tagsinput('onRemovingTag', this.text);
				var $ul = this.element.parent();
                var numChildren = $ul.children("li").length;
           		this.remove();
				this.container.trigger("removingtag", [{tag: this.element, text:this.text}] );
				if (numChildren == 1){
                    $ul.append($("<p/>").text("There aren't any tags defined yet."));                                     
                }
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
