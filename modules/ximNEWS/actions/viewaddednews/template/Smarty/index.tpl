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
<legend><span>{t}Associated news{/t}</span></legend>
{if count($news) > 0}

	<table>
		<tr>
			<th>{t}News name{/t}</th>
			<th>{t}State{/t}</th>
			<th>{t}Publication date{/t}</th>
			<th>{t}Expiration date{/t}</th>
			<th>{t}Version{/t}</th>
		</tr>
		{foreach from=$news key=id_new item=new}
			<tr>
				<td>{$new.Name}</td>
				<td>{$new.State}</td>
				<td>{$new.FechaIn}</td>
				<td>{$new.FechaOut}</td>
				<td>{$new.Version}</td>
			</tr>
		{/foreach}
	</table>
{else}

	<p>{t}The colector has not associated news{/t}</p>
	
	
{/if}
</fieldset>
