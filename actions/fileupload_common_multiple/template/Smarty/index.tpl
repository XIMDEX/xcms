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

<form action="{$action_url}"  method="POST" name="f_m_u" enctype="multipart/form-data" >
	<div class="action_header">
		<h2>{$lbl_anadir}</h2>
		<fieldset class="buttons-form">
			{button label="Upload files" class="validate btn main_action"}
			<!--message="Are you sure you want to continue?" -->
		</fieldset>
	</div>

	<div class="action_content uploader {if $type_node == "XmlContainer"}xml-uploader{/if}">
		<fieldset>
	{* for the XML massive upload *} 
	{if $type_node == "XmlContainer"}
			<div class="xml-properties">
			<h3>{t}Please, select the schema to follow and the languages for your documents before uploading them.{/t}</h3>	
			<div class="col1-2">
				
					<select name="id_schema" id="schemaid" class="cajaxg validable not_empty extra-param">
						<option value="">&laquo;{t}Select schema{/t}&raquo;</option>
					{foreach from=$schemas item=schema}
						<option value="{$schema.idSchema}">{$schema.Name}</option>
					{/foreach}
					</select>
				</div>
				<div class="col1-2">
					<select name="id_language" id="id_language" class="cajaxg validable not_empty extra-param">
						<option value="">&laquo;{t}Select language{/t}&raquo;</option>
					{foreach from=$languages item=language}
						<option value="{$language.IdLanguage}">{$language.Name|gettext}</option>		
					{/foreach}	
					</select>
				</div>
	</div>
	{/if}
			<uploader {if ($filter)}filter="{$filter}"{/if} is_structured="{$is_structured}"/>
		</fieldset>
	</div>
</form>
