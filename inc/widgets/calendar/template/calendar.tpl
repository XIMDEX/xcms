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

<div id="%=id%" class="xim-calendar-layer-container xim-calendar-%=type% hidden">
	<div class="cabeceratabla xim-calendar-titlecontainer">
	%=widget_label%
	</div>
	<div class="xim-calendar-datecontainer">
		<label for="xim-calendar-datefield">fecha</label>
		<input
			type="text"
			class="xim-calendar-datefield"
			value="%=datevalue%"
			maxlength="14"
			name="%=date_field_name%_%=type%"
			readonly
		/>
		<div class="xim-calendar-datepicker"></div>
	</div>
	<div class="xim-calendar-timecontainer">
		<label for="xim-calendar-hourfield">hora</label>
		<input
			type="text"
			class="xim-calendar-hourfield"
			value="%=hourvalue%"
			maxlength="2"
			name="%=hour_field_name%_%=type%"
		/>

		<input
			type="text"
			class="xim-calendar-minfield"
			value="%=minvalue%"
			maxlength="2"
			name="%=min_field_name%_%=type%"
		/>

		<input
			type="text"
			class="xim-calendar-secfield"
			value="%=secvalue%"
			maxlength="2"
			name="%=sec_field_name%_%=type%"
		/>
	</div>
	<div class="xim-calendar-buttoncontainer">
		<input
			type="button"
			class="xim-calendar-button ui-state-default"
			value=" %=label_button% "
		/>
		<input type="hidden" name="%=timestamp_name%" value="%=timestamp_value%" class="xim-calendar-date-%=type%" />
	</div>

	<input type="hidden" value="%=timestamp_current%" class="xim-calendar-timestamp-current" />
</div>