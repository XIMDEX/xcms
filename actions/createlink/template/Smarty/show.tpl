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

<form method="post" name="cln_form" id="cln_form" action="{$action_url}">
	<input type="hidden" name="id_node" value="{$id_node}" >
	<input type="hidden" name="name" value="{$name}" >
	<input type="hidden" name="url" value="{$url}" >
	<input type="hidden" name="description" value="{$description}" >
	<input type="hidden" name="validated" value="1" >

	<fieldset>
    
    	<legend><span>{t}Create link{/t}</span></legend>
    		<p>{t}Found links which point at same url:{/t} ({$url})</p>
		<ul class="list">
		{foreach from=$links item=link}
			<li><strong>{t}Name{/t}</strong>: {$link.name}</li>
			<li><strong>{t}Description{/t}</strong>: {$link.description}</li>
		{/foreach}
		</ul>

	</fieldset>

	<fieldset class="buttons-form">
		{button label="Cancel" class="close-button"}
		{button label="Continue" class='validate' }<!--message=" Do you want to create the link anyway?"-->
	</fieldset>
</form>
