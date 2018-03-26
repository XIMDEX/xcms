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

<form method="post" id="msu_form" ng-controller="XModifyStatesCtrl" ng-init="idNode={$idNode};">
    <!-- header -->
    <div class="action_header">
        <h5 class="direction_header"> {t}Name Node:{/t} {t}Manage workflow{/t}</h5>
        <h5 class="nodeid_header"> {t}ID Node:{/t} {$nodeid}</h5>
        <hr>
    </div>
    <!-- / header -->

    <!-- message -->
    <div ng-view ng-show="thereAreMessages" class="slide-item #/messageClass/# message">
        <p>#/message/#</p>
    </div>
    <!-- / message -->

    <!-- content -->
    <div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Existing status{/t}</h2>
            </div>

            <!-- old -->
            <ul ui-sortable="sortableOptions" class="sortable" ng-model="all_status_info" ng-init='all_status_info={$all_status_info}; first=all_status_info[0]; last=all_status_info[all_status_info.length-1]'>

                <li ng-repeat="status in all_status_info" ng-class="{literal}{sortable: $middle, first: $first, last: $last}{/literal}">
                    <label for="id_#/status.id/#" class="status li_central label_general" style="margin-left: 20px!important;">
                        {t}Status{/t} #/$index+1/#:
                    </label>


                    <input ng-disabled="!$middle" placeholder="{t}Status name{/t}" type="text" id="id_#/status.id/#" class="name input_general" ng-model="status.name"/>

                    <input ng-disabled="!$middle" placeholder="{t}Description{/t}" type="text" class="description input_general" ng-model="status.description">

                    {*<img src="{url}/assets/images/show.png{/url}" class="modifyrolesstate"/>*}

                    <img ng-if="!$first && !$last" alt="<t>Flecha</t>" title="Cambiar orden" src="{url}/assets/images/action/move.png{/url}" class="sortable_element"/>

                    <button ng-if="$middle" type="button" class="delete-btn icon btn-unlabel-rounded" ng-click="deleteStatus($index)"></button>

                    <div ng-click="addStatus($index)" ng-if="$first || $middle" class="separator">
                        <button type="button" class="add-btn icon btn-unlabel-rounded"></button>
                    </div>
                </li>
            </ul>
            <!-- / old -->

            <br>
            <div class=" {if $is_workflow_master == true}disabled{/if}">
                <div class="small-2 columns">
                    <label for="id_nodetype" class="aligned label_general">{t}Node type (optional){/t}:</label>
                </div>

                <div class="small-10 columns">
                    <div class="input-select2">
                    <select ng-model="nodetype" ng-options="nt.id as nt.name for nt in nodetype_list" class="cajag form-control" {if $is_workflow_master == true}disabled{/if}>
                        <option>{t}Select a node type{/t}</option>
                    </select>
                    </div></div>
            </div>

            <br>
            <br>
            <div class="checkbox {if $is_workflow_master == true}disabled{/if}">
                <div class="small-12 columns">
                <div class="input">
                <input class="hidden-focus" type="checkbox" name="is_workflow_master" id="is_workflow_master" {if $is_workflow_master == true}checked="checked" disabled="disabled"{/if}/>
                <label class="icon checkbox-label">{t}This workflow will behave as default item{/t}</label>
                </div>
                </div></div>
            <div class="small-12 columns">
            <fieldset ng-init="loading=false; label='{t}Save changes{/t}';" class="buttons-form">
                <button class="button_main_action" xim-button xim-loading="loading" xim-label="label" xim-progress="" xim-disabled="" ng-click="saveChanges();"></button>
            </fieldset>
            </div></div></div>
    <!-- / content -->
</form>



