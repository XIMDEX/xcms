{**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

{if isset($properties.Metadata) and count($properties.Metadata) > 0}
	{assign var="metadatas" value=$properties.Metadata}
	<div class="row tarjeta propertyform">
		<h2 class="h2_general">{t}Metadata schemes{/t}</h2>
		<div class="manageproperties">
			{if ($inProject eq false)}
				<div>
					<input type="radio" name="inherited_metadata" class="metadata_inherited" value="inherited" 
                            id="metadata_inherited_{$id_node}" {if $Metadata_inherited == 'inherited'}checked="checked"{/if} />
					<label for="metadata_inherited_{$id_node}">{t}Use inherited metadata schemes{/t}:</label>
					<ul class="inheritlist">
						{foreach from=$metadatas item=metadata}
							{if $metadata.Inherited}
								<li>{$metadata.Name}</li>
							{/if}
						{/foreach}
					</ul>
				</div>
				<div class="sep">
					<input type="radio" name="inherited_metadata" class="metadata_overwritten" value="overwrite" 
                            id="metadata_overwritten_{$id_node}" {if $Metadata_inherited == 'overwrite'}checked="checked"{/if} />
					<label for="metadata_overwritten_{$id_node}">{t}Overwrite inherited metadata schemes{/t}:</label>
				</div>
			{else}
				<input type="hidden" name="inherited_metadata" value="overwrite" />
				<label>{t}Available project metadata schemes{/t}:</label>
			{/if}
            {if ($metadatas)}
				<div class="overwrited_properties">
					{foreach from=$metadatas item=metadata}
						<span name="check_metadata" class="slide-element metadatamp{if $Metadata_inherited == 'inherited'} disabled{/if}">
							<input type="checkbox" class="metadata input-slide" name="metadata[]" value="{$metadata.Id}" 
									{if $metadata.Checked == 1}checked="checked"{/if} id="metadata_{$metadata.Name}_{$id_node}" />
							<label for="metadata_{$metadata.Name}_{$id_node}" class="label-slide">{$metadata.Name}</label>
						</span>
					{/foreach}
				</div>
			{/if}
			<hr />
			<div>
				<input type="checkbox" class="metadata_recursive" name="metadata_recursive" id="metadata_recursive_{$id_node}" value="1" />
				<label for="metadata_recursive_{$id_node}">{t}Associate metadata schemes recursively{/t}</label>
			</div>
		</div>
	</div>
{/if}
