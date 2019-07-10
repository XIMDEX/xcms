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

//Slide animation for ngHide/Show
angular.module('ximdex')
	.animation('.slide-item', [function() {
		var runTheAnimation = function(element, done){
			done();
		}
		//TODO: Change jquery code for vanilla javascript
		var runTheShowAnimation = function(element, done){
			var duration = parseInt(element.attr('xim-slide-duration')) || 400;
			jQuery(element).hide().slideDown(duration, done);
		}
		var runTheHideAnimation = function(element, done){
			var duration = parseInt(element.attr('xim-slide-duration')) || 400;
			jQuery(element).slideUp(duration, done);
		}

		return {
		    //this is called BEFORE the class is removed
	        beforeAddClass : function(element, className, done) {
	          if(className == 'ng-hide') {
		        runTheHideAnimation(element, done);
		      }
		      else {
		        runTheAnimation(element, done);
		      }

		      return function onEnd(element, done) { };
	        },
		    removeClass : function(element, className, done) {
		      if(className == 'ng-hide') {
		        runTheShowAnimation(element, done);
		      }
		      else {
		        runTheAnimation(element, done);
		      }

		      return function onEnd(element, done) { };
		    }
		  }
}]);
