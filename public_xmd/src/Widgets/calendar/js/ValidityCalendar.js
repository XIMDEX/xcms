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

(function(X) {
	X.ValidityCalendar = Object.xo_create({

		calfrom: null,
		calto: null,

		_init: function(options) {
			this.calfrom = options.calfrom;
			this.calto   = options.calto;
			this.registerEvents();
		},

		registerEvents: function() {
			$(this.calfrom).bind('timeblur', this.onTimeBlur.bind(this));
			$(this.calto).bind('timeblur', this.onTimeBlur.bind(this) );
		},

		setCalFrom: function(cal) {
			this.calfrom = cal;
		},

		setCalTo: function(cal) {
			this.calto = cal;
		},

		getCalFrom: function() {
			return this.calfrom;
		},

		getCalTo: function() {
			return this.calto;
		},

		validateDiff: function(options) {


			if(this.calfrom != null && this.calto != null) {
				var stampfrom = $(this.calfrom).calendar('getStampValue');
				var stampto = $(this.calto).calendar('getStampValue');

				if(stampfrom != '' &&  stampto != '0' && (stampto - 60) <stampfrom) {
					var rel = $(this.calfrom).calendar('getRel') || $(this.calto).calendar('getRel');
					console.log(rel, stampfrom, stampto);
					if(options.msg) {
						if(rel != null) {
							alert(_("The validity end date of ")+rel+_(" must be at least one minute after validity start date"));
						}else {
							alert(_("The validity end date must be at least one minute after validity end start"));
						}
					}
					return false;

				}
				return true;

			}
			//Hay que validar:
			//2.Que en ciertos casos el calendario de inicio de vigencia sea anterior a fin de vigencia
		},

		onTimeBlur: function(event, params) {
			this.validateDiff(params);
		}

	});

})(X);
