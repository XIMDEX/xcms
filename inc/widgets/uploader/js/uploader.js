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


 (function ($) {

 	$.widget('ui.uploader', {
 	    id: null,
 	    container: null,
 	    files: [],
 	    filter: null,
 	    input: null,
 	    uploading: false,
 	    numfiles: 0,
		 nodeid: 0,
		 urlcheckname: null,
 		 urlgetpreview: null,
		 extraParams:{},
		 paramNames:[],

 		_init: function(options) {

			if (options && options.extraParams){
				this.extraParams = $.extend({},this.extraParams,options.extraParams);
			}

 			this.container = $(this.element).closest("div");
  			this.id = $(this.container).attr('id');
			this.nodeid = X.widgetsVars.getValue(this.id, "nodeid");
			this.filter =  X.widgetsVars.getValue(this.id, "filter");
			X.widgetsVars.setValue(this.id, "uploader", this);
			this._seturlcheckname();
			this._seturlgetpreview();
			var that = this;
			$("#"+this.id).siblings("ol").find(".extra-param").each(function(element, index){
 				that.paramNames.push($(this).attr("name"));
			})

 			$(this.element).change(function(event) {
	 			var files = event.target.files
	 			this.addFiles(files);
	 	  	}.bind(this));

	 	 	$( this.container).bind('dragover', function(event) {
	 	 		$('.xim-loader-list-container', this.container).addClass('drop');
	 	 		event.originalEvent.stopPropagation();
			     	event.originalEvent.preventDefault();
	 	 	}.bind(this));

	 	 	$( this.container).bind('dragenter', function(event) {
	 			$('.xim-loader-list-container', this.container).removeClass('drop');
 	 	 		event.originalEvent.stopPropagation();
			     	event.originalEvent.preventDefault();
	 	 	}.bind(this));

	 		$( this.container).bind('dragleave', function(event) {
	 	 		$('.xim-loader-list-container', this.container).removeClass('drop');
 	 	 		event.originalEvent.stopPropagation();
			     	event.originalEvent.preventDefault();
	 	 	}.bind(this));

	 	 	$('.xim-loader-list-container', this.container).bind('drop', function(event) {
  	 	 		event.originalEvent.stopPropagation();
			     	event.originalEvent.preventDefault();

 	 			$('.xim-loader-list-container', this.container).removeClass('drop');

				var files = event.originalEvent.dataTransfer.files;
				this.addFiles(files);
	 	 		//alert("drop");
	 	 	}.bind(this) );

			//console.log("Browser:", $.browser, "Mozilla:", $.browser.mozilla, "Versión:", $.browser.version );
			if( $.browser.mozilla && ( $.browser.version.indexOf('1.9.2') != -1 ||   $.browser.version.indexOf('1.9.1') != -1 )  ) {
				this.input = $(this.element);
				$('.xim-uploader-link', this.container).show();
			}else {
				this.input = $('.xim-uploader-selected', this.container);
				//$('.xim-uploader-link', this.container).hide();
				$('.xim-uploader-link', this.container).css({'visibility': 'hidden', 'position': 'absolute', 'top': 0, 'right': 0});
				$(this.element).css({"display": "block","visibility": "hidden"});
			}

			this.input.show();

 			//console.log("link", $('.xim-uploader-selected', this.container) );
 			$('.xim-uploader-selected', this.container).click(function(event) {
 				event.preventDefault();
 				$(this.element).click();
 			}.bind(this));

			X.widgetsVars.triggerLoaded(this.id, this);
 		},

 		addFiles: function(files) {
 			if(this.uploading) {
				alert(_("Process of uploading files currently running. Please wait."));
 				return false;
 			}

			var num_files = files.length;
 			for(var i=0, f; f=  files[i]; i++) {
				if(-1 == this.indexFile(files[i]) && this.checkType(files[i].type) && files[i].size > 0 ) {
					var index = this.files.length
 					this.files[index] = new X.ximfile({file: files[i], _container:this.container, _element: null,urlcheckname: this.urlcheckname, urlgetpreview: this.urlgetpreview});
					this.numfiles++;

					$(this.files[index].element).bind('fileUploaded', function(event, options) {
	 					this.numfiles--;
	 					if(0 == this.numfiles) {
	 						$(this.element).trigger('filesUploaded', [{uploader:this, files:this.files}] );
	 					}
				 	}.bind(this));
	 			}else {
					alert("'"+files[i].name+"' "+_("selected already or invalid for the destination") );
	 			}
	 		}

		 	this._showDelete();
			$(this.element).val('');
 		},

 		_showDelete: function() {
	 		if(this.files.length > 0 ) {
	 			//display remove file
	 			$('.xim-uploader-delete', this.container).show();

	 			$('.xim-uploader-delete', this.container).click(function(event) {
	 				event.preventDefault();
	 				var files = $('input:checked', this.container)
	 				var max = files.length;
	 				for(var i= 0; i<max;i++) {
	 					var file  = $(files[i]).closest('li').data('ximfile');
						var index = this.indexFile({name: file.getName(), size: file.getSize()} );
						file.remove();
						this.files.splice(index, 1);
						if(0 == this.files.length ) {
							$(event.target).hide();
						}
						//file.remove();
	 				}
	 			}.bind(this) );
	 		}
 		},

		_seturlcheckname: function() {
			var url = X.restUrl;
			url += "?nodes[0]="+this.nodeid+"&nodeid="+this.nodeid;
			url += "&action=fileupload_common_multiple";
			url += "&noCacheVar="+X.getUID();
			url += "&method=checkname";
			this.urlcheckname = url;
		},

		_seturlgetpreview: function() {
			var url = X.restUrl;
			url += "?nodes[0]="+this.nodeid+"&nodeid="+this.nodeid;
			url += "&action=fileupload_common_multiple";
			url += "&noCacheVar="+X.getUID();
			url += "&method=getpreview";
			this.urlgetpreview = url;
		},

 		checkType: function(ftype) {
 			if( null == this.filter ) return true;

			var filters = this.filter.split(",");
			var max = filters.length;

			if(filters != null) {
				if("all" == filters[0]) return true;

				for(var i=0; i<max; i++) {
					var typeValid=new RegExp(filters[i], "gi");

					if(ftype.match(typeValid) ) {
						return true;
					}
				}
			}
 			return false;
 		},

 		//Checking if file already exists in the uploader
 		indexFile: function(file) {
		   var name = file.name.toLowerCase();
 			var max = this.files.length;
 			for(var i = 0; i<max; i++) {
 				var fname = this.files[i].getName();
 				if(fname == name ) {
 					return i;
 				}
 			}

 			return -1;
 		},

		setExtraParam: function(name, value){
			this.extraParams[name]=value;
		},

 		upload: function(url) {
 			if (null == url) {
				alert(_("Upload url not found."));
 				return false;
 			}

 			if(!this.files || !this.files.length) {
				alert(_("There aren't any selected files for upload."));
 				return false;
 			}

			if($("#"+this.id).attr("data-is-structured")){
			     for (var i = 0; i< this.paramNames.length; i++){
					var paramName = this.paramNames[i];
					if (!this.extraParams[paramName]){
						alert(_("You should select a schema and a language before uploading."));
		 				return false;					
					}
				}

			}

			this.uploading = true;

			//hidding buttons
	 		$('.xim-uploader-delete', this.container).hide();
 			$(this.input).hide();

 			var total = this.files.length;
 			for ( var i = 0; i<total; i++ ) {
 				this.files[i].upload(url, this.extraParams);
			}
			return true;
 		}
 	});
 })(jQuery);
