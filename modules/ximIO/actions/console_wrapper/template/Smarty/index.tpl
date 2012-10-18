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

<tr>
	<td>
		<form action="{$action}" method="get" id="console_wrapper">
			<input type="hidden" name="nodeid" value=""/>
			<input type="hidden" name="actionid" value="{$id_action}"/>
			
			<label for="method">{t}Select operation mode{/t}:</label>
			<select name="method" id="method">
				<option value="export">{t}Export a new package{/t}</option>
				<option value="import">{t}Import a package in current instance{/t}</option>
				<option value="file_import">{t}Consolidate previous imports{/t}</option>
			</select>
			{button label="Next" class='validate'}
		</form>
	</td>
</tr>