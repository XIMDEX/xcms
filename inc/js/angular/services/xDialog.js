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

angular.module('ximdex.common.service')//Abstraction for server communications. TODO: Expose to client a REST like interface
    .factory('xDialog', ['$window', function($window) {
        return {
            openConfirmation: function(result, message){
                //TODO: Write a native dialog without jquery dependencies and support for templates directives etc
                var $dialog = $window.jQuery('<div class="form_send_dialog"><div/>').html(message || _('Are you sure'));
                $dialog.dialog({
    				title: 'Ximdex Notifications',
    				modal: true,
    				buttons: {
    					_('Accept'): function() {
    						result(true)
    						$dialog.dialog('destroy');
    						$dialog.remove();
    					}.bind(this),
    					_('Cancel'): function() {
    						result(false)
    						$dialog.dialog('destroy');
    						$dialog.remove();
    					}.bind(this)
    				}
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