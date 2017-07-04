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

<form ng-controller="XModifyGroupUsersCtrl"
      ng-init='nodeid={$idnode}; name="{$name}"; users_not_associated={$users_not_associated}; roles={$roles};
      users_associated={$users_associated}; init();' method="post" action="{$action_url}" class="form_group_user">

    <div class="action_header">
        <h5 class="direction_header"> Name Node: {t}Change users{/t}</h5>
        <h5 class="nodeid_header"> ID Node: {$nodeid}</h5>
        <hr>
    </div>

    <div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Available users{/t}</h2>
            </div>
            <div ng-show="users_not_associated.length>0" class="">
                <div class="input-select icon small-12 columns">
                    <label ng-init="newUser=users_not_associated[0]" class="label_title label_general label-select">{t}Name{/t}</label>
                    <select  class='cajaxg' ng-model="newUser"
                             ng-options="user as user.name for user in users_not_associated">
                    </select>
                </div>

                <div class="input-select icon small-12 columns">
                    <label ng-init="" class="label_title label_general label-select">{t}Rol{/t}</label>
                    <select  class='' ng-model="newRole"
                             ng-options="key as rol for (key, rol) in roles">
                    </select>

                </div></div>
            <div class="small-12 columns">
                <div class="alert alert-info" ng-hide="users_not_associated.length>0">
                    <strong>Info!</strong> {t}There are not{/t} <span ng-if="users_associated.length>0">{t}more{/t} </span>{t}available users to be associated with the group{/t} #/name/#.
                </div></div>

            <div class="small-12 columns">
                <fieldset class="buttons-form">
                    <button type="button" id="" ng-click="addGroup()" class="btn ui-state-default ui-corner-all button submit-button ladda-button main_action" data-style="slide-up" data-size="xs" tabindex=""><span class="ladda-label">Add</span></button>
                </fieldset></div>
        </div>
        <br>
        <div class="row tarjeta">
            <h2 class="h2_general">{t}The next users belongs to the group{/t} #/name/#</h2>
            <div  class="change-group">

                <div ng-repeat="user in users_associated" class="row-item icon">

                    <span class="col-1-3">
                        <label ng-init="" class="label_title label_general label-select">#/user.UserName/#</label></span>
                        <span class="col1-3">
                            <div class="input-select icon">
                        <select name='idRole' class='block'
                                ng-model="users_associated[$index].IdRole"
                                ng-change="users_associated[$index].dirty=true"
                                ng-options="key as role for (key, role) in roles">

                        </select></div></span>

                    <div style=" bottom: -70px;" class="buttons-form row-item-actions col1-3">
                        <span ng-show="users_associated[$index].dirty">
                            <button type="button" class="recover-btn icon btn-unlabel-rounded-recover"
                                    ng-click="update($index)"
                            >
                                <span>{t}Update{/t}</span>
                            </button>
                        </span>
                        <span ng-if="nodeid != '101'">
                            <button type="button" class="delete-btn icon btn-unlabel-rounded-delete"
                                    ng-click="openDeleteModal($index)">
                                <span>{t}Delete{/t}</span>
                            </button>
                            <button ng-if="$middle" type="button" class="delete-btn icon btn-unlabel-rounded" ng-click="openDeleteModal($index)"></butto
                        </span>
                    </div>
                    </div>
                <div class="small-12 columns">
                    <div class="alert alert-info" ng-if="users_associated.length<=0">
                        <strong>Info!</strong> {t}There are no users associated with this group yet{/t}.
                    </div>
                </div>

            </div>
        </div></div>


</form>


