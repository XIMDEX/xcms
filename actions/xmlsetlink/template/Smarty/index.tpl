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
	<fieldset>
		<legend><span>{t}Select master document{/t}</span></legend>

			<p class="left"><label>{t}Select the master document{/t}</label></p>
				<treeview class="xim-treeview-selector" paginatorShow="yes" />


			<ol><li>
				<label for="pathfield" class="aligned">{t}Link path{/t}</label>
				<input type="text" readonly="readonly" id="pathfield" name="pathfield" value="{$target_node_path}"  class="cajaxg validable not_empty long" style="width:420" >
				<input type="hidden" id="contenttype" name="contenttype" value="{$type}">
				<input type="hidden" id="targetfield" name="targetfield" value="{$id_target}">
			</li>
			<li>
				<input type="checkbox" name="sharewf" value="true" class="normal" {if $sharewf == 1}checked{/if} />
				{t}Do you want to share the master document workflow?{/t}
			</li>
		{if $id_target > 0}
			<li>
				<input type="checkbox" name="delete_link" value="true" class="normal" />
				{t}Do you want to delete the document link?{/t}
			</li>
			<li>
				<div class="translation_box hidden">
					<input type="checkbox" name="delete_method" value="unlink" class="delete_method">
					{t}Do you want to copy the master document content when a link would be deleted?{/t} <br/>
					<!--<input type="radio" name="delete_method" value="show_translation" class="delete_method">
					{t}Do you want to suggest Google Translate traduction?{/t}-->
				</div>
			</li>
		{/if}
		</ol>
			<tr class="link_info">
				<td bgcolor="#FAFAFA" nowrap>
				</td>
			</tr>
	</fieldset>
	<fieldset class="buttons-form">
		<ol>
			<li>
				{button class="validate" onclick="" label="Save changes" }<!--message="Are you sure you want to performe the changes?"-->
			</li>
		</ol>
	</fieldset>
</form>
