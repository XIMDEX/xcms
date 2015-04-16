<?php
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
?>

<form method="post" ng-controller="XowlConfigurationController" name="form" ng-submit="processForm()" ng-cloak>
    <input type="hidden" name="method" value="<?php echo $goMethod ?>">
	<h2>Xowl configuration (optional)</h2>
    <div class="form_item full-width">
        <label for="apikey">Introduce your generated API key</label>
        <input type="text" name="apikey" id="apikey" placeholder="Insert your API key here"
               ng-model="apikey" pattern="^((\s*\d+-\d+-\d+\s*)|)$"
               ng-class="{'error_element':form.apikey.$error.pattern}"
               ng-model-options="{ debounce: 400 }"/>
        <p class="error" ng-show="form.apikey.$error.pattern">This API key is not valid!</p>
    </div>
    <div class="form_item full-width">
        <label for="serviceurl">Xowl Service URL</label>
        <input type="text" name="serviceurl" id="serviceurl"
               placeholder="Insert your service URL here" ng-model="serviceurl"
               ng-class="{'error_element':form.serviceurl.$error.pattern}"
               pattern="^((\s*https?://.+\s*)|)$" ng-model-options="{ debounce: 400 }"/>
        <p class="error" ng-show="form.serviceurl.$error.pattern">This URL is not valid!</p>
    </div>
    <div ng-if="error">
        <p class="warning">{{message}}</p>
    </div>
    <div ng-if="success">
        <p class="success_element">{{message}}</p>
    </div>
    <div class="form_item full-width" >
        <label>If you don't want to configure this module, please leave these fields in blank.</label>
        <button class="action_launcher ladda-button" ui-ladda xim-state="loading" data-style="slide-up">Continue</button>
    </div>
</form>
