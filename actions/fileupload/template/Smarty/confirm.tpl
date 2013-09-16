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

<form id="void" action="{$action}" method="post">
	<input type="hidden" name="tmp_file" value="{$tmp_file}" />			
	<input type="hidden" name="tmp_name" value="{$tmp_name}" />			
	<input type="hidden" name="id_node" value="{$id_node}" />
	<input type="hidden" name="node_type_name" value="{$node_type_name}" />
	<fieldset><legend><span>{t}Messages{/t}</span></legend>
	{include file="`$_APP_ROOT`/xmd/template/Smarty/helper/messages.tpl"}
	</fieldset>
	<fieldset class="buttons-form">
		{button label="Go back" class="focus btn" onclick="$(this).closest('.ui-tabs').tabs('backToIndex', this);"}
		{button label="Confirm"  class="validate  btn main_action"}			<!--message="Are you sure you want to replace this file?"-->
	</fieldset>
</form>
