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
<form method="post" id="msu_form" ng-controller="XModifyStatesCtrl"
        ng-init="idNode={$idNode};">
    <div class="action_header">
        <h2>{t}Manage workflow{/t}</h2>
        <fieldset ng-init="loading=false; label='{t}Save changes{/t}';" class="buttons-form">
            <button class="button_main_action"
                    xim-button
                    xim-loading="loading"
                    xim-label="label"
                    xim-progress=""
                    xim-disabled=""
                    ng-click="saveChanges();">
            </button>
        </fieldset>
    </div>

    <div ng-view ng-show="thereAreMessages" class="slide-item #/messageClass/# message">
        <p>#/message/#</p>
    </div>

    <div class="action_content">
        <fieldset>
            <ol>
                <li ng-init='nodetype_list={$nodetype_list};'>
                    <label for="id_nodetype" class="aligned">{t}Node type (optional){/t}</label>
                    <select ng-model="nodetype" ng-options="nt.id as nt.name for nt in nodetype_list"
                            class="cajag" {if $is_workflow_master == true} disabled{/if}>
                        <option value="">{t}Select a node type{/t}</option>
                    </select>
                </li>
                <li>
                    <input type="checkbox" name="is_workflow_master"
                           id="is_workflow_master"{if $is_workflow_master == true} checked="checked" disabled="disabled"{/if} />
                    {t}This workflow will behave as default item{/t}
                </li>
                <li>
                    <strong>{t}Existing status{/t}</strong>
                </li>
                <li>
                    <ul ui-sortable="sortableOptions" class="sortable"
                        ng-model="all_status_info"
                        ng-init='all_status_info={$all_status_info}; first=all_status_info[0]; last=all_status_info[all_status_info.length-1]'>
                        <li ng-repeat="status in all_status_info"
                            ng-class="{literal}{sortable: $middle, first: $first, last: $last}{/literal}">

                            <label for="id_#/status.id/#" class="status">{t}Status{/t} #/$index+1/#:</label>

                            <input ng-disabled="!$middle" placeholder="{t}Status name{/t}" type="text" id="id_#/status.id/#" class="name"
                                   ng-model="status.name"/>

                            <input ng-disabled="!$middle" placeholder="{t}Description{/t}" type="text" class="description"
                                   ng-model="status.description"">
                            {*<img src="{$_URL_ROOT}/xmd/images/show.png" class="modifyrolesstate" />*}
                            <img ng-if="!$first && !$last" alt="<t>Flecha</t>" title="Cambiar orden"
                                 src="{$_URL_ROOT}/xmd/images/action/move.png" class="sortable_element"/>
                            <button ng-if="$middle" type="button" class="delete-btn icon btn-unlabel-rounded"
                                    ng-click="deleteStatus($index)"
                                    >
                                <span>{t}Delete status{/t}</span>
                            </button>
                            <div ng-click="addStatus($index)" ng-if="$first || $middle" class="separator">
                                <button type="button" class="add-btn icon btn-unlabel-rounded">
                                    <span>{t}Add status{/t}</span>
                                </button>
                            </div>
                        </li>

                    </ul>
                </li>
            </ol>
        </fieldset>

    </div>
</form>

