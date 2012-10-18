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

<tr><td align='center'>

	{t}Filter by user:{/t} 
	<select onChange="location.href=location.href+'&userid='+options[selectedIndex].value;" name="users" id="users">
			<option>{t}Select user{/t}</option>
		{foreach from = $users item = user}
	  		<option value="{$user.id}">{$user.name}</option>
		{/foreach}
	</select>

	<div class="tituloseccion" style="text-align:center;margin:10px">{$title}
		{if $selectedUser neq ''}
			<br>{t}User:{/t} {$selectedUser}
		{/if}	
	</div>

	<table class='tabla' cellpadding='3px' style='margin-left:2em'>
		<tr><td class='cabeceratabla'>{t}Action{t/}</td><td class='cabeceratabla'>{t}Method{/t}</td>
			<td class='cabeceratabla'>{$unit}</td><td class='cabeceratabla'>{t}Percent{/t}</td></tr>

		{foreach from = $html item = celda}
			<tr><td>{$celda.name}</td><td>{$celda.method}</td><td>{$celda.total}</td><td>{$celda.percent} %</td></tr>
		{/foreach}
	</table>

</td></tr>
