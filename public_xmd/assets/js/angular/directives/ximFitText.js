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

angular.module('ximdex.common.directive')
    .directive('ximFitText', ['$window', '$timeout', function ($window, $timeout) {
        return {
            link: function (scope, element, attrs) {
                var text="";
                
                var debounce = {
                    timeout: null,
                    tryExec: function(dFunction){
                        if (this.timeout) $timeout.cancel(this.timeout);
                        this.timeout = $timeout(function(){
                            dFunction();    
                        }, 100);  
                    }
                }
                var getTextWidth = function(text, font) {
                    if (!$window.jQuery._cacheCanvas) {
                        var canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
                        var docFragment = document.createDocumentFragment();
                        docFragment.appendChild(canvas);
                        jQuery._cacheCanvas = canvas;
                    }
                    var context = $window.jQuery._cacheCanvas.getContext("2d");
                    context.font = font;
                    return context.measureText(text).width;
                }

                var trimText = function (string, size) {
                    var sLength = string.length;
                    switch (attrs.ximFitText) {
                        case 'trim left': 
                            var trim = sLength - size;
                            return '...'+string.substring(trim + 4, sLength); 
                        case 'trim path':
                            var slashIndex = string.lastIndexOf('/');
                            var trimedString = string.substring(0, slashIndex-sLength+size-3)+'...'+string.substring(slashIndex, sLength);
                            if (trimedString.length <= size) {
                                return trimedString;
                            }
                        case 'trim center':
                            var mid = Math.floor(sLength/2);
                            var trim = Math.ceil( (sLength-size) / 2);
                            return string.substring(0, mid-trim-3)+'...'+string.substring(mid+trim+2, sLength);
                        default:
                            return string.substring(0, size - 4)+'...'; 
                    }
                }

                var fitText = function(event) {
                    var font = element.css('font');
                    var elementWidth = Math.floor(element.width());
                    //TODO: Use binary search recursive function to optimize
                    var textWidth = getTextWidth(text, font);
                    if (textWidth > elementWidth) {
                        var candidateSize = Math.floor(text.length*elementWidth/textWidth),
                        fit, 
                        lastFit, 
                        candidate, 
                        lastCandidate;
                        while (!fit) {
                            candidate = trimText(text, candidateSize);
                            var textWidth = getTextWidth(candidate, font);
                            if (textWidth > elementWidth) {
                                candidateSize = Math.floor(candidateSize*elementWidth/textWidth);
                                if (lastFit === true || lastCandidate == candidate) {
                                    fit = true;
                                    element.html(lastCandidate);    
                                }
                                lastFit = false;
                                lastCandidate = candidate;
                            } else if (textWidth < elementWidth){
                                candidateSize = Math.floor(candidateSize*elementWidth/textWidth);
                                lastCandidate = candidate;
                                if (lastFit === false || lastCandidate == candidate) {
                                    fit = true;
                                    element.html(candidate);  
                                }
                                lastFit = true;
                                lastCandidate = candidate;
                            } else if(textWidth = elementWidth){
                                fit = true;
                                element.html(candidate);
                            }
                        }
                    } else if (event){
                        element.html(text);   
                    }  
                }

                $timeout(function(){
                    text = element.text();
                    fitText();
                },0);
                var uiRefresh = attrs.ximRefresh.split(', ');
                for (var i = uiRefresh.length - 1; i >= 0; i--) {
                    scope.$on(uiRefresh[i], function(){
                        $timeout(function(){
                            fitText(true);
                        }, 0);
                    });
                };
            }
        }
    }]);