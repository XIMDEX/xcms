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

var clock = function() {
	var containers = $('.xim-clock');
	if (containers.length == 0) return;
	var format = function(item) {
		if (new String(item).length < 2) item = '0' + item;
		return item;
	};
	var d = new Date();
	var sec = d.getSeconds();
	var sep = (sec%2 == 0) ? ':' : '&nbsp;';
	var t = '%H %sep %m %sep %s';
	t = t.replace(/%H\b/g, format(d.getHours()));
	t = t.replace(/%m\b/g, format(d.getMinutes()));
	t = t.replace(/%s\b/g, format(sec));
	t = t.replace(/%sep\b/g, sep);
	containers.html(t);
	containers.each(function(idx, elem) {
		if (!$(elem).parents('div#panel_clock').data('initTime')) {
			$(elem).parents('div#panel_clock').data('initTime', new Date());
		}
	});
};

var autoalert = function() {
	var containers = $('.xim-autoalert');
	if (containers.length == 0) return;
	containers.each(function(idx, elem) {
		// Call update() method without arguments, so the widget options are kept
		$(elem).panel('update');
	});
};

(function($) {

	$.fn.extend({
		clock: function(options) {
			this.addClass('xim-clock');
			var t = XimTimer.getInstance();
			t.addObserver(clock, 1000);
			t.start();
			return this;
		},
		autoalert: function(options) {
			var interval = 300 * 1000; // 5 minutes
			options = $.extend({interval: interval}, options);
			this.addClass('xim-autoalert');
			var t = XimTimer.getInstance();
			t.addObserver(autoalert, interval);
			t.start();
			return this;
		}
	});


})(jQuery);
