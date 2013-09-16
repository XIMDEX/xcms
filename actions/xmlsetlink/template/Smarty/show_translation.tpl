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
<h2>{t}Node{/t}&raquo;&nbsp;{$ruta}</h2>
<form id="et_form" class="et_form" enctype="multipart/form-data" method="post" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$id_node}">
	<input type="hidden" id="publicar" name="publicar" value="0">
	<fieldset>
		<ol>
			<li>
				<textarea class="editor" name="editor" id="editor" wrap="soft" rows=30 cols=100 tabindex="7">
				</textarea>
			</li>
			<li>
				<input type="checkbox" name="keepcontent" id="keepcontent" value="true" class="normal"/>{t}Keep original content?{/t} <br/>
			</li>
		</ol>
	</fieldset>	
	<fieldset>
		<ol>
			<li>
				{button label="Delete link" onclick="redirect();" class="validate btn main_action"}
			</li>
		</ol>
	</fieldset>	
</form>
