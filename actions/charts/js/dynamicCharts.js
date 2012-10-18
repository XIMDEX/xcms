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

$(document).ready(function(event) {
	new Graph({
		element: $('#chart'),
		serie: {
			type: cht,
			label: chl,
			data: series
		}
	});
});

var Graph =  function(options) {

	this.options = null;
	this.flot = null;
	this.previousPoint = null;

	this._init = function(options) {
		this.options = $.extend({element: null, serie: null}, options);
		this.createFlotProperties();
		this.createGraph();
	};

	this.createFlotProperties = function() {

		var axisOptions = {
			mode: null,
			timeformat: '%d-%m-%y<br />%H:%M:%S',
			monthNames: ['jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec']
		};

		this.flot = {};

		this.flot.options = {
			legend: {
				position: 'nw'
			},
			points: {
				show: false,
				radius: 3,
				lineWidth: 1
			},
			selection: {
				mode: 'xy'
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			shadowSize: 3,
			xaxis: $.extend({}, axisOptions),
			x2axis: $.extend({}, axisOptions),
			yaxis: $.extend({}, axisOptions),
			y2axis: $.extend({}, axisOptions)
		};

		switch (this.options.serie.type) {
			case 'l':
				this.options.serie.lines = {
					show: true,
					lineWidth: 2
				};
				break;
			case 'b':
				this.options.serie.bars = {
					show: true,
					lineWidth: 2
				};
				break;
			case 'p':
				this.options.serie.points = {
					show: true,
					radius: 3,
					lineWidth: 1
				};
				break;
		}
		this.flot.series = [this.options.serie];
	};

	this.createGraph = function() {
		$.plot(
			this.options.element,
			this.flot.series,
			this.flot.options
		);
		this.bindPlotEvents();
	};

	this.bindPlotEvents = function() {

		var $this = this;
	    $(this.options.element).unbind().bind("plothover", function (event, pos, item) {

            if (item) {
                if ($this.previousPoint != item.datapoint) {
                    $this.previousPoint = item.datapoint;

                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1].toFixed(2);

                    $this.showTooltip(item.pageX, item.pageY,
						item.series.label + " of " + x + " = " + y);
                }
            } else {
                $("#tooltip").remove();
                $this.previousPoint = null;
            }
	    });

	    $(this.options.element).bind("plotselected", function (event, ranges) {
	    	//console.log(ranges);

			// clamping the zooming to prevent eternal zoom
			if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
				ranges.xaxis.to = ranges.xaxis.from + 0.00001;
			}
			if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
				ranges.yaxis.to = ranges.yaxis.from + 0.00001;
			}

			var newOptions = $.extend({}, $this.flot.options, {
				xaxis: {min: ranges.xaxis.from, max: ranges.xaxis.to},
				yaxis: {min: ranges.yaxis.from, max: ranges.yaxis.to}
			});
			if (ranges['x2axis']) {
				newOptions = $.extend(newOptions, {
					x2axis: {min: ranges.x2axis.from, max: ranges.x2axis.to}
				});
			}
			if (ranges['y2axis']) {
				newOptions = $.extend(newOptions, {
					y2axis: {min: ranges.y2axis.from, max: ranges.y2axis.to}
				});
			}

			// doing the zooming
			plot = $.plot(
				$this.options.element,
				$this.flot.series,
				newOptions
			);

			// it doesn't fire event on the overview to prevent eternal loop
			//overview.setSelection(ranges, true);
		});

	    $(this.options.element).bind("plotclick", function (event, ranges) {
	    	//console.log(arguments);
	    	plot = $.plot(
				$this.options.element,
				$this.flot.series,
				$this.flot.options
			).clearSelection(true);
	    });
	};

	this.showTooltip = function(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    };

	this._init(options);
};
