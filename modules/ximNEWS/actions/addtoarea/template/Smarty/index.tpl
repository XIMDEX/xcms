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

{if $areascount neq 0}

	{if count($areas) > 0}
		<form method="post" name="ca_form" id="ca_form" action="{$action_url}">
			<fieldset>
				<legend><span>{t}Associate news to categories{/t}</span></legend>
				<input type="hidden" name="nodeid" value="{$id_node}"/>
				<span>{t}The news can be associated to the following categories{/t}</span>
				<table class="tabla">
					<tr>
						<th>{t}Category/ies{/t}</th>
						<th>{t}Related colectors{/t}</th>
					</tr>
					<label style="visibility:hidden" for="areas">
						{t}Categories{/t}</label>

					{foreach from=$areas key=ene item=area}
						<tr>
							<td>
								<input id="areas[]" name="areas[]" 
									class="validable areas check_group__areas" 
										type="checkbox" value="{$area.id}">
								{$area.name} : {$area.description}
							</td>
							<td align="center">
								{$area.colectores}
							</td>
						</tr>
					{/foreach}
				</table>

			</fieldset>

			<fieldset class="buttons-form">		
				{button label="Asociar" class="validate"}
			</fieldset>
		</form>
	{/if}

	{if count($areasrelated) > 0}
	<fieldset>
		<legend><span>{t}Current associations{/t}</span></legend>
		<span>{t}The news is associated to the following categories{/t}</span>
		<table class="tabla">
			<tr><th>{t}Category/ies{/t}</th>
			<th>{t}Related colectors{/t}</th>

			{foreach from=$areasrelated key=ene item=area}
				<tr>
					<td>
						{$area.name}: {$area.description}
					</td>
					<td align="center">
						{$area.colectores}
					</td>
				</tr>
			{/foreach}
		</table>
		</fieldset>
	{/if}

{else}		
<fieldset>	
	<legend><span>{t}Available categories{/t}</span></legend>
	<p>{t}No category exists{/t}</p>
	</fieldset>
{/if}
