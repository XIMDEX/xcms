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

<form name="copy" id="copy" method="post" action="{$action_url}">
<fieldset>
<legend><span>{t}Here should be the action name{/t}</span></legend>
<input type="hidden" id="nodeid" name="nodeid" value="{$id_node}">
<input type="hidden" name="nodetypeid" value="{$nodetypeid}">
<input type="hidden" name="filtertype" value="{$filtertype}">
<input type="hidden" name="targetid" id="targetid">
<input type="hidden" id="editor">
	<ol>
		<li>
			<label for="id_node"><span>{t}Choose destination{/t}</span></label>
		<div class="right-block">
			<treeview class="xim-treeview-selector"	paginatorShow="yes" /></div>
		</li>
		<!--<li>
			<input type="text" readonly name="pathfield" id="pathfield" value="" >
			<input type="hidden" name="targetfield" id="targetfield" /> 
			<input type="hidden" name="contenttype" id="contenttype" value="{$nodetypeid}" /> 
		</li>-->
		<li>
			<input type="checkbox" name="recursive" id="recursive" checked="checked" /><label for="recursive"> <span>{t}Recursive process{/t}</span></label>
		</li>
		
	</ol>	
    </fieldset>
   <fieldset class="buttons-form">
   
			{button class="validate" label="{t}Copy{/t}" }<!--message="Are you sure you want to copy the node to selected destination?"-->
		
        </fieldset>
</form>
