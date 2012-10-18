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


(function($) {
	$.widget('ui.tagsinput', {
		id: null,
		tags: [],
		tagsList: [],
		newTag: null,
		text: '',
		
	
		_init: function() {
			  this.id = $(this.element).attr('id');
	  		  this.newTag = new X.writingtag({container: this.element}); 
	  		  $(this.element).click(function() { $(this.newTag).focus(); }.bind(this));
	  		  
	  		  //load default values
	  		  var _tags = $('.xim-tagsinput-tag', this.element);

	  		  if(_tags.length > 0 ) {
	  		  		for(var i=0; i<_tags.length;i++) {
		  		  		 var indexTag = this.tags.length;
	  		  			 this.tags[indexTag] = new X.tag({container:this.element, element: _tags[i]});
	  		  		}
	  		  }

			$('#kupu-toolbox-tags-header').click(function() {
				$('.xim-tagsinput-container').toggle();
				$(this).toggleClass('kupu-toolbox-heading-closed');
				$(this).toggleClass('kupu-toolbox-heading-opened');
				return false;
			});
			
		},
	
		getLastTag: function() {
 			return  $('.xim-tagsinput-tag:last', this.element);
 		},
 		
 		getLastTagList: function() {
 			return  $('.xim-tagsinput-taglist:last', this.element);
 		},
	
		getInputNewTag: function() {
			return this.newTag.element;
		},
		
		getDataUrl: function() {

			var total = this.tags.length;
			var url = '';
			for(var i=0; i<total;i++) {
			 	url += this.tags[i].getDataUrl();
			
			}
			
			if(total >= 1 ) {
				return url;
			}else {
				return '';
			}
		},
		
		/** ********************************* Tag  ******************************************** */

		save: function(baseURL, content, /* callback,*/ autoSave) {

			var url = baseURL.replace("actionid=7230","mod=ximTAGS&action=setmetadata")+"&method=save_metadata";

			new AjaxRequest(url, {
				method: 'POST',
				content: this.getDataUrl(),
				onComplete: function(req, json) {
					/** if (callback.onComplete) callback.onComplete(req, json); */
				}.bind(this),
				onError: function(req) {
					/* if (callback.onError) callback.onError(req); */
				}.bind(this)
			});
		
		},
		
		createTag: function(tag) {
			
			if( '' != tag.text && -1 == this.indexTag(tag.text)  ) {
				var indexTag = this.tags.length;
	
				 this.tags[indexTag] = new X.tag({container:this.element, text: tag.text,  typeTag: tag.typeTag, url: tag.url, description: tag.description});
				// $(this.tags[indexTag].element).bind("removingtag", this.onRemovingTag.bind(this) ); 
        }

		},
		
		onRemovingTag: function(text) {

			var indexTag = this.indexTag(text)

			if(-1 != indexTag) {
				this.tags.splice(indexTag, 1);
			}
			return false;

		},
		
		indexTag: function(_tag) {
			var total = this.tags.length;
			
			for(var i=0; i<total; i++) {
					if(_tag == this.tags[i].getText() ) {
						return i;
					}
			}
			
			return -1;
		
		},

		
		/** *********************** Tag Suggested List && Tag Related List ************************** */
		addTagslist:function(alltags, listname) {
			var listname = listname || 'related';
					
			$.each(alltags, function(key, value) {
				if("status" != key) {
					var type = key;
					$.each(value, function(key, value) {
						this.addTagList({text:key, url:value[0], typeTag:type}, listname );
				
					}.bind(this));		
				}
	
			}.bind(this));
		
		
			$('.xim-tagsinput-container-'+listname, this.element).show();

		},
		
		
		addTagList: function(tag, listname) {
			var listname = listname || 'related';
			
			if( '' != tag.text && -1 == this.indexTag(tag.text) ) {
				var list = $('.xim-tagsinput-list-'+listname, this.element);
				var indexTag = this.tagsList.length;
				
				 new X.taglist({container:this.element, list: list, text: tag.text,  typeTag: tag.typeTag, url: tag.url, description: ''});

			}	
		},
		
		getter: ['prueba']
		
	});

})(jQuery);
