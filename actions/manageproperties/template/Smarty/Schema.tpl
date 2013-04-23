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


{assign var="schemas" value=$properties.Schema}

<fieldset>

<legend><span>{t}Allowed types of templates{/t}</span></legend>
<div>
	<div class="xright-block">
		<input type="radio" name="inherited_schemas" class="schemas_inherited"
			value="inherited" {if $Schema_inherited == 'inherited'}checked{/if} />
		<label>{t}Use inherited templates{/t}</label>
		<ol>
			<li>
			{foreach from=$schemas item=schema}
				{$schema.Name},
			{/foreach}
			</li>
		</ol>
	</div>

	<div class="xright-block">
		<input type="radio" name="inherited_schemas" class="schemas_overwritten"
			value="overwrite" {if $Schema_inherited == 'overwrite'}checked{/if} />
		<label>{t}Overwrite inherited templates{/t}</label>
		<ol>
			{foreach from=$schemas item=schema}
			<li>
				<input
					type="checkbox"
					class="schemas"
					name="Schema[]"
					value="{$schema.IdSchema}"
					{if ($schema.Checked == 1)}
						checked="{$schema.Checked}"
					{/if}
					{if $Schema_inherited == 'inherited'}
						disabled
					{/if}
					/>
				{$schema.Name}
			</li>
			{/foreach}
		</ol>
	</div>
</div>

</fieldset>
