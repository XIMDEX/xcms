 if (angular.module('ximdex').notRegistred('XTagsCtrl')){
    angular.module('ximdex')
        .controllerProvider.register('XTagsCtrl', ['$scope', '$attrs', 'xBackend', 'xTranslate', '$window', '$http', 'xUrlHelper', function($scope, $attrs, xBackend, xTranslate, $window, $http, xUrlHelper){
        	$scope.documentTags = [];
        	$scope.cloudTags = [];
            $scope.namespaces = [];
        	$scope.nodeId = $attrs.ximNodeId;
        	if ($attrs.ximDocumentTags)
                $scope.documentTags = angular.fromJson($attrs.ximDocumentTags);
            if ($attrs.ximCloudTags)
                $scope.cloudTags = angular.fromJson($attrs.ximCloudTags);
            if ($attrs.ximNamespaces)
                $scope.namespaces = angular.fromJson($attrs.ximNamespaces);

            $scope.addTag = function(tag){
            	if (tag.isSemantic) {
                    for (i = 0, len = $scope.namespaces.length; i < len; i++){
                        if ($scope.namespaces[i].nemo === tag.type) {
                            tag.type_id = $scope.namespaces[i].id;
                        }
                    }
                }
                tag.selected = true;
            	$scope.documentTags.push(tag);
            }
            $scope.removeTag = function(index) {
            	$scope.documentTags[index].selected = false;
            	$scope.documentTags.splice(index, 1);
            }
            $scope.addNewTag = function() {
            	$scope.addTag(angular.copy($scope.newTag));
            	$scope.newTag = null;
            }

            $scope.addOntology = function(ontology){
            	$scope.addTag({
            		Name: ontology.name, 
            		ontology: true
            	});
            }
            $scope.removeOntology = function(ontology) {
            	for (var i=0; i < $scope.documentTags.lentgh; i++) {
            		if ($scope.documentTags[i].Name === ontology.name) {
            			$scope.removeTag(i);	
            		}	break;
            	}
            }
   
        }]);
    angular.module('ximdex').registerItem('XTagsCtrl');
    
    angular.module('ximdex')
        .compileProvider.directive('xtagsSuggested', ['$window', 'xTranslate', function ($window, xTranslate) {
            return {
                replace: true,
                scope: {
                    nodeId: '=ximNodeId',
                    selectCallback: '&ximOnSelect'
                },
                restrict: 'E',
                templateUrl : 'modules/ximTAGS/actions/setmetadata/template/Angular/xtagsSuggested.html',
                controller: ['$scope', '$element', '$attrs', '$http', 'xUrlHelper', function($scope, $element, $attrs, $http, xUrlHelper){   
                	
                	var url = xUrlHelper.baseUrl()+'?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent';
                	
                	//Fetch suggested tags from backend
                    $http.get(url+'&nodeid='+$scope.nodeId).success(function(data){
                		if (data && data.status === 'ok') {
                			$scope.tags = []
                			for (var set in data.semantic) {
            			        for (tag in data.semantic[set]) {
            			        	data.semantic[set][tag].Name = tag;
            			        	$scope.tags.push(data.semantic[set][tag]);
            			        }
            			    }
                            //TODO: Check for data.content responses
                		} else {
                			$scope.disconnected = true;
                		}
                	});
                	$scope.selectTag = function(tag){
                		$scope.selectCallback({tag:tag});
                	}
                }]
            }
        }]);
    angular.module('ximdex').registerItem('xtagsSuggested');
    
    angular.module('ximdex')
        .compileProvider.directive('ximOntologyBrowser', ['$window', function ($window) {
            return {
                replace: true,
                scope: {
                    selectedItems: '=ximSelectedList',
                    onSelect: '&ximOnSelect',
                    onUnSelect: '&ximOnUnSelect'
                },
                restrict: 'A',
                link: function (scope, element, attrs) {
            	    $window.jQuery(element).ontologywidget({
            			onSelect: function(el) {
            				scope.$apply(function(){
            					scope.onSelect({ontology:el});
            				});

            			},
            			offSelect: function(name) {
            				scope.$apply(function(){
            					scope.onUnSelect({ontology:{name:name}});
            				});
            			}
            		});    
	            }
            }
        }]);
    angular.module('ximdex').registerItem('ximOntologyBrowser');
}
//Start angular compile and binding
X.actionLoaded(function(event, fn, params) {
    X.angularTools.initView(params.context, params.tabId);
});


