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

<form ng-controller="XModifyUserGroupsCtrl"
      ng-init='nodeid={$id_node}; user_name="{$user_name}"; general_role={$general_role}; all_roles={$all_roles};
      filtered_groups={$filtered_groups}; user_groups_with_role={$user_groups_with_role}; init();'
      method="post" action="{$action_url}" class="form_group_user">

    <div class="action_header">
        <h5 class="direction_header"> Name Node: {t}Manage groups{/t}</h5>
        <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
        <hr>
    </div>

    <div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Available groups{/t}</h2>
            </div>
        <div class="associate-group">
            <div ng-show="filtered_groups.length>0" class="">
                <div class="input-select icon small-12 columns">
                    <label class="label_title label_general label-select">{t}Name{/t}</label>
			 			<select  class=' block' ng-model="newGroup"
                                ng-options="group_info as group_info.Name for group_info in filtered_groups">

                        </select>
                </div>
                    <div class="input-select icon small-12 columns">
                        <label ng-init="" class="label_title label_general label-select">{t}Rol{/t}</label>
						<select  class=' block' ng-model="newRole"
                                ng-options="rol_info.IdRole as rol_info.Name for rol_info in all_roles">

                        </select>

                    </div>
                </div>

            <div class="small-12 columns">
                <div class="alert alert-info" ng-hide="filtered_groups.length>0">
                    <strong>Info!</strong>
                <p >{t}There are not{/t} <span ng-if="user_groups_with_role.length>0">{t}more{/t} </span>{t}available groups to be associated with the user{/t}</p>
                </div></div>
        <div class="small-12 columns">
            <fieldset class="buttons-form">
                <button type="button" id="" ng-click="addGroup()" class="btn ui-state-default ui-corner-all button submit-button ladda-button main_action" data-style="slide-up" data-size="xs" tabindex=""><span class="ladda-label">Add</span></button>
            </fieldset></div></div></div>
        <br>
        <div class="row tarjeta">
            <h2 class="h2_general">#/user_name/# {t}belongs to the next groups{/t}</h2>

        <div ng-if="user_groups_with_role.length>0" class="change-group">

                <div ng-repeat="user_group_info in user_groups_with_role" class="row-item icon">

                    <span class=" col1-3 columns">
                        <label ng-init="" class="label_title label_general label-select">#/user_group_info.Name/#</label></span>
                    <span class="col1-3">
                      <div  class="input-select icon">
                        <select name='idRole' class='block'
                                ng-model="user_groups_with_role[$index].IdRole"
                                ng-change="user_groups_with_role[$index].dirty=true"

                                ng-options="rol_info.IdRole as rol_info.Name for rol_info in all_roles">

                        </select></div>
                    </span>

                    <div style=" bottom: -70px; " class="buttons-form row-item-actions col1-3">
                        <span ng-show="user_groups_with_role[$index].dirty">

                            <button type="button" class="recover-btn icon btn-unlabel-rounded-recover"
                                    ng-click="update($index)"
                                    >
                                <span>{t}Update{/t}</span>
                            </button>
                        </span>
                        <span ng-if="user_group_info.IdGroup != '101'">
                            <button type="button" class="delete-btn icon btn-unlabel-rounded-delete"
                                    ng-click="openDeleteModal($index)"
                                    >
                                <span>{t}Delete{/t}</span>
                            </button>
                        </span>
                    </div>
                </span>
        </div>
        </div>
    </div>

    <div class="small-12 columns">
        <div class="alert alert-info" ng-if="!user_groups_with_role">
            <strong>Info!</strong>{t}There are no groups associated with user yet{/t}

        </div></div>
</form>
