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

<form method="post" name="ca_form" id="ca_form"  action="{$action_url}">
	<fieldset>
	<legend><span>{t}View images{/t}</span></legend>
		<input type="hidden" name="nodeid" value="{$id_node}"/>		


            <fieldset>
                <label for="checkbox">{t}Checkboxes{/t}</label>
                <input type="checkbox" name="checkbox" id="checkbox" />
                <input type="checkbox" name="checkbox2" id="checkbox2" />
            </fieldset>

<!--		<ol>
			<li>
				<label for="plantilla">{t}Template{/t}</label>
				<select name="template" id="plantilla" class="validable not_empty">
					<option value="">{t}Select template{/t}</option>
					{foreach from=$templates key=ene item=template}
						<option value="{$template.id}">{$template.name}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label>{t}Image batch{/t}</label>
				<select id="nodoIDF" name="loteid" class="caja">
					<option value="0">(ninguno)</option>
					{foreach from=$lotes key=ene item=lote}
						<option value="{$lote.IdNode}">{$lote.Name}</option>
					{/foreach}
				</select>
			</li>
		</ol>-->
	</fieldset>
</form>
