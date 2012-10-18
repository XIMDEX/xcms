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

	$.widget('ui.calendar', {
		type: null,
		id: null,
		rel: null,
		actived: false,

		_init: function() {

			//if already init, out
			if(this.actived)
				return false;
			else
				this.actived = true;

			var widget = this;

			this.id = $(this.element).attr('id');
			this.options.format = this.options.format ||  X.widgetsVars.getValue(this.id, "format_display");
			this.type =  X.widgetsVars.getValue(this.id, "type");
			this.rel = this.options.rel || X.widgetsVars.getValue(this.id, "rel");
			var timezone = X.widgetsVars.getValue(this.id, "timezone");

			this._datepickerController(
				'.xim-calendar-datefield',
				'.xim-calendar-datefield',
				'.xim-calendar-datepicker',
				$(this.element),
				widget,
				true
			)();

			$('.xim-calendar-datefield', $(this.element))
				.click(
					this._datepickerController(
						'.xim-calendar-datefield',
						'.xim-calendar-datefield',
						'.xim-calendar-datepicker',
						$(this.element),
						widget,
						false
					)
				);

			$('.xim-calendar-hourfield', $(this.element))
				.blur(function(event) {
					this._checkTimeFormat('H', this);
					$(this.element).trigger('timeblur', [{widget: this.element,type:'H', hour: this.getHour(), msg: true}]);
				}.bind(this));


			$('.xim-calendar-minfield', $(this.element))
				.blur(function(event) {
					this._checkTimeFormat('i', this);
					$(this.element).trigger('timeblur', [{widget: this.element,type:'i', minute: this.getMinute(), msg: true}]);
				}.bind(this));


			$('.xim-calendar-secfield', $(this.element))
				.blur(function(event) {
					this._checkTimeFormat('s', this);
					$(this.element).trigger('timeblur', [{widget: this.element,type:'s', second: this.getSecond(), msg: true}]);
				}.bind(this));


			if("from" == this.type) {
				$('.xim-calendar-button', this.element)
					.click(
						this._setCurrentDate(
							this.element
						)
					);

			}else {
				$('.xim-calendar-button', this.element)
					.click(
						this._setUndefinedDate(this.element)
					);
			}

		if("to" == this.type) {
			$(this.element).closest(".xim-calendar-container").append("<div class='xim-calendar-alert'>*"+_("The periods are established in the server's time zone")+"("+timezone+")</div>");
		}

		widget._setStampValue();


			$(this.element).removeClass('hidden');
		},

		getHour: function() { return parseInt($('.xim-calendar-hourfield', this.element).attr('value'),10); },
		getMinute: function() { return parseInt($('.xim-calendar-minfield', this.elmement).attr('value'),10); },
		getSecond: function() { return parseInt($('.xim-calendar-secfield', this.elmement).attr('value'),10); },

		_setCurrentDate: function( $this) {
			var widget = this;
			return function(event) {
				//$(field, $this).attr('value', dateValue);
				$('.xim-calendar-button', $this).attr('value', _(' Select Date '));
				$('.xim-calendar-button', $this).unbind().click(
					widget._datepickerController(
						'.xim-calendar-datefield',
						'.xim-calendar-datefield',
						'.xim-calendar-datepicker',
						$this,
						widget,
						false
					)
				);

				var urlAction = (X.restUrl+"?action=widgets&widget=calendar&wmethod=date_system&ajax=text");
				$.ajax({
					type: "GET",
					url: urlAction,
					success:  function(data) {
						if(null != data) {
							var currentDate = new Date();
							currentDate.setTime(data*1000);

							$('.xim-calendar-date-from', $this).attr('value', currentDate);

							var hours = currentDate.getHours();
							hours = (hours < 10)? "0"+hours: hours; //hours zerofill
							$('.xim-calendar-hourfield', $(this.element)).attr('value', hours);
							var minutes = currentDate.getMinutes();
							minutes = (minutes < 10)? "0"+minutes: minutes; //minutes zerofill
							$('.xim-calendar-minfield', $(this.element)).attr('value', minutes);
							var seconds = currentDate.getSeconds();
							seconds = (seconds < 10)? "0"+seconds: seconds; //seconds zerofill
							$('.xim-calendar-secfield', $(this.element)).attr('value', seconds);
							$('.xim-calendar-datepicker', $(this.element)).datepicker("setDate", currentDate);

							var datefield = this.options.format;

							datefield = this._replaceFormat(datefield, "yy", currentDate.getFullYear());
							var month = parseInt(currentDate.getMonth(), 10) + 1;
							datefield = this._replaceFormat(datefield, "mm", month);
							var day = parseInt(currentDate.getDate(), 10);
							datefield = this._replaceFormat(datefield, "dd", day );


							$('.xim-calendar-datefield', $this).attr('value', datefield);

							this._validate($this.attr('id'));
						}

					}.bind(this),
					error: function() {
						alert("error");
					}
				});

			}.bind(this);
		},

		_replaceFormat: function(template, format, value) {
			template = template || ""
			format = format || ""
			value = value || "0"

			value = parseInt(value, 10);
			value = (value < 10)? "0"+value: value; //minutes zerofill

			var newString = template.split(format);
			return newString.join(value);
		},

		_setUndefinedDate: function($this) {
			var widget = this;

			return function(event) {
				//00-00-0000
				$('.xim-calendar-datefield', $this).attr('value', '00-00-0000');
				//00
				$('.xim-calendar-hourfield', $this).attr('value', '00');
				//00
				$('.xim-calendar-minfield', $this).attr('value', '00');
				//00
				$('.xim-calendar-secfield', $this).attr('value', '00');
				$('.xim-calendar-button', $this).attr('value', _(' Select Date '));
				$('.xim-calendar-button', $this).unbind().click(
						widget._datepickerController(
								'.xim-calendar-datefield',
								'.xim-calendar-datefield',
								'.xim-calendar-datepicker',
								$this,
								widget,
								false
						)
					);

				$('.xim-calendar-date-to', $this).attr('value', null);
			}.bind(this);
		},

		_validate: function(id) {
			this._checkTimeFormat('H', this);
			this._checkTimeFormat('i', this);
			this._checkTimeFormat('s', this);

			return true;

		},

		_datepickerController: function(field, button, dpicker, $this, widget, create) {
			return function(event) {

				if(!create) {
					$(dpicker, $this).show();
					$(dpicker, $this).datepicker('destroy');
				}
				$(dpicker, $this)
					.datepicker({
						dateFormat: this.options.format,
						changeYear: true,
					  dayNamesMin: [_('Su'), _('Mo'),_('Tu'), _('We'), _('Th'), _('Fr'), _('Sa')],
					 monthNamesShort: [_('Jan'),_('Feb'),_('Mar'),_('Apr'),_('May'),_('Jun'),_('Jul'),_('Aug'),_('Sep'),_('Oct'),_('Nov'),_('Dec')],
					  monthNames: [_('January'),_('February'),_('March'),_('April'),_('May'),_('June'),_('July'),_('August'),_('September'),_('October'),_('November'),_('December')],
						gotoCurrent: true,
						onSelect: function(dateText, inst) {
							var date_format = this.options.format
							date_format = this._replaceFormat(date_format, "dd", inst.currentDay);
							date_format = this._replaceFormat(date_format, "mm", inst.currentMonth+1);
							date_format = this._replaceFormat(date_format, "yy", inst.currentYear);

							$(field, $this).val(date_format);

							if("from" == widget.type) {
								$('.xim-calendar-button', $this).attr('value', " "+_('Now')+" ");
								$('.xim-calendar-button', $this).unbind().click(
										widget._setCurrentDate( $this )
									);
							} else {
								$('.xim-calendar-button', $this).attr('value', " "+_('Undetermined')+" ");
								$('.xim-calendar-button', $this).unbind().click(
										widget._setUndefinedDate($this)
								);
							}
							widget._validate($this.attr('id'));
							$(dpicker, $this).hide();
						}.bind(this)
					});
				if(create)
					$(dpicker, $this).hide();
			}.bind(this);
		},

		_checkTimeFormat: function(type,  widget) {
			var widget = this;

			var pattern = /^(\d{1,2})?$/;
			var match = null;

			if(type == 'H') {
				var hourValue = $('input.xim-calendar-hourfield', this.element).attr('value');
				match = hourValue.match(pattern);
				if(match == null || (parseInt(hourValue) < 0 || parseInt(hourValue) > 23)) {
					$('input.xim-calendar-hourfield', this.element).attr('value', '00');
				}
			} else if(type == 'i') {
				var minValue = $('input.xim-calendar-minfield', this.element).attr('value');
				match = minValue.match(pattern);
				if(match == null || (parseInt(minValue) < 0 || parseInt(minValue) > 59)) {
					$('input.xim-calendar-minfield', this.element).attr('value', '00');
				}
			} else if(type == 's') {
				var secValue = $('input.xim-calendar-secfield', this.element).attr('value');
				match = secValue.match(pattern);
				if(match == null || (parseInt(secValue) < 0 || parseInt(secValue) > 59)) {
					$('input.xim-calendar-secfield', this.element).attr('value', '00');
				}
			}

			widget._setStampValue();
		},

		_setStampValue: function() {
			//is not default value
			if( $('.xim-calendar-datefield', this.element).attr('value') != "00-00-0000")  {
				var dateValue = parseInt($('.xim-calendar-datepicker', this.element).datepicker('getDate').getTime(),10) / 1000;
				var hourValue = parseInt($('.xim-calendar-hourfield', this.element).attr('value'),10) * 3600;
				var minValue = parseInt($('.xim-calendar-minfield', this.element).attr('value'),10) * 60;
				var secValue = parseInt($('.xim-calendar-secfield', this.element).attr('value'),10) * 1;

				var stampValue = dateValue + hourValue + minValue + secValue;
			}else {
				var stampValue = 0;
			}

			//¿illegal character ?
			if(isNaN(stampValue) || 0 == stampValue) {
				$('.xim-calendar-date-'+this.type, this.element).val('');
			}else {
				$('.xim-calendar-date-'+this.type, this.element).val(stampValue);
			}

			return stampValue;
		},

		getRel: function() {
			return this.rel;
		},

		getStampValue: function() {
			return this._setStampValue();
		},


		getter: ['getStampValue', 'getRel']
	});


})(jQuery);
