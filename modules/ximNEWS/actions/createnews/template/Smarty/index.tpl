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
	<legend><span>{t}New news{/t}</span></legend>
		<input type="hidden" name="nodeid" value="{$id_node}"/>
		<ol>
			<li>
				<label for="plantilla" class="aligned">{t}Template{/t}</label>
				<select name="template" id="plantilla" class="validable not_empty">
					<option value="">{t}Select template{/t}</option>
					{foreach from=$templates key=ene item=template}
						<option value="{$template.id}">{$template.name}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label class="aligned">{t}Batch of images{/t}</label>
				<select id="nodoIDF" name="loteid" class="caja">
					<option value="0">{t}(none){/t}</option>
					{foreach from=$lotes key=ene item=lote}
						<option value="{$lote.IdNode}">{$lote.Name}</option>
					{/foreach}
				</select>
			</li>
		</ol>
	</fieldset>

	<fieldset>
		<legend><span>{t}Idiomas{/t}</span></legend>
		<ol>
			{foreach from=$languages key=ene item=language}
				<li><label class="aligned">{$language.name}</label>
					<input type="checkbox" class="validable languages check_group__languages"
					name="langidlst[]" value="{$language.id}">

					<input type="text" name="{$language.id}" class="cajag">
				</li>
			{/foreach}

			<li>
				<label class="aligned">{t}Select master language{/t}</label>
				<select class="cajaxg" name='master'>
					<option value="">{t}None{/t}</option>
					{foreach from=$languages item=language}
						<option  {if $colector_data.master_lang}selected="selected"{/if} value="{$language.id}">
							{$language.name}</option>
					{/foreach}
				</select>
			</li>
		</ol>
	</fieldset>

	<fieldset>
		<legend><span>{t}Channels{/t}</span></legend>
		<ol>
			{foreach from=$channels key=ene item=channel}
				<li>
					<input name="channellst[]" class="validable canales check_group__canales"
						type="checkbox" value="{$channel.IdChannel}"
					/>
					<label>{$channel.Description}</label>
				</li>

			{/foreach}
		</ol>
	</fieldset>

	<fieldset class="buttons-form">
		{button label="Create" class="validate btn main_action"}
	</fieldset>
</form>
