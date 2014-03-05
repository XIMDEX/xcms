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

<form method="post" name="workflow_forward" action="{$action_url}">
<div class="action_header">
        <h2>{t}Next state{/t}</h2>
        <fieldset class="buttons-form">
                {button class="validate accept-button btn main_action" label="Accept"}
        </fieldset>
</div>
	<input type="hidden" name="default_message" value="{$defaultMessage}">
<div class="action_content">
	<fieldset class="">
		<ol>
			<li>
				<span>{t}Do you want to move this file{/t} {t}from the state{/t} <strong>{$currentStateName}</strong> {t}to the state{/t}: </span>
				<select name="nextstate">
					{foreach from=$allowedstates key=state item=stateName}
                                                <option value="{$state}">
                                                        {$stateName} 
                                                </option>
                                        {/foreach}
				</select>
				<span>?</span>
			</li>
		</ol>
	</fieldset>
	
        <fieldset class="notifications">
                                        <span class="">
                                        <input type="checkbox" name="sendNotifications" id="sendNotifications" class="send-notifications hidden-focus" value="1" {if $required == 1}checked="checked"{/if} />
                                                <label for="sendNotifications" class="checkbox-label icon">{t}Send notifications{/t}</label>
                                        </span>
                                    <ol>
                                        <li class="conditioned {if $required != 1}hidden{/if}">
                                                <label for="groups" class="label_title">{t}Group{/t}</label>
                                                    <select id="groups" name="groups" class="cajaxg group_info">
                                                {counter assign=index start=1}
                        {foreach from=$group_state_info key=index item=group}
                                                            <option value="{$group.IdGroup}|{$group.IdState}" {if $index == 0}selected="selected"{/if}>{$group.groupName} ({$group.stateName})</option>
                                                {counter assign=index}
                        {/foreach}
                                                    </select>
                                        </li>
                                
                        <li class="conditioned {if $required != 1}hidden{/if}">
                                                <label class="label_title">{t}Users{/t}</label>
                                                <div class="user-list-container">
                                                        <ol class="user-list">
                                                        {counter assign=index start=1}
                            {foreach from=$notificableUsers item=notificable_user_info}
                                                                <li class="user-info">
                                                                        <input type="checkbox" name="users[]" class="validable notificable check_group__notificable" id="user_{$notificable_user_info.idUser}" value="{$notificable_user_info.idUser}" {if $index == 1}checked="checked"{/if} />
                                                                        <label for="user_{$notificable_user_info.idUser}">{$notificable_user_info.userName}</label>
                                                                </li>
                                                        {counter assign=index}
                            {/foreach}
                                                        </ol>
                                                </div>
                                        </li>
                                
                        <li class="conditioned {if $required != 1}hidden{/if}">
                                                <label for="texttosend" class="label_title">{t}Comments{/t}:</label>
                                                <textarea class="validable not_empty comments" name="texttosend" id="texttosend" rows="4" wrap="soft" tabindex="7">{$defaultMessage}</textarea>
                                        </li>
                    </ol>
                        </fieldset>                     
                        </div>
        </div>
</form>