// X.actionLoaded(function(event, fn, params) {
// 	// try {

// 	// 	var $tagslist = fn('.js-tagsinput');
// 	// 	$tagslist.tagsinput();
// 	// 	if (fn(".xim-tagsinput-list-related").length){
// 	// 		var url = X.baseUrl+"/?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent";
// 	// 		var limitResult = 50;
// 	// 		$.ajax({
// 	// 			url:url,
// 	// 			data:{
// 	// 					nodeid: params.nodes[0]
// 	//    				 },
// 	// 			dataType:"json",
// 	// 			success:function(data){
// 	//                 if(!data){
// 	//                     var msgNotFound=_("Related tags not found.<br/> Is the ontology service up?");
// 	//                     var link= fn("<a/>").html("See how to enable it.").attr("href","https://github.com/XIMDEX/ximdex/wiki/Faqs#wiki-how-can-i-activate-the-ontology-service-on-my-ximdex-cms-local-instance").attr("target","_blank");
// 	//                     fn(".xim-tagsinput-list-related").removeClass("loading");
// 	//                     fn(".xim-tagsinput-list-related").html('<p class="">'+msgNotFound+'</p>').append(link);
// 	//                 }
// 	//                 else{
// 	//     				var parsedData  = $.parseJSON(data);
// 	// 	    			if (parsedData){
// 	// 		    			var isSemantic = 0;
// 	// 			    		tags = [];
// 	// 				    	for (var i in parsedData){
						
// 	// 						switch(i){
// 	// 							case "content":
// 	// 								isSemantic = 0;
// 	// 								break;
// 	// 							case "semantic":
// 	// 								isSemantic = 1;
// 	// 								break;
// 	// 						}
// 	// 						if (i != "status"){
// 	// 							var typeElement = parsedData[i];
// 	// 							for (var j in typeElement){
// 	// 								var Xtags = typeElement[j];
// 	// 	                        	for (var tag in Xtags){
// 	//                                 	var count = 0;
//  //                                		var alreadyExist = false;
//  //                                		fn('.xim-tagsinput-list input#text').each(function(){
//  //                                			if ($(this).val() === tag) {
//  //                                				alreadyExist = true;		
//  //                                			}
//  //                                		});
//  //                                		if (!alreadyExist){
//  //                                			tags.push({isSemantic:Xtags[tag].isSemantic,text:tag,type:Xtags[tag].type,conf:Xtags[tag].confidence});
//  //                                		}	
// 	//                                 	count++;
// 	//                                 	if (count >= limitResult)
// 	//                                        break;
// 	// 	                        	}
// 	// 							}
// 	// 						}
// 	// 					}
// 	// 					$tagslist.tagsinput('addTagslist', tags );
// 	// 				}
// 	//                 }
// 	// 			}
// 	// 		});
// 	// 	}
// 	// 	fn('.xim-tagsinput-list input#text').each(function(){
// 	// 		$tag = $(this);
// 	// 		fn('.tagcloud').find("li.xim-tagsinput-taglist").each(function(){
// 	// 			if ($(this).find(".tag-text").text() === $tag.val()) {
// 	// 				$(this).hide();
// 	// 			}
// 	// 		});
// 	// 	});

// 	// 	fn('.tagcloud').find("li.xim-tagsinput-taglist").click(function (event) {
// 	// 		var element = event.target;
// 	// 		var text = $(event.currentTarget).find('.tag-text').text();
// 	// 		$(this).slideUp(400);
// 	// 		$tagslist.tagsinput('createTag', {text: text, typeTag: 'generics', url: '#', description:''});
// 	// 	});

// 	// 	$tagslist.on('removingtag', function(event, tag){
// 	// 		fn('.tagcloud').find("li.xim-tagsinput-taglist").each(function(){
// 	// 			if($(this).find(".tag-text").text() === tag.text) {
// 	// 				$(this).slideDown(300);
// 	// 			} 
// 	// 		});
// 	// 		fn('.xim-tagsinput-list-related').find("li.xim-tagsinput-taglist").each(function(){
// 	// 			if($(this).find(".tag-text").text() === tag.text) {
// 	// 				$(this).slideDown(300);
// 	// 			} 
// 	// 		});
// 	// 	});
		
// 	// }catch(e) {
// 	// 	alert(_("Module ximTAGS needed"));
// 	// }



	
// });
