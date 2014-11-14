{**
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
 *}

<form method="post" id="msr_action" ng-controller="XModifyStatesRoleCtrl">
    <div class="action_header">
        <h2>{t}Modify asociated status of role{/t}</h2>
        <fieldset ng-init="label='{t}Save changes{/t}'; loading=false;" class="buttons-form">
            <button class="button_main_action"
                    xim-button
                    xim-loading="loading"
                    xim-label="label"
                    xim-progress=""
                    xim-disabled=""
                    ng-click="saveChanges();">
        </fieldset>
    </div>

    <div ng-view ng-show="thereAreMessages" class="slide-item #/messageClass/# message">
        <p>#/message/#</p>
    </div>

    <div class="action_content" ng-init='idRole={$idRole}; all_states={$all_states};'>
        <p>{t}Select the status asociated with the role{/t}:</p>
        <fieldset>
            <div class="col1-2">
                <p ng-repeat="state in all_states">
                    <span>
                        <input type="checkbox" class="hidden-focus" id="#/state.name/#_#/idRole/#"
                               ng-model="state.asociated"/>
                        <label for="#/state.name/#_#/idRole/#"
                               class="checkbox-label icon">#/state.name/#</label>
                    </span>
                </p>
            </div>
        </fieldset>
    </div>
</form>
