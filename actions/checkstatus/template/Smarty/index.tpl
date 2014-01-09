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

 <div class="action_header">
	<h2>{t}Status Report{/t}</h2>
</div>
<div class="action_content versions">
	<p>{t}Below are listed all the relevant documents in your system, grouped by state. Only are shown the files that are modified in comparation with its last published version.{/t}</p>
		
	{foreach from=$files key=state item=statenode}
		<div class="state-info row-item_selectable">
			<span class="state">{t}Documents in{/t} {$statesFull[{$state}].stateName}</span>  <div class="docs-amount right">{$statesFull[{$state}].count}</div>
		
		<div class="documents-info">
			{foreach from=$statenode item=file}		
				<div class="version-info">
					<span class="file-path">{$file.Path}/<strong>{$file.Name}</strong></span>
					<span class="file-date">{$file.Date} hrs.</span>
					<span class="file-version">{$file.Version}.{$file.SubVersion}</span>
				</div>
			{/foreach}
		</div>
	{/foreach}
</div>
</div>
