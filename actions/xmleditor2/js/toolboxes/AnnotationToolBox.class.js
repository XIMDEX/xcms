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



/**
*<p>Toolbox to manage tags about the current node</p>
*/
var AnnotationsToolBox = Object.xo_create(FloatingToolBox, {

	currentSelection:false,

	/**
	* <p>Init method</p>
	*/
	initialize: function(tool, editor) {

		AnnotationsToolBox._super(this, 'initialize', tool, editor);
		this.setTitle(_('External references'));

		var children = $('#kupu-toolbox-annotation').children().clone(true);
		$(this.element).append(children);
		$('#kupu-toolbox-annotationbox').unbind().remove();


		/**
		*<p>Click method on every tag category</p>
		*<p>This action will toggle the panel with the results for every category.</p>
		*/
		$('.anottationtoolbox-section-header', this.element).click(function(event) {

			this.currentSelection = "#"+event.currentTarget.id;
			$('.anottationtoolbox-section-header', this.element).removeClass('button-pressed');
			$('.anottationtoolbox-section', this.element).hide();
			$(event.currentTarget)
				.addClass('button-pressed')
				.next('.anottationtoolbox-section')
				.slideToggle('fast');

		}.bind(this));
		//End of click event
	
    $('.anottationtoolbox-section', this.element).hide();
    this.editor.logMessage(_('AnnotationToolBox tool initialized'));
	},


	/**
	*<p>Update the annotation info.</p>	
	*<p>This method is called from XimdocAnnotationTool.class.js</p>
	*/
	refreshInfo: function(annotationDoc) {
		//update class for selection
		$('.anottationtoolbox-section-header', this.element).removeClass('button-pressed');
		if(annotationDoc !== null) {

			if (this.currentSelection){
				$(this.currentSelection, this.element).addClass('button-pressed');
			}
			else{
				$('#anottationtoolbox-section-header-image', this.element).addClass('button-pressed');
			}


			//Load each category with its results
			this.populateImageSection(annotationDoc.content.images);
			this.populateLinkSection(annotationDoc.content.links);
			this.populateArticleSection(annotationDoc.content.articles);

			this.populatePeopleSection(annotationDoc.semantic.people);
			this.populatePlacesSection(annotationDoc.semantic.places);
			this.populateOrganisationsSection(annotationDoc.semantic.orgs);

			$('#anottationtoolbox-section-link, #anottationtoolbox-section-image, #anottationtoolbox-section-article, #anottationtoolbox-section-people, #anottationtoolbox-section-places, #anottationtoolbox-section-organisations').slideUp('fast');

			//restart styles for every category
			if(this.currentSelection){		
				$(this.currentSelection.replace("-header","")).slideDown('fast');
				$(this.currentSelection,this.element).addClass("button-pressed");
			}else if (annotationDoc.content.images.length > 0) {
				$('#anottationtoolbox-section-header-image').addClass("button-pressed");
				$('#anottationtoolbox-section-image').slideDown('fast');
			} else if (annotationDoc.content.links.length > 0) {
				$('#anottationtoolbox-section-link').slideDown('fast');
				$('#anottationtoolbox-section-header-link').addClass("button-pressed");
			} else if (annotationDoc.content.articles.length > 0){
				$('#anottationtoolbox-section-article').slideDown('fast');
				$('#anottationtoolbox-section-header-article').addClass("button-pressed");
			}
			else if(annotationDoc.semantic.people.length > 0){
				$('#anottationtoolbox-section-people').slideDown('fast');
				$('#anottationtoolbox-section-header.people').addClass("button-pressed");
			} else if (annotationDoc.semantic.places.length > 0){
				$('#anottationtoolbox-section-places').slideDown('fast');
				$('#anottationtoolbox-section-header-places').addClass("button-pressed");
			} else if (annotationDoc.semantic.orgs.length > 0){
				$('#anottationtoolbox-section-organisations').slideDown('fast');
				$('#anottationtoolbox-section-header-organisations').addClass("button-pressed");
			}


			//$('.xim-tagsinput-container').tagsinput('addTagslist', annotationDoc["semantic"] );
		}
	},


	/**
	* <p>Load link category in a specific way</p>
	* @param info. Object with a structure like:
	* "Link anchor": {confidence:[0-1], type:"zLink", isSemantic:0, others:{...}}
	*/
	populateLinkSection: function(info) {


		var div = $('#anottationtoolbox-link-template', this.element).clone(true);
		$(".removable", div).remove();
		$(div).appendTo('#anottationtoolbox-section-link');
		$(div).attr('id', 'anottationtoolbox-link-container');
		$('#anottationtoolbox-link-template', this.element).hide();
		
		var countLinks=0;
		var length = info.length;
		var k = 0;

		//For every link. i is the name for the link.
		for(var i in info) {
			var divHeader = $('#anottationtoolbox-linkheader-template', div).clone(true);
			divHeader.show();			
			divHeader.addClass("removable");
			$(divHeader).attr('id', '');
			$(divHeader).click(function(event) {this.toggleItem(event);}.bind(this));
			$(divHeader).html(i);
			$(divHeader).appendTo(div);

			var anchor = $('#anottationtoolbox-linkitem-template', div).clone(true);
			$(anchor).attr('id', 'anottationtoolbox-linkitem' + k);
			$(anchor).appendTo(div);
			$(anchor).empty();

			targetLength = info[i].others.target.length;
			for(var l = 0; l < targetLength; l ++) {
				var typeText = document.createTextNode(info[i].others.target[l].type + ' ');
				$(anchor).append(typeText);

				var linkgo = $('#anottationtoolbox-linkitem-template_template_visit', div).clone(true);
				$(linkgo).attr('href', info[i].others.target[l].url);
				$(linkgo).attr('id', 'anottationtoolbox-linkitem' + k + '_' + l + '_visit');
				$(anchor).append($(linkgo));

				var linkadd = $('#anottationtoolbox-linkitem-template_template', div).clone(true);
				$(linkadd).click(function(event) {this.addLinkToDocument(event);}.bind(this));
				$(linkadd).attr('id', 'anottationtoolbox-linkitem' + k + '_' + l);
				$(linkadd).attr('anchorname', info[i].others.anchor);
				$(anchor).append($(linkadd));

				$(anchor).append('<br/>');
			}

			$(divHeader, anchor).show();
			countLinks++;
			k++;
		}

		//Update the number of results		
		if(countLinks==0){
                        $('#anottationtoolbox-section-header-link').click(function (event) {
                                var NoRefs = document.createTextNode("References not found.");
                                $('#anottationtoolbox-section-link').empty();
                                $('#anottationtoolbox-section-link').append(NoRefs);
                        }.bind(this));
                }
		$('#anottationtoolbox-section-header-link span.hits').remove();
                $('#anottationtoolbox-section-header-link span').append('<span class="hits">'+countLinks+'</span>');


		//Hide the header elements
		$('#anottationtoolbox-linkheader-template', div).hide();
		$('#anottationtoolbox-linkitem-template', div).hide();
	},

/**
	* <p>Load image category in a specific way</p>
	* @param info. Object with a structure like:
	* "Image anchor": {confidence:[0-1], type:"zImage", isSemantic:0, others:{...}}
	*/
	populateImageSection: function(info) {
		var infoContainerImageDiv = $('#infoContainer-image', this.element);
		var sliderContainerImageDiv = $('#sliderContainer-image', this.element);
		$(".removable", infoContainerImageDiv).remove();
		$(".removable", sliderContainerImageDiv).remove();

		var length = info.length;
		var countImages = 0;
		var k = 0;
		//For each image, where i is the image name
		for (var i in info){		
			var infoImageDiv = $('#infoImage-template', this.element).clone(true);
			infoImageDiv.show();
			infoImageDiv.addClass("removable");
			$(infoImageDiv).attr('id', 'infoImageDiv' + k);
			var descriptionText = document.createTextNode(i);
			var licenseText = document.createTextNode(info[i].others.license);
			$('#description-image', infoImageDiv).empty().append(descriptionText);
			$('#license-image', infoImageDiv).empty().append(licenseText);
			$(infoContainerImageDiv).append($(infoImageDiv));

			var img = $('#anottationtoolbox-imageitem-template', this.element).clone(true);
			$(img).attr('src', info[i].others.url_m);
			$(img).attr('id', 'anottationtoolbox-imageitem' + k);
			if(countImages > 0) {
				$(img).hide();
				$(infoImageDiv).hide();
			}
			$(img).click(function (event) {this.addImageToDocument(event);}.bind(this));
			$(sliderContainerImageDiv).append($(img));
			countImages ++;
			k++;
		}

		//Update the number of results
		if(countImages==0){
                        $('#anottationtoolbox-section-header-image').click(function (event) {
                                var NoRefs = document.createTextNode("References not found.");
                                $('#anottationtoolbox-section-image').empty();
                                $('#anottationtoolbox-section-image').append(NoRefs);
                        }.bind(this));
                }
		$('#anottationtoolbox-section-header-image span.hits').remove();
                $('#anottationtoolbox-section-header-image span').append('<span class="hits">'+countImages+'</span>');


		//Set image navigation tools.
		var prevImageButton = $('#prevButton-image', this.element);
		var nextImageButton = $('#nextButton-image', this.element);
		$(prevImageButton).click(function (event) {this.slideSwitch('backward');}.bind(this));
		$(nextImageButton).click(function (event) {this.slideSwitch('forward');}.bind(this));


		//Hide the headers
		$('#infoImage-template', this.element).hide();
		$('#anottationtoolbox-imageitem-template', this.element).hide();
		$('#anottationtoolbox-section-image', this.element).show();
	},

	/**
	* <p>Load article category in a specific way</p>
	* @param info. Object with a structure like:
	* "Article anchor": {confidence:[0-1], type:"zLink", isSemantic:0, others:{...}}
	*/
	populateArticleSection: function(info) {
		var articleContainerDiv = $('#articleContainer-article');
		$(".removable", articleContainerDiv).remove();

		var targetLength = info.length;
		var countArticles=0;
		var k = 0;
		for(var i in info){
			var articleDiv = $('#anottationtoolbox-articleitem-template', this.element).clone(true);
			articleDiv.show();
			articleDiv.addClass("removable");
			$(articleDiv).attr('id', '');
			var articleDivText = document.createTextNode(i);

			var link = $('#anottationtoolbox-articleitem-template_template_visit', this.element).clone(true);
			$(link).attr('href', info[i].others.url);
			$(link).attr('id', 'anottationtoolbox-articleitem' + k + '_visit');

			var linkAdd = $('#anottationtoolbox-articleitem-template_template', this.element).clone(true);
			$(linkAdd).attr('id', 'anottationtoolbox-articleitem' + k);
			$(linkAdd).attr('anchorname', i);

			var container = $('.anottationtoolbox-actions', articleDiv).clone(true).empty();
			articleDiv.empty().append(articleDivText).append(container);
			container.append($(link));
			container.append($(linkAdd));

			$(linkAdd).click(function (event) {this.addArticleToDocument(event);}.bind(this));
			$(articleContainerDiv).append($(articleDiv));
			k++;
			countArticles++;
		}

		if(countArticles==0){
                        $('#anottationtoolbox-section-header-article').click(function (event) {
                                var NoRefs = document.createTextNode("References not found.");
                                $('#anottationtoolbox-section-article').empty();
                                $('#anottationtoolbox-section-article').append(NoRefs);
                        }.bind(this));
                }
		$('#anottationtoolbox-section-header-article span.hits').remove();
                $('#anottationtoolbox-section-header-article span').append('<span class="hits">'+countArticles+'</span>');

		$('#anottationtoolbox-articleitem-template', this.element).hide();
		$('#anottationtoolbox-section-article').show();
	},

	/**
	* <p>Load people category in a specific way</p>
	* @param info. Object with a structure like:
	* "People anchor": {confidence:confidence, type:"dPerson", isSemantic:1, others:{...}}
	*/
	populatePeopleSection: function(info) {
		var peopleContainerDiv = $('#peopleContainer');
		$(".removable", peopleContainerDiv).remove();
		
		
		var length = info.length;
    var countPeople = 0;
		var k = 0;
		for (var i in info){
			var articleDiv = $('#anottationtoolbox-personitem-template', this.element).clone(true);
			articleDiv.show();
			articleDiv.addClass("removable");
			$(articleDiv).attr('id', '');
			var articleDivText = document.createTextNode(i);

			var link = $('#anottationtoolbox-personitem-template_template_visit', this.element).clone(true);
			$(link).attr('href', i);
			$(link).attr('id', 'anottationtoolbox-personitem_' + k + '_visit');

			var linkAdd = $('#anottationtoolbox-personitem-template_template', this.element).clone(true);
			$(linkAdd).attr('id', 'anottationtoolbox-personitem_' + k);
			$(linkAdd).attr('anchorname', i);

			var container = $('.anottationtoolbox-actions', articleDiv).clone(true).empty();
			articleDiv.empty().append(articleDivText).append(container);
			if(info[i].length>0){
				container.append($(link));
			}
			container.append($(linkAdd));

			$(linkAdd).click(function (event) {this.addTag(event, 'people', 'dPerson');}.bind(this));
			$(peopleContainerDiv).append($(articleDiv));
			countPeople++;
			k++;
		}

		if(countPeople==0){
			$('#anottationtoolbox-section-header-people').click(function (event) {
				var NoRefs = document.createTextNode("References not found.");
				$('#anottationtoolbox-section-people').empty();
				$('#anottationtoolbox-section-people').append(NoRefs);
			}.bind(this));
		}
		$('#anottationtoolbox-section-header-people span.hits').remove();
		$('#anottationtoolbox-section-header-people span').append('<span class="hits">'+countPeople+'</span>');
	
		$('#anottationtoolbox-personitem-template', this.element).hide();
		$('#anottationtoolbox-section-people').show();
	},

	/**
	* <p>Load link category in a specific way</p>
	* @param info. Object with a structure like:
	* "Place anchor": {confidence:confidence, type:"dPlace", isSemantic:1, others:{...}}
	*/
	populatePlacesSection: function(info) {
		var placesContainerDiv = $('#placesContainer');
		$(".removable", placesContainerDiv).remove();

		var length = info.length;
    var countPlaces = 0;
		var k = 0;
    for(var i in info) {
			var articleDiv = $('#anottationtoolbox-placeitem-template', this.element).clone(true);
			articleDiv.show();
			articleDiv.addClass("removable");
			$(articleDiv).attr('id', '');
			var articleDivText = document.createTextNode(i);

			var link = $('#anottationtoolbox-placeitem-template_template_visit', this.element).clone(true);
			$(link).attr('href', i);
			$(link).attr('id', 'anottationtoolbox-placeitem' + k + '_visit');

			var linkAdd = $('#anottationtoolbox-placeitem-template_template', this.element).clone(true);
			$(linkAdd).attr('id', 'anottationtoolbox-placeitem' + k);
			$(linkAdd).attr('anchorname', i);

			var container = $('.anottationtoolbox-actions', articleDiv).clone(true).empty();
			articleDiv.empty().append(articleDivText).append(container);
			if(info[i].length>0){
				container.append($(link));
			}
			container.append($(linkAdd));

			$(linkAdd).click(function (event) {this.addTag(event,'places', 'dPlace');}.bind(this));
			$(placesContainerDiv).append($(articleDiv));
			countPlaces++;
			k++;
		}

		if(countPlaces==0){
			$('#anottationtoolbox-section-header-places').click(function (event) {
				var NoRefs = document.createTextNode("References not found.");
				$('#anottationtoolbox-section-places').empty();
				$('#anottationtoolbox-section-places').append(NoRefs);
			}.bind(this));
		}
		$('#anottationtoolbox-section-header-places span.hits').remove();
		$('#anottationtoolbox-section-header-places span').append('<span class="hits">'+countPlaces+'</span>');

		$('#anottationtoolbox-placeitem-template', this.element).hide();
		$('#anottationtoolbox-section-places').show();
	},

	/**
	* <p>Load organisation category in a specific way</p>
	* @param info. Object with a structure like:
	* "Organizacion anchor": {confidence:confidence, type:"dOrganization", isSemantic:1, others:{...}}
	*/	
	populateOrganisationsSection: function(info) {
		var organisationsContainerDiv = $('#organisationsContainer');
		$(".removable", organisationsContainerDiv).remove();
		
		var length = info.length;
    var countOrgs = 0;
		var k = 0;
		for (var i in info){
			var articleDiv = $('#anottationtoolbox-organisationitem-template', this.element).clone(true);
			articleDiv.show();
			articleDiv.addClass("removable");
			$(articleDiv).attr('id', '');
			var articleDivText = document.createTextNode(i);

			var link = $('#anottationtoolbox-organisationitem-template_template_visit', this.element).clone(true);
			$(link).attr('href', i);
			$(link).attr('id', 'anottationtoolbox-organisationitem' + k + '_visit');

			var linkAdd = $('#anottationtoolbox-organisationitem-template_template', this.element).clone(true);
			$(linkAdd).attr('id', 'anottationtoolbox-organisationitem' + k);
			$(linkAdd).attr('anchorname', i);

			var container = $('.anottationtoolbox-actions', articleDiv).clone(true).empty();
			articleDiv.empty().append(articleDivText).append(container);
			if(info[i].length>0){
				container.append($(link));
			}
			container.append($(linkAdd));

			$(linkAdd).click(function (event) {this.addTag(event,'organisations', 'dOrganisation');}.bind(this));
			$(organisationsContainerDiv).append($(articleDiv));
			countOrgs++;
			k++;
		}

		if(countOrgs==0){
			$('#anottationtoolbox-section-header-organisations').click(function (event) {
				var NoRefs = document.createTextNode("References not found.");
				$('#anottationtoolbox-section-organisations').empty();
				$('#anottationtoolbox-section-organisations').append(NoRefs);
			}.bind(this));
		}
		$('#anottationtoolbox-section-header-organisations span.hits').remove();
		$('#anottationtoolbox-section-header-organisations span').append('<span class="hits">'+countOrgs+'</span>');

		$('#anottationtoolbox-organisationitem-template', this.element).hide();
		$('#anottationtoolbox-section-organisations').show();
	},

	toggleItem: function(event) {
		var id = $(event.target.nextSibling, this.element).attr('id');
		$('#' + id, this.element).each(function(index,elem) {
			$(elem).slideToggle("fast");
		}.bind(this));
	},

	/**
	 * Function which adds a tag to document
	 *
	 * @param clickEvent
	 * @param type
	 *
	 */
	addTag: function(clickEvent, type, nemo) {
		var rngElementNames = this.editor.getRngDocument().getRngElementNameByType('annotation_tag');

		if(rngElementNames.length == 0) {
			this.editor.alert(_('No `annotation_tag` RngElement type found'));
			return;
		}

		var rngElementName = rngElementNames[0];
		var parent = $(clickEvent.target).closest('.anottationtoolbox-articleitem');
		var word = $('.anottationtoolbox-linkadd', parent).attr('anchorName');
		var link = $('.anottationtoolbox-linkgo', parent).attr('href');

		//temporal measure
		//$('.xim-tagsinput-container').tagsinput('createTag',{text: word, typeTag: type, url: link, description:''});
		$(document).trigger('addTag', {Name: word, type: nemo, isSemantic: true});
		$('.xim-tagsinput-container').show();

		// Locating all words in document
		var elementsToParse = this.editor.getRngDocument().getAllowedParents(rngElementName);

		elementsToParse.each(function(index, elementToParse) {
			this.editor.setActionDescription(_('Applied `annotation tag`'));
			$(elementToParse + ":contains('" + word + "')", this.editor._xmlDom).each(function(index,elem) {

				var rngElement = this.editor.getRngDocument().getElement(rngElementName);
				var parentElement = this.editor._ximdoc.getElement($(elem).attr('uid'));

				selNode = $('[uid="' + $(elem).attr('uid') + '"]', this.editor.getInnerDocument());

				$($(selNode).contents().get().reverse()).each(function(index, el) {
					if(el.nodeType == 3 && el.nodeValue.indexOf(word) >= 0) {
						var size = $(selNode).contents().length;
						var ptdIndex = Math.floor(((size-index-1) / 2));
						var brotherElement = parentElement.childNodes[ptdIndex];
						var selectedTextNode = el;
						var pieces = selectedTextNode.nodeValue.split(word);
						for(var it = pieces.length -1; it > 0; it--) {
							var prevText = pieces[it - 1];
							var postText = pieces[it];
							var pre = this.editor.getInnerDocument().createTextNode(prevText);
							var post = this.editor.getInnerDocument().createTextNode(postText);

							if(it == 1){
								$(selectedTextNode).before(pre);
							}
							$(selectedTextNode).after(post);

							//New elemente to append (tag)
							var newElement = new XimElement(rngElement, false);
							newElement.attributes['type'] = type;
							if(link!=undefined){
								newElement.attributes['url'] = link;
							}
							else{
								newElement.attributes['url'] = '';
							}
							newElement.value = word;

							if(brotherElement)
								this.editor.getXimDocument().insertBefore(newElement, parentElement, brotherElement);
							else
								this.editor.getXimDocument().appendChild(newElement, parentElement);

							this.editor.setActionDescription(_('Applied `annotation tag`'));
							ptdIndex ++;
							brotherElement = parentElement.childNodes[ptdIndex];
						}
						$(selectedTextNode).remove();
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));

		this.editor.updateEditor({caller: this});

	},

	addImageToDocument: function(clickEvent) {

		var rngElementNames = this.editor.getRngDocument().getRngElementNameByType('annotation_image');
		if(rngElementNames.length == 0) {
			alert(_('No annotation_image RngElement type found'));
			return;
		}

		var rngElementName = rngElementNames[0];
		var target = clickEvent.target;

		if(target) {

			if(this.editor.ximParent) {
				var ximElement = this.editor.tools['ximdoctool'].createElement(rngElementName, this.editor.ximParent, this.editor.ximElement);
			} else if (this.editor.ximElement.isRoot) {
				var ximElement = this.editor.tools['ximdoctool'].createElement(rngElementName, this.editor.ximElement, null);
			}

			if(ximElement) {
				ximElement.attributes['url'] = $(target).attr('src');
				ximElement.attributes['height'] = $(target).attr('height');
				ximElement.attributes['width'] = $(target).attr('width');
				this.editor.updateEditor({caller: this});
			}
		}
	},

	addLinkToDocument: function(clickEvent) {

		this.editor.setActionDescription(_('Applied annotation link'));

		var rngElementNames = this.editor.getRngDocument().getRngElementNameByType('annotation_link');

		if(rngElementNames.length == 0) {
			this.editor.alert(_('No annotation_link RngElement type found'));
			return;
		}


		var rngElementName = rngElementNames[0];
                //This way is valid just for one result
		var idLink = $(clickEvent.target).attr("id")+"_visit";
		var parent = $(clickEvent.target).closest('.anottationtoolbox-linkitem');
		var word = $('.anottationtoolbox-linkadd', parent).attr('anchorName');
		var link = $('#'+idLink,parent).attr('href');

		// Locating all words in document
		var elementsToParse = this.editor.getRngDocument().getAllowedParents(rngElementName);

		elementsToParse.each(function(index, elementToParse) {
			$(elementToParse + ":contains('" + word + "')", this.editor._xmlDom).each(function(index,elem) {

				var rngElement = this.editor.getRngDocument().getElement(rngElementName);
				/*var newElement = new XimElement(rngElement, true);
				newElement.attributes['a_enlaceid_url'] = link;
				newElement.value = word;*/
				var parentElement = this.editor._ximdoc.getElement($(elem).attr('uid'));

				selNode = $('[uid="' + $(elem).attr('uid') + '"]', this.editor.getInnerDocument());
				$($(selNode).contents().get().reverse()).each(function(index, el) {
					if(el.nodeType == 3 && el.nodeValue.indexOf(word) >= 0) {
						var size = $(selNode).contents().length;
						var ptdIndex = Math.floor(((size-index-1) / 2));
						var brotherElement = parentElement.childNodes[ptdIndex];
						var selectedTextNode = el;
						var pieces = selectedTextNode.nodeValue.split(word);
						for(var it = pieces.length -1; it > 0; it--) {
							var prevText = pieces[it-1];
							var postText = pieces[it];
							var pre = this.editor.getInnerDocument().createTextNode(prevText);
							var post = this.editor.getInnerDocument().createTextNode(postText);

							if(it == 1)
								$(selectedTextNode).before(pre);
							$(selectedTextNode).after(post);

							var newElement = new XimElement(rngElement, true);
							newElement.attributes['a_enlaceid_url'] = link;
							newElement.value = word;

							if(brotherElement)
								this.editor.getXimDocument().insertBefore(newElement, parentElement, brotherElement);
							else
								this.editor.getXimDocument().appendChild(newElement, parentElement);

							this.editor.setActionDescription(_('Applied annotation link'));
							ptdIndex ++;
							brotherElement = parentElement.childNodes[ptdIndex];
						}
						$(selectedTextNode).remove();
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));

		this.editor.updateEditor({caller: this});
	},

	addArticleToDocument: function (clickEvent) {

		var rngElementRelatedContainerNames = this.editor.getRngDocument().getRngElementNameByType('related_info_container');
		var rngElementContainerNames = this.editor.getRngDocument().getRngElementNameByType('annotation_related_info');
		var rngElementElementNames = this.editor.getRngDocument().getRngElementNameByType('annotation_related_info_element');

		if(rngElementContainerNames.length == 0) {
			this.editor.alert(_('No annotation_related_info RngElement type found'));
			return;
		}

		if(rngElementElementNames.length == 0) {
			this.editor.alert(_('No annotation_related_info_element RngElement type found'));
			return;
		}

		var rngElementRelatedContainerName = (rngElementRelatedContainerNames.length > 0) ? rngElementRelatedContainerNames[0] : null;
		var rngElementContainerName = rngElementContainerNames[0];
		var rngElementElementName = rngElementElementNames[0];

		var uid = $(rngElementContainerName + '[identificador="annotator"]', this.editor._xmlDom).attr('uid');
		if(!uid) {
			var parentUid = (rngElementRelatedContainerName) ? $(rngElementRelatedContainerName, this.editor._xmlDom).attr('uid') : null;
			var relatedInfoContainer = $(rngElementContainerName + '[identificador="annotator"]', this.editor._xmlDom);

			var rngElement = this.editor.getRngDocument().getElement(rngElementContainerName);
			var newElement = new XimElement(rngElement, false);
			var parentElement = (parentUid) ? this.editor.getXimDocument().getElement(parentUid) : this.editor.getXimDocument().getRootElement();
			newElement.attributes['identificador'] = 'annotator';

			var ximElement = this.editor.getXimDocument().appendChild(newElement, parentElement);
		}

		var parent = $(clickEvent.target).closest('.anottationtoolbox-articleitem');
		var word = $('.anottationtoolbox-linkadd', parent).attr('anchorName');
		var link = $('.anottationtoolbox-linkgo', parent).attr('href');

		if (parent) {
			var rngElement = this.editor.getRngDocument().getElement(rngElementElementName);
			var newElement = new XimElement(rngElement, false);
			var parentElement = ximElement || this.editor.getXimDocument().getElement(uid);
			newElement.attributes['identificador'] = 'annotator';

			var ximElement = this.editor.getXimDocument().appendChild(newElement, parentElement);

			rngElement = this.editor.getRngDocument().getRngElementNameByType('annotation_link');
			if (Object.isEmpty(rngElement)) {
				this.editor.logMessage(_("An article cannot be inserted in this document. Check for annotation_link element."));
				return;
			}

			rngElement = this.editor.getRngDocument().getElement(rngElement[0]);
			newElement = new XimElement(rngElement, false);
			parentElement = ximElement;
//			parentElement.value = '';
			newElement.attributes['a_enlaceid_url'] = link;
			newElement.value = word;

			var ximElement = this.editor.getXimDocument().appendChild(newElement, parentElement);

			var tagName = ximElement.tagname || _("element");
			this.editor.setActionDescription(_('Create ')+tagName);
			this.editor.updateEditor({caller: this});
		}
	},

	slideSwitch: function (to) {
	    var visibleImages = $('#sliderContainer-image IMG:visible');

	    if(visibleImages.length == 0) {
	    	visibleImages = (to == 'forward') ? $('#sliderContainer-image IMG:last') : $('#sliderContainer-image IMG:first');
		}

	    if(to == 'forward') {
		    var nextImage =  visibleImages.next().length ? visibleImages.next()
		        : $('#sliderContainer-image IMG:first');
	    } else {
		    var nextImage =  visibleImages.prev().length ? visibleImages.prev()
		        : $('#sliderContainer-image IMG:last');
	    }

	    var visibleId = $(visibleImages).attr('id');
	    var nextId = $(nextImage).attr('id');
		visibleId = visibleId.replace('anottationtoolbox-imageitem', 'infoImageDiv');
		nextId = nextId.replace('anottationtoolbox-imageitem', 'infoImageDiv');

	    visibleImages.toggle();
		$('#' + visibleId).toggle();
	    nextImage.toggle();
	    $('#' + nextId).toggle();
	}

/*

	// Unused?

	updateState: function(options) {
		return;
	},

	afterUpdateContent: function(options) {
		return;
	},

	removeSection: function() {
		return;
	},

	removeAllSections: function() {
		$('div.anottationtoolbox-section', this.element).each(function(index, elem) {
			$(elem).remove();
		}.bind(this));
		$('.anottationtoolbox-section-header', this.element).each(function(index, elem) {
			$(elem).remove();
		}.bind(this));
	}

*/
});

