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


<form method="post" action='{$action_url}'>
	<input type="hidden" id="idboton" name='boton' value=''> <input type="hidden" name="nodeid" value='{$id_node}'> <input type="hidden" name="userid" value='{$userID}'>
	<input type="hidden" name="groupid" value='{$groupID}'> <input type="hidden" name="stateid" value='{$nextstateID}'> <input type="hidden" name="estado" value='mostrarusers'>
	<fieldset>
		<legend><span>{t}Notifications{/t}</span></legend>
		<p><strong>Atenci&oacute;n:</strong> {$message}</p>
		<ol>
			<li>{t}Group{/t}: {$groupName}</li>
			<li>{t}Next state{/t}: {$workflow_name}</li>
			<li>{t}Select the users to notify{/t} &raquo;
				<table>
					{foreach from=$userlist item=_user}
						<tr>
							<td align="center" class="filaclara">
								<input type='checkbox' name='check[]' value='{$_user.id}'> <input type='hidden' name='users[]' value='{$_user.id}'>
							</td>
							<td class="filaclara">
								{$_user.name}
							</td>
						</tr>
					{/foreach}
				</table>
			</li>
			<li>
				<label for="texttosend" class="aligned">{t}Add comments{/t}:</label> 
				<textarea class="normal" name="texttosend" id="texttosend" rows=16 cols=65 wrap="soft" style="width: 540px; height: 240px;" tabindex="7"></textarea>
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons-form">
		<ol>
			<li>
				{button label="Back" class='validate' onclick="$('#idboton').val('previous');"}
				{button label="Next" class='validate' onclick="$('#idboton').val('next');"}
			</li>
		</ol>
	</fieldset>
</form>