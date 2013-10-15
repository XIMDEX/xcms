/**
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
 */


(function($) {
	
	var calendarOptions = {
		timeFormat : "HH:mm"
	};

	//On click over checkbox 
	$(".xim-calendar-layer-container input[type='checkbox']").click(function(){
		var $calendar = $(this).prev(".xim-calendar");
		if ($calendar.is(":disabled")){
			$calendar.removeAttr("disabled");			

		}else{
			$(this).prev(".xim-calendar").attr("disabled","disabled");
			$(this).prev(".xim-calendar").val("00-00-0000 00:00");
		}		
	});

	$('.datetimepicker').each(function(){			
		calendarOptions.dateFormat = $(this).attr("data-date-format");
		calendarOptions.minDateTime = new Date();
		
		//If is a from calendar, set the min date in "to-calendar" (if exists)
		if ($(this).hasClass("datetimepicker-from")){
		
			//Defining OnClose event
			calendarOptions.onClose = function( selectedDate ) {
				//Get Date object from selected Date
				var dateTime = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, selectedDate);
				//Get dateTimePiker html element
				
				var dateTimePickerTo = $( ".datetimepicker-to", $(this).parent.parent)[0]								

				//Set mindate only if the datepickerto is not disable, 
				if (!$(dateTimePickerTo).is("disabled")){
					var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-to", $(this).parent.parent).val());
					if (dateTime >= dateTimeToCalendar){
						var newDateTimeToCalendar = new Date(dateTime.getTime()+1000);
						$(dateTimePickerTo).datepicker("setDate", newDateTimeToCalendar);	
					}
				}

				//Update the timestamp for the input hide
				$(this).next("input[type='hidden']").val(dateTime.getTime());
			};
		
		//If is a to calendar, set the min date in "from-calendar" (if exists)
		}else if ($(this).hasClass("datetimepicker-to")){
			//Defining OnClose event
			calendarOptions.onClose = function( selectedDate ) {
				//Get Date object from selected Date
				var dateTime = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, selectedDate);
				$.datepicker._optionDatepicker($( ".datetimepicker-to", $(this).parent)[0],"maxDateTime",dateTime);
			};

			//Defining onSelect event
			calendarOptions.onSelect = function(dateTime){				

				//Date Time for To calendar
				var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, dateTime);
				var dateTimeFromCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-from").val());
				if (dateTimeFromCalendar >= dateTimeToCalendar){
					var newDateTimeToCalendar = new Date(dateTimeFromCalendar.getTime()+1000);
					$(this).datepicker("setDate", newDateTimeToCalendar);
				}
								
			};

			//Defining beforeShow event
			calendarOptions.beforeShow = function(input){

				//Date Time for To calendar
				var dateTime = $(input).val();
				var dateTimeFromCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-from").val());
				var newDateTimeToCalendar = new Date(dateTimeFromCalendar.getTime()+1000);
				if (dateTime.indexOf("00 00")>-1){
					$(this).datepicker("setDate", newDateTimeToCalendar);
				}else{
					var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, dateTime);				
					if (dateTimeFromCalendar >= dateTimeToCalendar){
						$(this).datepicker("setDate", newDateTimeToCalendar);							
					}	
				}
				
			}
		}
		$(this).datetimepicker(calendarOptions);
	});

	})(jQuery);;	