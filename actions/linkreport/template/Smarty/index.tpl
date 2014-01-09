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

<form method="post" name="ca_form" id="ca_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$id_node}">
	<div class="action_header">
		<h2>{t}Select search criteria{/t}</h2>
		<fieldset class="buttons-form">
			{button label="Search" class="validate btn main_action" }{*message="A search will be performed with selected parameters. Would you like to continue?"*}
		</fieldset>
	</div>
		<div class="action_content">
			<fieldset>
				<ol>
					<li>
						<select name='field' class="xim-filter-field validable">
							<option value="Name">{t}Name{/t}</option>
							<option value="Description">{t}Description{/t}</option>
							<option value="Url">{t}URL{/t}</option>
						</select>

						<select name='criteria' class="xim-filter-comparation validable">
							<option value="contains">{t}contains{/t}</option>
							<option value="nocontains">{t}does not contain{/t}</option>
							<option value="equal">{t}equal to{/t}</option>
							<option value="nonequal">{t}not equal to{/t}</option>
							<option value="startswith">{t}begins with{/t}</option>
							<option value="endswith">{t}ends with{/t}</option>
						</select>

						<input type="text" name="stringsearch" class="xim-filter-content validable long" />
					</li>
					<li>
						<input type="checkbox" name="rec" class="validable" id="rec"/> <label for="rec">{t}Search links in subcategories{/t}</label>

					</li>
					<li>
						<input type="checkbox" name="all" class="validable" id="all"/> <label for="all">{t}Show also correct links{/t}</label>
					</li>
				</ol>
			</fieldset>
		</div>

</form>
