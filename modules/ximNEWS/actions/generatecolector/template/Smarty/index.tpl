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

<form method="POST" name="ca_form" id="ca_form"  action="{$action_url}">
	<fieldset>
		<input type="hidden" name="nodeid" value="{$id_node}"/>
		<legend><span>{t}Generate colector{/t}</span></legend>
        {if $inactive_msg neq ''}
			<p>{$inactive_msg}.</p>
			{else}
			  <p> {t}The next automatic generation will occur{/t}</p>
			<ul>
				{if $time_msg neq ''}
					<li>{$time_msg}.</li>
				{/if}
				{if $news_msg neq ''}
					<li>{$news_msg}.</li>
				{/if}
			</ul>
		{/if}
		<p>{t}This action will generate the bulletins affected by modifications or new associated news.{/t}</p>
		<p>{t}If total generation checkbox is selected, all the colector's bulletins will be generated even if they are not affected by the introduced news.{/t}</p>

		<p><input type="checkbox" name="total" id="total" />
		<label for="total">{t}Total generation{/t}</label>		</p>
	</fieldset>

	<fieldset class="buttons-form">
		{button label="Generate colector" class="validate"}
	</fieldset>
</form>

