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
						{button label="Check dependencies" class="open_report btn"}
						{button label="Modify" class='validate btn main_action' }<!--message="Are you sure you want to change the name?"-->
			</fieldset>
	</div>
	<div class="action_content"><fieldset>
			<ol>
				<li>
					<label for="name" class="aligned">{t}Name{/t}</label>
					<input type="text" name="name" id="name" value="{$name}" class="cajaxg validable not_empty">
				</li>
				{if ($module_ximnews == true)}
				<li><input type="radio" name="template_type" value="generic_template"{if $template_type == 'generic_template' || $template_type == ''} checked="checked"{/if}>{t}Generic use template{/t}</li>
				<li><input type="radio" name="template_type" value="news_template"{if $template_type == 'news_template'} checked="checked"{/if}>{t}XimNEWS news template{/t}</li>
				<li><input type="radio" name="template_type" value="bulletin_template"{if $template_type == 'bulletin_template'} checked="checked"{/if}>{t}XimNEWS newsletter template{/t}</li>
				{/if}

				<li>
					{if !empty($valid_pipelines)}

								<label for="id_pipeline" class="aligned">{t}Associated workflow{/t}</label>

								<select name="id_pipeline" id="id_pipeline" class="cajag" disabled>
									<option value="">{t}Select a workflow{/t}</option>
								{foreach from=$valid_pipelines key=id_pipeline item=pipeline}
									<option value="{$id_pipeline}" {if $id_pipeline == $selected_pipeline} selected="selected"{/if}>{$pipeline}</option>
								{/foreach}
								</select>

					{/if}
				</li>
			</ol>
		</fieldset>
		    {if $is_section}
		    <h2>{t}Language{/t}</h2>
					<fieldset>
						<ol>
						{foreach from=$all_languages item=language_info}
						<li><label class="aligned">{$language_info.Name}</label>

								<input type='text' name="language[{$language_info.IdLanguage}]" value="{$language_info.alias}" class='cajaxg'>
						</li>
						{/foreach}
						</ol>
					</fieldset>
				{/if}</div>

</form>
