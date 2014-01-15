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

{if (null != $files_nok.name)}
<div class="action_header">
<h2>{t}Not added files{/t}</h2>
	
	</div>
	<div class="action_content">
<fieldset>


	 <ul class="files_nok">
		  {section name=i loop=$files_nok.name}
		  <li><strong>{$files_nok.name[i]}:</strong> {$files_nok.msg[i]}</li>
		  {/section}
	 </ul>
	 
</fieldset>
</div>
{/if}

{if (null != $files_ok.name)}
<div class="action_header">
<h2>{t}Added files{/t}</h2>
	
	</div>
	<div class="action_content">

<fieldset>
	 <ul class="files_ok">
		{section name=i loop=$files_ok.name}
		<li><strong>{$files_ok.name[i]}:</strong> {$files_ok.msg[i]}</li>
		{/section}
	 </ul>
</fieldset>
</div>
{/if}