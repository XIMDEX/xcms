/**
 * labelWidth is a plugin for jQuery that normalizes the width of the labels in a form.
 * Copyright (C) 2010 Elena Ramirez <ahdiaz@gmail.com> http://www.e-lena.es
 * Copyright (C) 2010 Antonio Hernandez Diaz <ahdiaz@gmail.com> http://ahdiaz.euler.es
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @version 1.0
 *
 */

(function($) {

	$.fn.labelWidth = function(options) {

		options = options || {};
		var settings = {
			fieldset: 'fieldset',
			label: 'label',
			min: 0,
			max: 0
		};
		$.extend(settings, options);

		return this.each(function() {

			$(settings.fieldset, this).each(function() {

				var mw = settings.min;
				var $labels = $(settings.label, this);

				$labels.each(function() {
					var w = $(this).outerWidth(true);
					if (w > mw) {
						mw = w;
					}
				});

				if (settings.max > 0 && mw > settings.max) {
					mw = settings.max;
				}
				

				if ($labels.is('.aligned')) {
					$labels.width(mw);
				}
			});
		});
	};

})(jQuery);
