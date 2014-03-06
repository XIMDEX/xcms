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

			var url = X.baseUrl+"/?mod=ximTAGS&action=setmetadata&method=loadAllNamespaces";
			var result = false;
			var that = this;
			$.ajax({
				url:url,
				dataType: "json",
				success: function(data){
					X.ontologyType = data;
					X.ontologyTypeLikeOptions="";
					for (var i in X.ontologyType){
						window.X.ontologyTypeLikeOptions += '<option value="'+i+'" data-isSemantic="'+
						window.X.ontologyType[i].isSemantic+'">'+X.ontologyType[i].type+'</option>';
					}
					that._initAfterOntologyLoad();
				},
				error: function(data){

				}
			});			
			
		},

		_initAfterOntologyLoad: function(){
			var $newTagSelect = $(".xim-tagsinput-newtag select");
			$newTagSelect.html(X.ontologyTypeLikeOptions);
			$newTagSelect.inputSelect();

			$selects = $('.xim-tagsinput-tag select', this.element);
			$selects.html(X.ontologyTypeLikeOptions);
			$selects.inputSelect();
			this.id = $(this.element).attr('id');
			//this.newTag = new X.writingtag({container: this.element});
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
			var that = this;
			
			$("input.xim-tagsinput-input", $(this.element)).on("keyup", function(){
				
				var text = $(this).val();
				var url = X.baseUrl+"/?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromText&vocabulary=external&text="+text;
				var result = false;
				$that = $(this);
				$spanResults = $that.next("span.results");
				if ($spanResults.length === 0){
					$spanResults = $("<span/>").addClass("results");
					$that.after($spanResults);
				}
				var $thatTagSelect = $newTagSelect;
				if (text.length>2){
					$.ajax({
						url:url,
						dataType: "json",
						success: function(data){
							$that.next("span.results").empty();
							data = $.parseJSON(data);
							var results = data.external;						
							for (var i in results){
								if (results.hasOwnProperty(i)){
									var $div = $("<div/>").text(i);
									$div.on("click", function(){
										$that.val(i);
										$that.next("span.results").empty();
										$thatTagSelect.inputSelect("select", results[i]["type"].toLowerCase());
									});
									$spanResults.append($div);	
								}
								
							}
							
						},
						error: function(data){

						}
					});	
				}
				
				return false;
			});
			
			$("button", $(this.element)).on("click", function(){
					var tagText = $(".xim-tagsinput-input",$(that.element)).val();
					$(".xim-tagsinput-input",$(that.element)).val("");						
					var typeTag = $newTagSelect.data().ximdexInputSelect.selectedValue;
					typeTag = typeTag ? typeTag:"custom";
					that.createTag({text: tagText, typeTag: typeTag, url: '#', description:''});
					return false;
			});
		},

		getTags: function(){
			return this.tags;
		},

		getLastTag: function() {
 			return  $('.xim-tagsinput-tag:last', this.element);
 		},

 		getLastTagList: function() {
 			return  $('.xim-tagsinput-list-related li:last', this.element);
 		},

		getInputNewTag: function() {
			return this.newTag.element;
		},

		isUniqueText: function(text, numimage){
			for (var i in this.tags){
				if(this.tags[i].getText){
					if (this.tags[i].getText().trim() == text.trim() &&
						this.tags[i].numimage != numimage)
						return false;
				}
			}
			return true;
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
				 this.tags[indexTag] = new X.tag({container:this.element, text: tag.text,  typeTag: tag.typeTag, url: tag.url, description: tag.description, conf: tag.conf, typeFixed:tag.typeFixed});
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

			for (var i = 0; i < alltags.length;i++){
				this.addTagList({text:alltags[i].text, url:"", typeTag:alltags[i].type, conf:alltags[i].conf}, listname );
			}
			/*$.each(alltags, function(key, value) {
				if("status" != key) {
					var type = key;
					$.each(value, function(key, value) {
						this.addTagList({text:key, url:value[0], typeTag:type}, listname );

					}.bind(this));
				}

			}.bind(this));*/


			$('.xim-tagsinput-container-'+listname, this.element).show();
			$('.xim-tagsinput-list-related').removeClass('loading');
		},


		addTagList: function(tag, listname) {
			var listname = listname || 'related';

			if( '' != tag.text && -1 == this.indexTag(tag.text) ) {
				var list = $('.xim-tagsinput-list-'+listname, this.element);
				var indexTag = this.tagsList.length;

				 new X.taglist({container:this.element, list: list, text: tag.text,  typeTag: tag.typeTag, url: tag.url, description: '', conf: tag.conf});

			}
		},

//		getter: ['prueba']

	});

})(jQuery);
