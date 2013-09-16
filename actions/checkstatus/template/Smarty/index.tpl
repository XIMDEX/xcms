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
	<legend><span>{t}Status Report{/t}</span></legend>
	<p>{t}Below are listed all the relevant documents in your system, grouped by state. Only are shown the files that are modified in comparation with its last published version.{/t}</p>
	
	<table class="versions">
		
	{foreach from=$files key=state item=statenode}
				
		<tr class="state-info">
			<td colspan="3">
				<strong>{t}Documents in{/t} {$statesFull[{$state}].stateName}</strong> ({$statesFull[{$state}].count}
			{if $statesFull[{$state}].count > 1}
				{t}documents found{/t}) 
			{else}
				{t}document found{/t})
			{/if}	
			</td>
		</tr>
		<tr class="header">
			<td>{t}File{/t}</td>
			<td>{t}Version{/t}</td>
			<td>{t}Date{/t}</td>
		</tr>
		{foreach from=$statenode item=file}		
			<tr class="version-info">
				<td>{$file.Path}/<strong>{$file.Name}</strong></td>
				<td>{$file.Version}.{$file.SubVersion}</td>
				<td>{$file.Date} hrs.</td>
			</tr>
		{/foreach}
	{/foreach}

	</table>
</fieldset>
