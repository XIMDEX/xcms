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


	<form method="post" name="as_form" id="as_form" action="{$action_url}">
		<input type="hidden" name="nodeid" VALUE="{$nodeID}">
		<input type="hidden" id="nodeURL" name="nodeURL" value="{$nodeURL}">
		<div class="action_header">
			<h2>{t}Add section{/t}</h2>
			<fieldset class="buttons-form">
				{button label="Create section" class='validate btn main_action' }<!--message="Would you like to add this section?"-->
			</fieldset>
		</div>

		<div class="action_content section-properties">
			
			
			<div class="folder-name folder-normal icon input-select">
				<input type="text" name="name" id="name" maxlength="100" class="cajaxg validable not_empty full-size" placeholder="{t}Name of your section{/t}">
				{if $sectionTypeCount > 1}
				<select id="type_sec" name="nodetype" class="caja validable not_empty folder-type">
					{foreach from=$sectionTypeOptions item=sectionTypeOption}
					<option {if ($sectionTypeOption.id == $selectedsectionType)} selected{/if} value="{$sectionTypeOption.id}">{$sectionTypeOption.name}</option>
					{/foreach}
				</select>
				{else}
				<input name="nodetype" type="hidden" value="{$sectionTypeOptions.id}" />
				{/if}
			</div>

			<div class="languages-available col1-3 right">{if $languageCount neq 0}
				<h3>{t}Languages availables{/t}</h3>	
				{foreach from=$languageOptions item=languageOption}
			
				<div class="languages-section">
                                        <input name="langidlst[]" type='checkbox' value="{$languageOption.IdLanguage}" class="hidden-focus" id="{$languageOption.IdLanguage}">
                                        <label for="{$languageOption.IdLanguage}" class="icon checkbox-label">
                                        {$languageOption.Name|gettext}</label>
                                        <input type="text" name="namelst[{$languageOption.IdLanguage}]" class="alternative-name" placeholder="{t}Alternative name for paths &amp; breadcrumbs{/t}">
                                </div>	
				
				{/foreach}
				
				{else}
				<p>{t}There are no languages associated to this project.{/t}</p>
				{/if}</div>

				<div class="subfolders-available col2-3"><h3>{t}Subfolders availables{/t}</h3>
				{if $subfolders|@count != 0}
					{foreach from=$subfolders key=nt item=foldername}

					<div class="subfolder box-col1-1">
						<input name="folderlst[]" type="checkbox" value="{$nt}" {if $nt eq 5018 || $nt eq 5016 || $nt eq 5022 || $nt eq 5301 || $nt eq 5304} checked{/if} {if $nt eq 5301 || $nt eq 5304} readonly {/if} class="hidden-focus" id="{$nt}"/>
                                                <label class="icon" for="{$nt}"><strong class="icon {$foldername[0]}">{$foldername[0]}</strong>
                                                </label>
                                                <span class="info">{t}{$foldername[1]}{/t}</span>
					</div>


					{/foreach}
				{else}
					<p>{t}There aren't any avaliable subfolders for this section.{/t}</p>
				{/if}
				</div>
				</div>


			</form>
