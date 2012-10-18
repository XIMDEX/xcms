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

	var arrValidity = {};
	$(params.context).data('validity', arrValidity);

	fn('.check_group_nodes').change(function(event) {

		var arrValidity = $(params.context).data('validity');
		var key_id = fn(this).val();

		var $container = fn('.xim-calendar-container', fn(this).closest('li'));
		fn($container).hide();

		if (fn(this).attr('checked')) {


			if(0 == fn(".xim-calendar-layer-container", $container).length) {
				fn(this).attr("disabled", true);
				var calendar = new X.WidgetsManager("calendar", $container);
				/* From WidgetManager:
				* $(this.container).trigger("widgetLoaded", [{widget: this.widget, id: this.widget_id, type: this.type, params: this.params, element: this.element}]);
				*/
				$($container).bind("widgetLoaded", function(event, options) {
					var $container = fn('.xim-calendar-container', fn(this).closest('li'));
					if("from" == options.params.type) {
						//get second calendar
						var calendar = new X.WidgetsManager("calendar", $container);
						var label = $("label", $(this).closest("li") ).html();
						var id= $("input:first", $(this).closest("li") ).val();
						calendar.load({type: "to", calendar_from: options, rel: label,cname:"enddate["+id+"]"});
					}else {
						var key_id = fn(this).val();
						var arrValidity = $(params.context).data('validity');
						arrValidity[key_id] = new X.ValidityCalendar({
							calfrom: options.params.calendar_from.widget,
							calto: options.widget
						});
						$(params.context).data('validity', arrValidity);

						//already two calendars
						$($container).unbind("widgetLoaded");
						fn($container).show();
						fn(this).attr("disabled", false);
					}
				}.bind(this) );

				var calendar = new X.WidgetsManager("calendar", $container);
				var label = $("label", $(this).closest("li") ).html();
				calendar.load({type: "from", rel: label,cname:"startdate[]"});

			}else {
				widgets_calendar = fn(".xim-calendar-layer-container", $container);
				arrValidity[key_id] = new X.ValidityCalendar({
					calfrom: widgets_calendar[0],
					calto: widgets_calendar[1]
				});
				fn($container).show();
			}


		}else {
			delete arrValidity[key_id];
			$(params.context).data('validity', arrValidity);

		}



	/*	var arrValidity = $(params.context).data('validity');
		var key_id = fn(this).val();

		if (fn(this).attr('checked')) {

			fn(calendars).show().calendar({format: 'dd-mm-yy'});
			arrValidity[key_id] = new X.ValidityCalendar({
				calfrom: calendars[0],
				calto: calendars[1]
			});

		} else {
			delete arrValidity[key_id];
			fn(calendars).hide();
		}
		$(params.context).data('validity', arrValidity); */
	});


	fn('.submit-button').get(0).beforeSubmit.add(function(event, button) {
		var arrValidity = $(params.context).data('validity');
		return validate_dates(event,button, arrValidity);
	});

});
