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

angular.module('ximdex.common.service')
    .factory('xDialog', ['$window', 'xTranslate', function($window, xTranslate) {
        return {
            openConfirmation: function(result, message){
                //TODO: Write a native dialog without jquery dependencies and support for templates directives etc
                //or better use angular.ui modals
                var $dialog = $window.jQuery('<div class="form_send_dialog"><div/>').html(message || 'Are you sure');
                
                var buttons  = {};
                buttons[xTranslate('ui.dialog.confirmation.accept')] = function() {
                    result(true)
                    $dialog.dialog('destroy');
                    $dialog.remove();
                }.bind(this);
                buttons[xTranslate('ui.dialog.confirmation.cancel')] = function() {
                    result(false)
                    $dialog.dialog('destroy');
                    $dialog.remove();
                }.bind(this);

                $dialog.dialog({
    				title: 'Ximdex Notifications',
    				modal: true,
    				buttons: buttons
    			});
    			return {
    				close: function(){
    					$dialog.dialog('destroy');
    					$dialog.remove();
    				}
    			}
            }
        }
    }]);