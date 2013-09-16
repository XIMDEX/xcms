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

<form method="post" id="print_form" action="{$action_url}">
	<div class="action_header">
    		<h2>{t}Create a new file{/t}</h2>
    		<fieldset class="buttons-form">
        		{button label="Create" class='validate btn main_action' }
    		</fieldset>
  	</div>
	<div class="action_content">
      		<fieldset>
          		<input type="hidden" name="nodeid" value="{$nodeID}">
        		<p><label for="foldername" class="aligned">{t}Name{/t}</label><input type="text" name="name" id="foldername" class="cajaxg validable not_empty"></p>
			{if $countChilds > 1}
      			<p><label class="aligned">{t}File type{/t}</label>
                    			<select name="nodetype" class="caja validable not_empty">
                          			{foreach from=$childs item=child}
                            				<option value="{$child.idnodetype}">{$child.nodetypename}</option>
                          			{/foreach}
                          		</select>
      			</p>
                  	{else}
                       		<input name="nodetype" type="hidden" value="{$childs[0].idnodetype}" />
                  	{/if}
			{t}<p>The <strong>file extension</strong> is not needed.</p>{/t}
       		</fieldset>
    	</div>
</form>

