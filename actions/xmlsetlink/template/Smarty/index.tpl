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


<form method="post" name="sl_form" class="sl_form" id="sl_form" action="{$action_url}">
	<input type="hidden" name="id_node" value="{$id_node}" class="id_node" />
	<div class="action_header">
		<h2>{t}Select master document{/t}</h2>
		<fieldset class="buttons-form">
				{button class="validate btn main_action" onclick="" label="Save changes" }{*message="Are you sure you want to performe the changes?"*}
		</fieldset>
	</div>
	<div class="action_content">
		<fieldset>
			{if {count($targetNodes)}}
					<div class="copy_options" tabindex="1">
						{foreach from=$targetNodes key=index item=targetNode}
							<div>
								<input id="{$id_node}_{$targetNode.idnode}" type="radio" name="targetid" value="{$targetNode.idnode}" {if $targetNode.idnode == $id_target}checked{/if}   />
								<label for="{$id_node}_{$targetNode.idnode}" class="icon folder">{$targetNode.path}</label>
							</div>					
						{/foreach}
					</div>								
				{else}
					<div class="info-message message">
						<div>{t}There aren't any available destination{/t}.</div>
					</div>
				{/if}

			<span class="slide-element">
				<input type="checkbox" id="{$id_node}_sharewf" name="sharewf" value="true" class="normal input-slide" {if $sharewf == 1}checked{/if} />
				<label class="label-slide" for="{$id_node}_sharewf">{t}Do you want to share the master document workflow?{/t}</label>
			</span>
			
			{if $id_target > 0}			
			</ol>
				<li>
					<input type="checkbox" name="delete_link" value="true" class="normal" />
					{t}Do you want to delete the document link?{/t}
				</li>
				<li>
					<div class="translation_box hidden">
						<input type="checkbox" name="delete_method" value="unlink" class="delete_method">
						{t}Do you want to copy the master document content when a link would be deleted?{/t} <br/>
						{*<input type="radio" name="delete_method" value="show_translation" class="delete_method">{t}Do you want to suggest Google Translate traduction?{/t}*}
					</div>
				</li>
			</ol>
			{/if}
			
				
		</fieldset>
	</div>

</form>
