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
 *  @version $Revision: 8093 $
 */




var AnnotationsRdfaToolBox = Object.xo_create(FloatingToolBox, {
	
	initialize: function(tool, editor) {
		AnnotationsRdfaToolBox._super(this, 'initialize', tool, editor);
		this.setTitle(_('Rdfa annotations'));

		var children = $('#kupu-toolbox-annotationrdfa').children().clone(true);
		$(this.element).append(children);
		$('#kupu-toolbox-annotationrdfa').unbind().remove();
	
		$('.anottationrdfatoolbox-section-header', this.element).click(function(event) {
			$('.anottationrdfatoolbox-section', this.element).hide();
			$(event.target).next('.anottationrdfatoolbox-section').slideToggle('fast');
		}.bind(this));
        
        $('.anottationrdfatoolbox-section', this.element).hide();
        this.loadAnnotations();
        this.editor.logMessage(_('AnnotationRdfaToolBox tool initialized'));
        
	},
	
	loadAnnotations: function() {
		 $.getJSON(X.baseUrl + "/extensions/rdfapi-php/geosparql.php", function(json){
			 this.printClass("http://xmlns.com/foaf/0.1/", 
					 json["http://xmlns.com/foaf/0.1/"], 
					 $('.anottationrdfatoolbox-section-foaf'));
			 this.printClass("http://www.geonames.org/ontology", 
					 json["http://www.geonames.org/ontology"], 
					 $('.anottationrdfatoolbox-section-geo'));
		 }.bind(this));
	},
	printClass: function(key, classInfo, container) {
		var ul = $('<ul></ul>');
		container.append(ul);
		
		for (k in classInfo) {
			var value = classInfo[k];
			var link = $('<a></a>')
				.attr('href', '#')
				.attr('title', value['info']['comment'])
				.html(value['info']['label'])
				.click(function() { return false;});
			
			var li = $('<li></li>');
			li.append(link);
			ul.append(li);
			
			if(Object.isObject(value['properties'])) {
				this.printProperty(value['properties'], li);
			}
		}
	},
	
	printProperty: function (info, container) {
		var ul = $('<ul></ul>');
		
		for (k in info) {
			var value = info[k];
			var link = $('<a></a>')
			.attr('href', '#')
			.attr('title', value['info']['comment'])
			.html(value['info']['label'])
			.click(function() { return false;});
//			var box = $('<input type="text"></input>');
			
		    $(link).click(function(event) {this.addRdfaInContainer(event);}.bind(this));
//		    $(link).attr('id', 'anottationtoolbox-linkitem' + k + '_' + l);
//		    $(link).attr('anchorname', info[k].anchor);
		
			var li = $('<li></li>');
			li.append(link);
			ul.append(li);
		};
		container.append(ul);
	},
	addRdfaInContainer: function(clickEvent) {
		console.log(clickEvent);
    	var selection = this.editor.getSelection().parentElement();
    	console.log(selection);
	}
	

});

