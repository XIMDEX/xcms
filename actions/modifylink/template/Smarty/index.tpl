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
<form method="post" action="{$action_url}" id="link_form">
	<div class="action_header">
		<h2>{t}Modify external link{/t}</h2>
		<fieldset class="buttons-form">
		{button label="Modify" class='validate  btn main_action' }<!--message="Would you like to modify this link?"-->
	</fieldset>
	</div>
	<div class="action_content">
		<fieldset>
			<ol>
				<li>
					<label for="name" class="aligned">{t}Name{/t}</label>
					<input type="text" name='Name' id="name" value='{$name}' class='validable not_empty'>
				</li>
				<li>
					<label for="url" class="aligned">{t}URL{/t}</label>
					<input type="text" name='Url' id="url" value='{$url}' class='validable not_empty is_url long'>
				</li>
				<li>
		                                <label class="aligned"><span>{t}Prefixes{/t}</span></label>
		                                <input type="checkbox" name="urlprefix" id="urlprefix" class="">
		                                <label for="prefix" class=""><span>{t}Add{/t} <i>http://</i></span></label>
		                                <input type="checkbox" name="mailprefix" id="mailprefix" class="">
		                                <label for="mailprefix" class=""><span>{t}Add{/t} <i>mailto:</i></span></label>
		                        </li>
				<li>
					<label for="description" class="aligned">{t}Description{/t}</label>
					<input type="text" name='Description' id="description" value="{$description}" class="validable not_empty long">
				</li>
			</ol>
		</fieldset>
	</div>

</form>
