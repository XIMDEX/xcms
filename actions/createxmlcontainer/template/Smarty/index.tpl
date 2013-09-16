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


<form method="post" id="cdx_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$idNode}"/>

	<div class="action_header">
		<h2>{t}Add new XML{/t}</h2>
		<fieldset class="buttons-form">
		{button label="Create" class='validate btn main_action' }<!--message="Do you wan to create the XML document?"-->
	</fieldset>
	</div>

	<div class="action_content"><fieldset>


			<ol>
				<li>
					<label for="name" class="aligned">{t}File name{/t}</label>
					<input type="text" name="name" id="docname" class="cajaxg validable not_empty"/>
				</li>
				<li>
					<label for="id_template" class="aligned">{t}Document type{/t}</label>
					<select name="id_template" id="templateid" class="cajaxg validable not_empty">
						<option value="">&laquo;{t}Select template{/t}&raquo;</option>
						{foreach from=$templates item=template}
							<option value="{$template.idTemplate}">{$template.Name}</option>
						{/foreach}
					</select>
				</li>
			</ol>
		</fieldset>

		{include file="`$_APP_ROOT`/actions/createxmlcontainer/template/Smarty/_ximdoc_languages.tpl"}</div>


</form>
