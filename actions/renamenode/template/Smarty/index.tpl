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

<form method="post" name="ren_form" id="ren_form" action="{$action_url}">
	<input type="hidden" name="id_node" value="{$id_node}" />
	<div class="action_header">
			<h2>{t}Change name{/t}</h2>
			<fieldset class="buttons-form">
			{if $id_nodetype!=5078}
				{button label="Check dependencies" class="open_report btn"}
			{/if}
				{button label="Modify" class='validate btn main_action' }<!--message="Are you sure you want to change the name?"-->
			</fieldset>
	</div>
	<div class="action_content">
			
					<div class="input icon icon-positioned project">
						<input type="text" name="name" id="name" value="{$name}" class="cajaxg validable not_empty full-size">
					</div>
			
			{if $id_nodetype==5078}
				<li><input type="radio" name="schema_type" value="generic_schema"{if $schema_type == 'generic_schema' || $schema_type == ''} checked="checked"{/if}>{t}Generic schema{/t}</li>
				<li><input type="radio" name="schema_type" value="metadata_schema"{if $schema_type == 'metadata_schema'} checked="checked"{/if}>{t}Metadata schema{/t}</li>
				{if ($module_ximnews == true)}
				<li><input type="radio" name="schema_type" value="news_schema"{if $schema_type == 'news_schema'} checked="checked"{/if}>{t}XimNEWS news schema{/t}</li>
				<li><input type="radio" name="schema_type" value="bulletin_schema"{if $schema_type == 'bulletin_schema'} checked="checked"{/if}>{t}XimNEWS newsletter schema{/t}</li>
				{/if}
			{/if}
			
					{if !empty($valid_pipelines)}
					<label for="id_pipeline" class="aligned">{t}Associated workflow{/t}</label>
					<select name="id_pipeline" id="id_pipeline" class="cajag" disabled>
						<option value="">{t}Select a workflow{/t}</option>
					{foreach from=$valid_pipelines key=id_pipeline item=pipeline}
						<option value="{$id_pipeline}" {if $id_pipeline == $selected_pipeline} selected="selected"{/if}>{$pipeline}</option>
					{/foreach}
					</select>
					{/if}

	{if $is_section}
	   <div class="col1-3">
	    	<h3>{t}Language{/t}</h3>

			{foreach from=$all_languages item=language_info}
				<div class="languages-section">
<input type="checkbox"
									name="languages[]" id="lang_{$language_info.Name}" value="{$language.idLanguage}" class= "hidden-focus" />
					<label for="lang_{$language_info.Name}" class="icon checkbox-label">{$language_info.Name}</label>
					<input type='text' name="language[{$language_info.IdLanguage}]" value="{$language_info.alias}" class="alternative-name" placeholder="Nombre alternativo para migas de pan y rutas">
				</div>
			{/foreach}
		</div>
	{/if}
	</div>
</form>

