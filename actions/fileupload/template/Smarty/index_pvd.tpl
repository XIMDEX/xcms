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

<form enctype="multipart/form-data" method="post" id="up_form" name="up_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$id_node}"/>
	<div class="action_header">
		<h2>{t}Upload file{/t}</h2>
    		<fieldset class="buttons-form">
			{button label="Upload file" class="validate btn main_action" }{*message="Are you sure you want to upload this pvd?"*}
		</fieldset>
    	</div>

	<div class="action_content">
		<fieldset>
			<ol>
			<li>
				<label class="aligned">{t}Template view{/t}</label>
				<input type="file" name="template" id="template" size="30" class="cajaxg validable not_empty">
			</li>
			<li>
				<label class="aligned">{t}Default view{/t}</label>
				<input type="file" name="content"  id="content" size="30" class="cajaxg">
			</li>
		{if ($module_ximnews == true)}
			<li>
				<input type="radio" name="template_type" value="generic_template" checked="checked">
				<label>{t}Generic use template{/t}</label>
			</li>
			<li>
				<input type="radio" name="template_type" value="news_template">
				<label>{t}XimNEWS news template{/t}</label>
			</li>
			<li>
				<input type="radio" name="template_type" value="bulletin_template">
				<label>{t}XimNEWS newsletter template{/t}</label>
			</li>
		{/if}
		</ol>
	</fieldset>
</div>
</form>
