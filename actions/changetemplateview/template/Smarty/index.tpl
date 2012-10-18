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


<form method=POST action='{$action_url}'>
	<INPUT TYPE=hidden NAME='nodeid' VALUE='{$id_node}' class='ecajag'>
	<br>
	
	<table  class="tabla" width="560" align="center" cellpadding="3">
		<tr>
			<td align="center" class="filaclara"><br>
				<table align=center class=tabla width=420 cellpadding="1" cellspacing="1">
					<tr>
						<td class="cabeceratabla" colspan="2">{$title}</td>
					</tr>
					<tr>
						<td class="filaoscuranegrita">{t}Template{/t}</td>
						{if ($templates)}
							<td class="filaclara">
								<select name="templateid" class="cajaxg">
								{foreach from=$templates item=template}
									<option value="{$template.id}">{$template.name}</option>';
								{/foreach}
								</select>
							</td>
						{else}
							<td class="filaclara">{$no_template}</td>
						{/if}
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table align=center border="0" width=420 cellpadding="1" cellspacing="1">
		<tr>
			<td align="right">						
				{if ($templates)}
					<input type="image" src="{$_URL_ROOT}/xmd/images/botones/crear.gif" title="{$button}" onclick="if(confirm('{$question}')) this.form.submit()">
				{/if}
			</td>
		</tr>
	</table>
</form>
