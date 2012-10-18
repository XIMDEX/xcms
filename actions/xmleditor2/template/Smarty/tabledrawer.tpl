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

<div id="kupu-tabledrawer"
	class="kupu-drawer kupu-tabledrawer">
	<h1 i18n:translate="tabledrawer_title">{t}Tables{/t}</h1>
	<div class="kupu-panels">
		<table>
			<tr class="kupu-panelsrow">
				<td class="kupu-panel">
					<div
						class="kupu-tabledrawer-addtable">
						<table>
							<tr>
								<th
									i18n:translate="tabledrawer_rows_label"
									class="kupu-toolbox-label">
									{t}Rows{/t}
								</th>
								<td>
									<input type="text"
										class="kupu-tabledrawer-newrows"
										onkeypress="return HandleDrawerEnter(event);" />
								</td>
							</tr>
							<tr>
								<th
									i18n:translate="tabledrawer_columns_label"
									class="kupu-toolbox-label">
									{t}Cols{/t}
								</th>
								<td>
									<input type="text"
										class="kupu-tabledrawer-newcols"
										onkeypress="return HandleDrawerEnter(event);" />
								</td>
							</tr>
							<tr>
								<th
									class="kupu-toolbox-label">
								</th>
								<td>
									
								</td>
							</tr>
						</table>
					</div>
					<div
						class="kupu-tabledrawer-edittable">
						<table>
							<tr>
								<th
									class="kupu-toolbox-label"
									i18n:translate="tabledrawer_class_label">
									{t}Table class{/t}
								</th>
								<td>
									<select
										class="kupu-tabledrawer-editclasschooser"
										onchange="ximdocdrawertool.current_drawer.setTableClass(this.options[this.selectedIndex].value)">
										<option
											i18n:translate="" value="plain">
											{t}Plain{/t}
										</option>
										<option
											i18n:translate="" value="listing">
											{t}List{/t}
										</option>
										<option
											i18n:translate="" value="grid">
											{t}Grid{/t}
										</option>
										<option
											i18n:translate="" value="data">
											{t}Data{/t}
										</option>
									</select>
								</td>
							</tr>
							<tr>
								<th
									class="kupu-toolbox-label"
									i18n:translate="tabledrawer_alignment_label">
									{t}Current column align{/t}
								</th>
								<td>
									<select
										id="kupu-tabledrawer-alignchooser"
										class="kupu-tabledrawer-alignchooser"
										onchange="ximdocdrawertool.current_drawer.tool.setColumnAlign(this.options[this.selectedIndex].value)">
										<option
											i18n:translate="tabledrawer_left_option" value="left">
											{t}Left{/t}
										</option>
										<option
											i18n:translate="tabledrawer_center_option"
											value="center">
											{t}Center{/t}
										</option>
										<option
											i18n:translate="tabledrawer_right_option" value="right">
											{t}Right{/t}
										</option>
									</select>
								</td>
							</tr>
							<tr>
								<th
									class="kupu-toolbox-label"
									i18n:translate="tabledrawer_column_label">
									{t}Col{/t}
								</th>
								<td>
									<button
										class="kupu-dialog-button" type="button"
										i18n:translate="tabledrawer_add_button"
										onclick="ximdocdrawertool.current_drawer.addTableColumn()">
										{t}Add{/t}
									</button>
									<button
										class="kupu-dialog-button" type="button"
										i18n:translate="tabledrawer_remove_button"
										onclick="ximdocdrawertool.current_drawer.delTableColumn()">
										{t}Remove{/t}
									</button>
								</td>
							</tr>
							<tr>
								<th
									class="kupu-toolbox-label"
									i18n:translate="tabledrawer_row_label">
									{t}Row{/t}
								</th>
								<td>
									<button
										class="kupu-dialog-button" type="button"
										i18n:translate="tabledrawer_add_button"
										onclick="ximdocdrawertool.current_drawer.addTableRow()">
										{t}Add{/t}
									</button>
									<button
										class="kupu-dialog-button" type="button"
										i18n:translate="tabledrawer_remove_button"
										onclick="ximdocdrawertool.current_drawer.delTableRow()">
										{t}Remove{/t}
									</button>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<div class="kupu-dialogbuttons">
		<button
										class="kupu-dialog-button" type="button"
										i18n:translate="tabledrawer_add_table_button"
										onclick="ximdocdrawertool.current_drawer.createTable()">
										{t}Add table{/t}
									</button>
			<button class="kupu-dialog-button" type="button"
				onfocus="window.status='focus';"
				onmousedown="window.status ='onmousedown';"
				i18n:translate="tabledrawer_close_button"
				onclick="ximdocdrawertool.closeDrawer(this)">
				{t}Close{/t}
			</button>
		</div>
	</div>
</div>