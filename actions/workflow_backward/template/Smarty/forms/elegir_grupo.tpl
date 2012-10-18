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

{if ($emptyGroup)}
<script type="text/javascript">
	// if group is empty, warning
	alert(_('A publisher group should be selected'));
	</script>
{/if}
<form method='post' action='{$action_url}'>
	<input type="hidden" name="nodeid" value="{$id_node}"> <input type="hidden" name="userid" value="{$userID}"> <input type="hidden" name="estado" value="mostrarusers"><br/>
	<fieldset>
		<legend>
			<span>{t}Select the group to use{/t} </span>
		</legend>
		<ol>
			{if ($allGroups)}
				<li>&middot;<strong>{t}Warning{/t}:</strong> {t}In your work groups there is not any user which could attend to your request.{/t}</li>
				<li>&middot; {t}Other work groups with permissions to attent to it will be shown below.{/t}</li>
				<li>
					<table>
						{if ($validGroup)}
							<tr>
								<td class="filaoscuranegritac">&nbsp;</td>
								<td class="filaoscuranegritac">{t}Group{/}</td>
								<td class="filaoscuranegritac">{t}Previous state{/t}</td>
							</tr>
							{foreach from=$validGroupList item=_group}
								<tr>
									<td class="filaclara" align="center">
										<input type="radio" name="idx" value="{$_group.idx}"> <input type="hidden" name="groups[]" value="{$_group.groupID}"> <input type="hidden" name="states[]" value="{$_group.stateID}">
									</td>
									<td class="filaclara">
										&nbsp; {$_group.nombreGroup}
									</td>
									<td class="filaclara">
										&nbsp; {$_group.nombreEstado}
									</td>
								</tr>
							{/foreach}
						{else}
							<tr>
								<td class="filaclara" align="center"></td>
								<td class="filaclara" colspan="2">
									<ul>
										<li>{t}There is not any group to notify which could attend to your request.{/t}</li>
										<li>{t}Please contact your administrator.{/t}</li>
									</ul>
								</td>
							</tr>
						{/if}
					</table>
				</li>
			{/if}
		</ol>
	</fieldset>
	{if ($validGroup)}
		<fieldset>
			<ol>
				<li>{button label="Next" class="validate"}</li>
			</ol>
		</fieldset>
	{/if}
</form>