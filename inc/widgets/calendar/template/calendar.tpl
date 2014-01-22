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

<div id="%=id%" class="xim-calendar-layer-container xim-calendar-%=type%">	
	{if '%=type%' eq 'interval'}

		<span class="js_date_container">
			<span class="">%=first_date_label%</span>
			<span class="js_date_content">
				<span class="js_date_text">%=first_date_text%</span>
				<span class="js_time_text">%=first_time_text%</span>
			</span>
			<input type="hidden" name="%=first_date_name%_timestamp" class="" value="%=server_timestamp%" />
			<input 
				type="input" 
				name="%=first_date_name%" 
				data-date-format="%=date_format_display%"
				data-timestamp-input="%=first_date_name%_timestamp" 
				data-goto-text ="%=first_now_text%"
				data-type="%=type%"
				data-default-date="%=server_timestamp%"
				class="datetimepicker js_datetimepicker_from"
				/>
		</span>

		<span class="js_date_container">
			<span class="">%=last_date_label%</span>
			<span class="js_date_content">
				<span class="js_date_text">%=last_date_text%</span>
				<span class="js_time_text">%=last_time_text%</span>
			</span>
			<input type="hidden" name="%=last_date_name%_timestamp" class="" value="0"/>
			<input type="input" 
				   name="%=last_date_name%" 
				   data-date-format="%=date_format_display%" 
				   data-timestamp-input="%=last_date_name%_timestamp"
				   data-goto-text ="%=last_now_text%"
				   data-type="%=type%"
				   data-default-date="0"
				   class="datetimepicker js_datetimepicker_to" 
				   />
		</span>
	
	{/if}
</div>

