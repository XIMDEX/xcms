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


<form method="post" action="{$action_url}" id="channel_form" class='form_active validate_ajax'>
	<input type="hidden" name="id_node" value="{$id_node}">
	<fieldset>
        	<legend><span>{t}Add channel{/t}</span></legend>
		<ol>
                	<li><label for="name" class="aligned">{t}Name{/t}</label> &nbsp;<input type="text" name='name' id="channelname" class='cajag validable not_empty'></li>
			<li><label for="extension" class="aligned">{t}File extension{/t}</label>&nbsp;<input type="text" name='extension' id="extension" class='cajag validable not_empty'></li>
			<li><label for="description" class="aligned">{t}Description{/t}</label>&nbsp;<input type='text' name='description' id="description" class='cajag validable not_empty'></li>
                        <li><label for="rendermode" class="aligned">{t}Rendering in{/t}</label>&nbsp;
			<input type='radio' id="rendermode" name="rendermode" checked value='ximdex'>{t}Ximdex{/t}&nbsp;
			<input type='radio' id="rendermode" name="rendermode" value='client'>{t}Client{/t}</li>
              	</ol>
		<div class="extrainfo">{t}Ximdex renderizes documents at the <i>Ximdex</i> local server by default. If your website it's going to be dynamic, then select the <i>Client</i> rederized mode{/t}.</div>
	</fieldset>
	<fieldset class="buttons-form">
		{button label="Reset" class='form_reset' type='reset'}
		{button label="Create channel" class='validate' }<!--message=Would you like to add a channel?"-->
	</fieldset>
</form>
