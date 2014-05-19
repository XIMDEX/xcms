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
                var fitText = function(event) {
                    var font = element.css('font');
                    var elementWidth = element.width();
                    //TODO: Use binary search recursive function to optimize
                    var textWidth = getTextWidth(text, font);
                    if (textWidth > elementWidth) {
                        var i = 1;
                        var fits = null;
                        var subString = null;
                        while (i < text.length && !fits) {
                            subString = text.substring(0, text.length - i);
                            if (getTextWidth(subString, font) > elementWidth) {
                                i++;
                            } else {
                                fits = true;
                                element.html(subString.substring(0, subString.length - 4)+'...');
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
                        },0);
                    });
                };
            }
        }
    }]);