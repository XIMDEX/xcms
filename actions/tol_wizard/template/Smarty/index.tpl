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

<tr>
	<td class="cabeceratabla">{t}Edit document{/t}&nbsp;&raquo;</td>
</tr>
<tr>
	<td align="center" class="filaclara" width="100%">
	
		<form method="post" name="tol_wizard" id="tol_wizard" action="{$action_url}">
		<input type="hidden" name="actionid" value="{$actionid}" />
		<input type="hidden" name="nodeid" value="{$nodeid}" />
		<input type="hidden" name="id_template" value="{$id_template}" />
		<input type="hidden" name="template_name" value="{$template_name}" />
		<input type="hidden" name="seed" value="{$seed}" />
		{* Alimentacion inicial via render *}
			<div class="wizard">
				<div class="form">
				{foreach from=$form_elements key=tag_name item=form_element}
						{include file="`$view_folder`render.tpl" tag_name="`$tag_name`" tag_info="`$form_element`" root="[`$tag_name`]"}
				{/foreach}
				</div>
				<div class="buttons">
					{button label="Send" class="validate" }<!--message="Would you like to save the changes?"-->
				</div>
			</div>
		</form>
	</td>
</tr>
