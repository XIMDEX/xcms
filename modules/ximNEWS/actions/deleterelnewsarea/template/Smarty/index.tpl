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

{if $num_areas eq 0}
	<fieldset><legend><span>{t}Delete news from categories{/t}</span></legend>

	<span>{t}The news is not associated to any category{/t}</span>
	</fieldset>
{else}
	<form method="post" name="ca_form" id="ca_form"  action="{$action_url}">
		<fieldset>
			<input type="hidden" name="nodeid" value="{$id_node}"/>
			<legend><span>{t}Delete associations{/t}</span></legend>
			<p>{t}Select the categories where you want to delete the news{/t}:</p>
			<ol>
				{foreach from=$areas key=ene item=area}
					<li>
						<input name="areas[]" type="checkbox" value="{$area.id}">
						<label>{$area.name} ({$area.desc})</label>
					</li>
				{/foreach}
			</ol>
		</fieldset>

		<fieldset class="buttons-form">
			{button label="Delete" class="validate btn main_action"}
		</fieldset>
	</form>
{/if}
