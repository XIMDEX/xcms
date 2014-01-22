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
		timeFormat : "HH:mm",
		showButtonPanel: true
	};
	
	var inputTypes = {
		interval: "interval"
	}

	$('.datetimepicker').each(function(){			
		calendarOptions.dateFormat = $(this).attr("data-date-format");		
		calendarOptions.minDateTime = new Date();
		calendarOptions.currentText = $(this).attr("data-goto-text");
		calendarOptions.defaultDate = $(this).attr("data-default-date");
		var inputTimestampName = $(this).attr("data-timestamp-input");
		calendarOptions.timestampInput = $(this).siblings("input[name='"+inputTimestampName+"']");
		calendarOptions.noDateSelected=false;
		if ($(this).attr("data-type") == inputTypes.interval){
			calendarOptions.inputSibling=$(this).parent().siblings(".js_date_container").children("input[data-type='"+inputTypes.interval+"']");
		}
			
		calendarOptions.changeSelectedDate = function(selectedDate, object){

			//Get dateTimePiker html element
			if (object.settings.noDateSelected){
				object.settings.noDateSelected = false;
				object.settings.timestampInput.val(object.settings.defaultDate);				
				$(object.input).parent().find(".js_date_text").text($(object.input).attr("data-goto-text"));
				$(object.input).parent().find(".js_time_text").text("--/--/----");
			}
			else{
				var dateTime = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, selectedDate);
				var dateExploded = selectedDate.split(" ");
				var dateText = dateExploded[0];
				var timeText = dateExploded[1];	
				object.settings.timestampInput.val(dateTime.getTime());
				$(object.input).parent().find(".js_date_text").text(dateText);
				$(object.input).parent().find(".js_time_text").text(timeText);
			}
			
			$(object.input).datepicker("hide");
		};
		
		//If is a from calendar, set the min date in "to-calendar" (if exists)
		if ($(this).hasClass("js_datetimepicker_from")){
		
			//Defining OnClose event
			calendarOptions.onClose = function( selectedDate, object ) {
				$(object.input).parent().addClass("js-calendar-hidden").removeClass("js-calendar-showed");
				object.settings.changeSelectedDate(selectedDate, object);
			};
			
			calendarOptions.onSelect = function( selectedDate, object ) {
				$(object.input).datepicker("hide");
			};
				//Get Date object from selected Date
				
//				var dateTimePickerTo = $( ".js_datetimepicker_to", $(this).parent.parent)[0]								
//
//				//Set mindate only if the datepickerto is not disable, 
//				if (!$(dateTimePickerTo).is("disabled")){
//					var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-to", $(this).parent.parent).val());
//					if (dateTime >= dateTimeToCalendar){
//						var newDateTimeToCalendar = new Date(dateTime.getTime()+1000);
//						$(dateTimePickerTo).datepicker("setDate", newDateTimeToCalendar);	
//					}
//				}
//
//				//Update the timestamp for the input hide
//				$(this).next("input[type='hidden']").val(dateTime.getTime());
			
//			calendarOptions.onClose = 
//				//Get Date object from selected Date
//				var dateTime = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, selectedDate);
//				//Get dateTimePiker html element
//				
//				var dateTimePickerTo = $( ".datetimepicker-to", $(this).parent.parent)[0]								
//
//				//Set mindate only if the datepickerto is not disable, 
//				if (!$(dateTimePickerTo).is("disabled")){
//					var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-to", $(this).parent.parent).val());
//					if (dateTime >= dateTimeToCalendar){
//						var newDateTimeToCalendar = new Date(dateTime.getTime()+1000);
//						$(dateTimePickerTo).datepicker("setDate", newDateTimeToCalendar);	
//					}
//				}
//
//				//Update the timestamp for the input hide
//				$(this).next("input[type='hidden']").val(dateTime.getTime());
//			};
		
		//If is a to calendar, set the min date in "from-calendar" (if exists)
		}else if ($(this).hasClass("js_datetimepicker_to")){
			
			//Defining onSelect event
			calendarOptions.onClose = function(selectedDate, object){
				object.settings.changeSelectedDate(selectedDate, object);
				$(object.input).parent().addClass("js-calendar-hidden").removeClass("js-calendar-showed");				
			};
			
			calendarOptions.onSelect = function(selectedDate, object){
				$(object.input).datepicker("hide");
			};
			
			
				//Date Time for To calendar
//				var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, dateTime);
//				var dateTimeFromCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-from").val());
//				if (dateTimeFromCalendar >= dateTimeToCalendar){
//					var newDateTimeToCalendar = new Date(dateTimeFromCalendar.getTime()+1000);
//					$(this).datepicker("setDate", newDateTimeToCalendar);
//				}
								
			

			//Defining beforeShow event
			calendarOptions.beforeShow = function(input){

				//Date Time for To calendar
//				var dateTime = $(input).val();
//				var dateTimeFromCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, $(".datetimepicker-from").val());
//				var newDateTimeToCalendar = new Date(dateTimeFromCalendar.getTime()+1000);
//				if (dateTime.indexOf("00 00")>-1){
//					$(this).datepicker("setDate", newDateTimeToCalendar);
//				}else{
//					var dateTimeToCalendar = $.datepicker.parseDateTime(calendarOptions.dateFormat,calendarOptions.timeFormat, dateTime);				
//					if (dateTimeFromCalendar >= dateTimeToCalendar){
//						$(this).datepicker("setDate", newDateTimeToCalendar);							
//					}	
//				}
				
			}
		}
		$.extend($.datepicker,  {
			_gotoToday: function(id){				
				var target = $(id);
				var inst = this._getInst(target[0]);
				var currentText = inst.settings.currentText;
				if (currentText == _("Now")){					
					$dp = inst.dpDiv;
					this._base_gotoToday(id);
					var now = new Date(parseInt(inst.settings.defaultDate));
					var tp_inst = this._get(inst, 'timepicker');
					if (tp_inst && tp_inst.timezone_select) {
						tp_inst.timezone_select.val(-now.getTimezoneOffset());
					}					
					
					this._setTime(inst, now);					
				}else{
					inst.settings.noDateSelected = true;
				}
				$('.ui-datepicker-today', $dp).click();
			}
		});
		$(this).datetimepicker(calendarOptions);
		$(this).closest(".js_date_container").click(function(){			
			if (!$(this).hasClass("js-calendar-showed")){
				$(".datetimepicker", $(this)).datetimepicker("show");				
				$(this).addClass("js-calendar-showed").removeClass("js-calendar-hidden");
			}
		})
	});

	})(jQuery);;	