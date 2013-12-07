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


		<form method="post" name="cg_form" id="cg_form" action="{$action_url}" class='validate_ajax'>
		<input type="hidden" name="id_node" value="{$id_node}" class=ecajag>
		<div class="action_header">
			<h2>{t}Add group{/t}</h2>
			<fieldset class="buttons-form">
					{button label="Create group" class='validate btn main_action' }<!--message="do you want to create the group?"-->
			</fieldset>
		</div>
		<div class="action_content">
		
								<div class="input icon group">
												<input type="text" name="name" id="groupname" class="cajag validable not_empty" placeholder="{t}Group's name{/t}">
										</div>		</div>

		</form>
