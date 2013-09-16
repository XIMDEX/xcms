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


		<form method="post" name="ml_form" id="ml_form" action="{$action_url}">
			<div class="action_header">
				<h2>{t}Modificar Idioma{/t}</h2>
				<fieldset class="buttons-form">
					{button label="Modify" class="validate btn main_action" }<!--message="Would you like to modify this language?"-->
				</fieldset>
			</div>
		<div class="action_content">
			<fieldset>

				<ol>
			           <li> <label class="aligned">{t}ISO name{/t}</label>
						{$iso_name}
					</li>
				<li><label for="name" class="aligned">{t}Language name{/t}</label>
						<input type="text" name='Name' id="name" value="{$name}" class='cajag validable not_empty'>
					</li>
				<li><label for="description" class="aligned">{t}Description{/t}</label>
						<input type="text" name='Description' id="description" value="{$description}" class='cajag validable not_empty'>
				</li>
				<li><label for="enabled" class="aligned">{t}Activated{/t}</label>
						<input type="checkbox" name='Enabled' id="enabled" value='1'{if $enabled == 1} checked="checked"{/if}>
					</li>
				</ol>
			            </fieldset>
		</div>

		</form>
