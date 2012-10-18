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

	function loadGraphs() {
		$.getJSON(
			window.url_root + '/xmd/loadaction.php',
			{
				action: 'charts',
				method: 'getGraphs'
			},
			function(data) {

				$('<option/>')
					.val('')
					.html('-----')
					.appendTo('#dropDownGraphs');

				$(data).each(function(id, elem) {
					$('<option/>')
						.val(elem)
						.html(elem)
						.appendTo('#dropDownGraphs');
				});

				$('#dropDownGraphs').change(function(event) {
					var selected = $(':selected', this).val();
					if (selected != '') {
						$.getJSON(
							window.url_root + '/xmd/loadaction.php',
							{
								action: 'charts',
								method: 'getGraphInfo',
								graph: selected
							},
							function(data) {
								new Graph({element: $('#chart'), graph: data});
								//new Grid({element: $('#grid'), graph: data});
								//$('#chkShowGrid').change();
							}
						);
					}
				});
			}
		);
	}

	$('#refresh').click(function(event) {
		$('#dropDownGraphs').change();
	});

	$('#chkShowGrid').hide();
	$('#chkShowGrid_label').hide();
	$('#chkShowGrid').change(function(event) {
		var checked = $(this).attr('checked');
		if (checked) {
			$('#chart').hide();
			$('#grid').show();
		} else {
			$('#chart').show();
			$('#grid').hide();
		}
	});

	loadGraphs();

});

var Graph =  function(options) {

	this.LINES = 1;
	this.BARS = 2;
	this.POINTS = 3;

	this.options = null;
	this.flot = null;
	this.previousPoint = null;

	this._init = function(options) {
		this.options = $.extend({element: null, graph: null}, options);
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

		this.flot.series = [
		];

		var g = this.options.graph;
//		console.log(g, g.SERIES);
		for (var s in g.SERIES) {
			var serie = {};
			s = g.SERIES[s];

			for (var o in s.ATTRIBUTES) {
				this.createSerieProperty(o, s.ATTRIBUTES[o], serie);
			}
			for (var o in s.PROPERTIES) {
				this.createSerieProperty(o, s.PROPERTIES[o], serie);
			}

			serie.data = [];
		//	console.log(s);
			for (var i=0; i<s.VALUES.length; i++) {
				serie.data.push([
					parseFloat(s.VALUES[i].x),
					parseFloat(s.VALUES[i].y)
				]);
			}
			this.flot.series.push(serie);
			//console.log(this.flot);
		}
	};

	this.createSerieProperty = function(property, value, serie) {
		switch(property) {
			// Units for labels
			case 'x_unit':
				//serie.label = serie.label + '(' + value + ')';
				break;
			case 'y_unit':
				serie.label = serie.label + ' (' + value + ')';
				break;
			// Number of x/y axis (1 or 2)
			case 'xaxis':
				serie.xaxis = value;
				break;
			case 'yaxis':
				serie.yaxis = value;
				break;
			// Type of serie
			case 'SerieRepresentation':
				value = parseInt(value);
				switch (value) {
					case this.LINES:
						serie.lines = {
							show: true,
							lineWidth: 2
						};
						break;
					case this.BARS:
						serie.bars = {
							show: true,
							lineWidth: 2
						};
						break;
					case this.POINTS:
						serie.points = {
							show: true,
							radius: 3,
							lineWidth: 1
						};
						break;
				}
				break;
			case 'Label':
				serie.label = value;
				break;
			case 'shadowSize':
				serie.shadowSize = value;
				break;
			case 'color':
				serie.color = value;
				break;
			// axis options for time lines
			case 'xaxis_mode':
				if (value == 'time') {
					$.extend(this.flot.options.xaxis, {
						mode: 'time'
	    			});
    			}
				break;
			case 'xaxis_timeformat':
				$.extend(this.flot.options.xaxis, {
				    timeformat: value
    			});
				break;
			case 'x2axis_mode':
				if (value == 'time') {
					$.extend(this.flot.options.x2axis, {
						mode: 'time'
	    			});
    			}
				break;
			case 'x2axis_timeformat':
				$.extend(this.flot.options.x2axis, {
				    timeformat: value
    			});
				break;
			case 'yaxis_mode':
				if (value == 'time') {
					$.extend(this.flot.options.yaxis, {
						mode: 'time'
	    			});
    			}
				break;
			case 'yaxis_timeformat':
				$.extend(this.flot.options.yaxis, {
				    timeformat: value
    			});
				break;
			case 'y2axis_mode':
				if (value == 'time') {
					$.extend(this.flot.options.y2axis, {
						mode: 'time'
	    			});
    			}
				break;
			case 'y2axis_timeformat':
				$.extend(this.flot.options.y2axis, {
				    timeformat: value
    			});
				break;
		}
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

			// do the zooming
			plot = $.plot(
				$this.options.element,
				$this.flot.series,
				newOptions
			);

			//it doesn't fire event on the overview to prevent eternal loop
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

// ALL
/*var Grid = function(options) {

	this.options = null;

	this._init = function(options) {
		this.options = $.extend({element: null, graph: null}, options);
		this.createGrid();
	};

	this.createGrid = function() {
		var table = $('<table class="grid"></table>');
		table.append($('<tr><th>Test</th><th>Label</th><th>Calls</th><th>TimeAvarage (ms)</th><th>MemoryAvarage (Kb)</th><tr>'));
		for (var i=0; i<dataset.data.length; i++) {
			var data = dataset.data[i];
			var html = '<tr><td>'+data.idtest+'</td><td>'+data.label+'</td><td>'+data.calls+'</td><td>'+data.time+'</td><td>'+data.memory+'</td></tr>';
			table.append(html);
		}
	};

	this._init(options);
};*/
