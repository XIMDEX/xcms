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

X.actionLoaded(function(event, fn, params) {
	(function ($) {

		var calendarOptions = {
			timeFormat: "HH:mm",
			showButtonPanel: true
		};

		var inputTypes = {
			interval: "interval"
		}

		$('.datetimepicker').each(function () {
			calendarOptions.dateFormat = $(this).attr("data-date-format");
			calendarOptions.minDateTime = new Date();
			calendarOptions.currentText = $(this).attr("data-goto-text");
			calendarOptions.defaultDate = $(this).attr("data-default-date");
			var inputTimestampName = $(this).attr("data-timestamp-input");
			calendarOptions.timestampInput = $(this).siblings("input[name='" + inputTimestampName + "']");
			calendarOptions.noDateSelected = false;
			if ($(this).attr("data-type") == inputTypes.interval) {
				calendarOptions.inputSibling = $(this).parent().siblings(".js_date_container").children("input[data-type='" + inputTypes.interval + "']");
			}

			//If is a from calendar, set the min date in "to-calendar" (if exists)
			if ($(this).hasClass("js_datetimepicker_from")) {

				//Defining OnClose event
				calendarOptions.onClose = function (selectedDate, object) {
					$(object.input).parent().addClass("js-calendar-hidden").removeClass("js-calendar-showed");
				};

				//If is a to calendar, set the min date in "from-calendar" (if exists)
			} else if ($(this).hasClass("js_datetimepicker_to")) {

				//Defining onSelect event
				calendarOptions.onClose = function (selectedDate, object) {
					$(object.input).parent().addClass("js-calendar-hidden").removeClass("js-calendar-showed");
				};
				calendarOptions.onSelect = function (selectedDate, object) {
					object.settings.noDateSelected = false;
				}

			}
			$.extend($.datepicker, {
				_gotoToday: function (id) {
					var target = $(id);
					var inst = this._getInst(target[0]);
					var currentText = inst.settings.currentText;
					$dp = inst.dpDiv;
					if (currentText == _("Now")) {
						this._base_gotoToday(id);
						var now = new Date(parseInt(inst.settings.defaultDate));
						var tp_inst = this._get(inst, 'timepicker');
						if (tp_inst && tp_inst.timezone_select) {
							tp_inst.timezone_select.val(-now.getTimezoneOffset());
						}

						this._setTime(inst, now);
						$('.ui-datepicker-today', $dp).click();
					} else {
						inst.settings.noDateSelected = true;
						$(inst.input).trigger("change");
					}
				},
			});

			$(this).datetimepicker(calendarOptions);
			$(this).change(function () {

				var object = $.datepicker._getInst(this);
				var selectedDate = $(this).val();
				//Get dateTimePiker html element
				if (object.settings.noDateSelected) {
					object.settings.timestampInput.val(object.settings.defaultDate);
					$(object.input).parent().find(".js_date_text").text($(object.input).attr("data-goto-text"));
					$(object.input).parent().find(".js_time_text").text("--/--/----");
				}
				else {
					var dateTime = $.datepicker.parseDateTime(calendarOptions.dateFormat, calendarOptions.timeFormat, selectedDate);
					var dateExploded = selectedDate.split(" ");
					var dateText = dateExploded[0];
					var timeText = dateExploded[1];
					object.settings.timestampInput.val(dateTime.getTime());
					$(object.input).parent().find(".js_date_text").text(dateText);
					$(object.input).parent().find(".js_time_text").text(timeText);
				}

				return false;
			});

			$(this).closest(".js_date_container").click(function () {
				if (!$(this).hasClass("js-calendar-showed")) {
					$(".datetimepicker", $(this)).datetimepicker("show");
					$(this).addClass("js-calendar-showed").removeClass("js-calendar-hidden");
				}
			})
		});

	})(jQuery);
});
