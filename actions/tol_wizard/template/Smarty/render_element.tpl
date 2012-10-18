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

{* tag_info.attributes.label root tag_info *}				

		{if $tag_info.attributes.view == 'text'}
        <p>
			<label>{$tag_info.attributes.label}</label>
			{if $tag_info.attributes.editable == 'false'}
				<input class="tolFieldInput {$_tag_name}" type="text" readonly name="d[{$seed}]{$root}[_value_]" value="{$tag_value|escape:'html'}"/>
			{else}
				<input class="tolFieldInput {$_tag_name}"  type="text" name="d[{$seed}]{$root}[_value_]" value="{$tag_value|escape:'html'}"/>
			{/if}
		</p>
		{/if}
		
		{if $tag_info.attributes.view == 'hidden'}
        <p>
			<input class="tolFieldInput {$_tag_name}"  type="hidden" name="d[{$seed}]{$root}[_value_]" value="{$tag_value|escape:'html'}"/>
            </p>
		{/if}
		
		{if $tag_info.attributes.view == 'textarea'}
			<div class="editor_label">
            <p>
				<label>{$tag_info.attributes.label}</label>
                </p>
			</div>
			<div class="editor_content">
				<textarea class="tolFieldTextarea {$_tag_name}"  name="d[{$seed}]{$root}[_value_]" class="ckeditor" cols="1" rows="1">{$tag_value|escape:'html'}</textarea>
			</div>
		{/if}
        
		{if $tag_info.attributes.view == 'choice' or $tag_info.attributes.view == 'dropdown'}
        <p>
                <label>{$tag_info.attributes.label}</label>
                <select class="tolFieldSelect {$_tag_name}" class="tolTextField {$_tag_name}"  name="d[{$seed}]{$root}[_value_]">
                {foreach from=$tag_info.children item=tag_info_children}
                	<option class="tolFieldOption" value="{$tag_info_children.textnode|escape:'html'}"{if $tag_info_children.textnode == $tag_value} selected="selected"{/if}>{$tag_info_children.textnode|escape:'htmlall'}</option>
                {/foreach}
                </select>
                </p>
        {/if}

        {if $tag_info.attributes.view == 'checkboxes'}
        <p>
			<label>{$tag_info.attributes.label}</label>
			{foreach from=$tag_info.children item=tag_info_children}
				<input class="tolFieldInput {$_tag_name}" name="d[{$seed}]{$root}[value][_value_][]" {if is_array($tag_value) && in_array($tag_info_children.textnode, $tag_value)} checked{/if} value="{$tag_info_children.textnode|escape:'html'}" type="checkbox" class="check"/>
               
                {$tag_info_children.textnode|escape:'htmlall'} 
			{/foreach}
            </p>
        {/if}

		{if $tag_info.attributes.view == 'autocomplete'}
			<p class="autocomplete_container">
				<label>{$tag_info.attributes.label}</label>
				<input class="xxx tolFieldInput {$_tag_name}" type="text" name="d[{$seed}]{$root}[_value_]" value="{$tag_value|escape:'html'}"/>
				<ul class="autocompleted">
					{foreach from=$tag_info.children item=tag_info_children}
						<li style="display:none" class="test_autors">{$tag_info_children.textnode|escape:'htmlall'}</li>
					{/foreach}
				</ul>
            </p>
		{/if}

		{if $tag_info.attributes.view == 'dialog'}
        <p>
			<label>{$tag_info.attributes.label}</label>
			<input class="tolFieldInput {$_tag_name}" type="text" name="d[{$seed}]{$root}[_value_]" value="{$tag_value|escape:'html'}"/>
			<a href="#" onclick="show_dialog('d[{$seed}]{$root}[_value_]');">Modificar</a>
            </p>
			<div class="div_dialog" id="{$tag_info.attributes.dialog}"></div>

		{/if}

		{if $tag_info.attributes.view == 'indice'}
			<div id="indice_{$_tag_name}">
				<label>{$tag_info.attributes.label}</label>

				<p>
					<a href="#" class="tolIndexLink tolil-seed-{$seed} tolil-root-{$root}">Insertar documentos</a>
				</p>
				{foreach from=$tag_info.children key=_tag_name item=children_tag_info}
					{include file="`$view_folder`render_element.tpl" tag_name="`$_tag_name`" 
						root="`$root`[`$_tag_name`]" tag_info="`$children_tag_info`"}
				{/foreach}

			</div>

		{/if}
