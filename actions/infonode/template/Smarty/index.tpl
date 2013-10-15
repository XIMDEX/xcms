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
	<h2>{t}Information about{/t}: <strong>{$info.name}</strong></h2>
</div>

<div class="action_content">
	<ul class="info-node">
		<div class="col1-2 main-color general-info">
			<h3>{t} General info {/t}</h3>
			<li class="box-col2-1">
				<div class="box-content"><strong>{t}NodeId{/t}</strong> {$info.nodeid}</div>
			</li>
			<li class="box-col2-1">
				<div class="box-content"><strong>{t}Parent node{/t}</strong> {$info.parent}</div>
			</li>
			<li class="box-col4-1">
				<div class="box-content"><strong>{t}NodeType{/t}</strong> {$info.typename}<span class="nodetype"> ({$info.type}) </span><span class="path">{$info.path|replace:"/Ximdex/Projects":""}</span></div>
			</li>
		</div>

		<div class="col1-2 grey-color properties-info">
			<h3>{t} Properties info {/t}</h3>
			<li class="box-col4-1">
				<div class="box-content"><strong>{t}Languages{/t}</strong>
	{if ($languages)}
			{section name=i loop=$languages}
						{$languages[i].Name} ( {$languages[i].Id} )
				{if (!$smarty.section.i.last)},{/if}
				{if (0 == $smarty.section.i.index_next%4  )}<br />{/if}
			{/section}
		
	{else}
		{t}Not found{/t}
	{/if}			</div>
			</li>
			<li class="box-col4-1">
				<div class="box-content"><strong>{t}Channels{/t}</strong>
		{section name=i loop=$channels}
					{$channels[i].Name} ({$channels[i].IdChannel})
			{if (!$smarty.section.i.last)},{/if}
			{if (0 == $smarty.section.i.index_next%4  )}<br />{/if}
		{/section}	</div>
			</li>
		</div>

	{if isset($info.date)}
		<div class="col1-1 grey-color workflow-info">
			<h3>{t} Workflow info {/t}</h3>
		{if ($info.date)}
			<li class="box-col2-1">
				<div class="box-content"><strong>{t}State{/t}</strong><span class="state">{if ($info.published)}{t}Published{/t}{else}{t}Not published{/t}{/if}</span></strong></div>
			</li>
			<li class="box-col2-1">
				<div class="box-content"><strong>{t}Last version{/t}</strong> {$info.version}.{$info.subversion}</div>
			</li>
			<li class="box-col6-1">
				<div class="box-content"><strong>{t}Last modified{/t}</strong> <span class="date">{$info.date|date_format:"%d/%m/%y %H:%S"}</span><span class="user">{$info.lastusername} ({$info.lastuser})</span></div>
			</li>
		{/if}
		</div>	
	{/if}
		
	</ul>
</div>
