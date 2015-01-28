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
        <h2>{t}Change users{/t}</h2>
        <fieldset class="buttons-form">

        </fieldset>
    </div>

    <div class="action_content">
        <h3>{t}Available users{/t}:</h3>
        <div class="associate-group">
            <div ng-show="users_not_associated.length>0" class="row-item col2-3">
			 		<span ng-init="newUser=users_not_associated[0]" class="col1-2 icon icon-group label-select">
			 			<select  class='select-clean block' ng-model="newUser"
                                 ng-options="user as user.name for user in users_not_associated">
                        </select>
			 		</span>

					<span ng-init="" class="col1-2 icon icon-rol label-select">
						<select  class='select-clean block' ng-model="newRole"
                                 ng-options="key as rol for (key, rol) in roles">
                        </select>

					</span>
                <div class="buttons-form row-item-actions actions-outside col1-3">
                    <button type="button" class="add-btn icon btn-unlabel-rounded"
                            ng-click="addGroup()"
                            >
                        <span>{t}Add user{/t}</span>
                    </button>
                </div>
            </div>

            <p ng-hide="users_not_associated.length>0">{t}There are not{/t} <span ng-if="users_associated.length>0">{t}more{/t} </span>{t}available users to be associated with the group{/t} #/name/#.</p>
        </div>
        <h3>{t}The next users belongs to the group{/t} #/name/#:</h3>
        <div  class="change-group">

            <div ng-repeat="user in users_associated" class="row-item icon">

                    <span class="col1-3">
						#/user.UserName/#
					</span>
                    <span class="col1-3">
                        <select name='idRole' class='select-clean block'
                                ng-model="users_associated[$index].IdRole"
                                ng-change="users_associated[$index].dirty=true"
                                ng-options="key as role for (key, role) in roles">

                        </select>
                    </span>

                <div class="buttons-form row-item-actions col1-3">
                        <span ng-show="users_associated[$index].dirty">
                            <button type="button" class="recover-btn icon btn-unlabel-rounded"
                                    ng-click="update($index)"
                                    >
                                <span>{t}Update{/t}</span>
                            </button>
                        </span>
                        <span ng-if="nodeid != '101'">
                            <button type="button" class="delete-btn icon btn-unlabel-rounded"
                                    ng-click="openDeleteModal($index)"
                                    >
                                <span>{t}Delete{/t}</span>
                            </button>
                        </span>
                </div>
            </div>
            <p ng-if="users_associated.length<=0">{t}There are no users associated with this group yet{/t}.</p>
        </div>
    </div>

    </div>
</form>



