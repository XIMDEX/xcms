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

<fieldset>
<legend><span>{t}Descargar archivo{/t}</span></legend>
<p>
{t}File download will start immediately. If it does not start, click on:{/t}
<a href="{$_URL_ROOT}/xmd/loadaction.php?action=filemapper&nodeid={$id_node}"
	class="destacada download_link"
	title="{t nodename=$node_name}Descargar %1{/t}"> {$node_name}</a>
</p>
</fieldset>

<fieldset class="buttons-form">
	{button label="Download" class="button-download"}
</fieldset>
