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

    var defaultValue = "CreativeWork";

	$.widget('ui.ontologywidget', {
	
		defaults: {
			messageAdd: "Add?",
			messageDelete: "Delete?",
			jsonURL: X.restUrl+"?mod=ximTAGS&action=setmetadata&method=getLocalOntology",
			rootElement: defaultValue,
			inputFormat: "json",
			inputJson: "SchemaOrg.json",
            onSelect: function(){ },
            offSelect: function(){ },
            element: null
		},
		selected : [],
		$footer: null,
		_init: function(opt){
			this.options = $.extend(this.defaults, this.options);
            this.options.element = this.element;
			this.loadValues();
			this.selected = this.options.selected;
			this.$footer = $(".infobox", this.element);
			this._on($(".tree", this.element),{click:"_selectTree"});			
			this._on($(".text", this.element),{click:"_selectText"});
			this._on($(".main_action", this.$footer),{click:"_selectFooter"});
			this._on($("a.close", this.$footer), {click: "_hideFooter"});
			this._on($("a.ontology-close", this.element), {click: function(){
				$("a.ontology-close", this.element).parent().addClass('hidden');
			}});
		},
		_selectTree: function(){
			
			this.showTree();

			$(".ontology-browser", this.element).removeClass("hidden");
			$(".textViewer", this.element).addClass("hidden");
			$(".treeViewer", this.element).removeClass("hidden");
		},
		_selectText: function(){

			this.showText();
			$(".ontology-browser", this.element).removeClass("hidden");
			$(".textViewer", this.element).removeClass("hidden");
			$(".treeViewer", this.element).addClass("hidden");
			

		},

		_selectFooter: function(ev){
			  var that = this;
			  var textNode = d3.select(this.element[0]).selectAll("text").filter(function(d, i) {
				  return d.name == $('h1', that.$footer).text();
			  });
			
			  var rectNode = d3.select(this.element[0]).selectAll("rect").filter(function(d, i) {
			    return d.name == $('h1', that.$footer).text();
			  });
			
			  if ($(ev.currentTarget).text() == this.options.messageAdd) {
				    textNode.attr("class", "nodetext added");
				    rectNode.attr("class", "added");
				    this.selected.push($('h1', that.$footer).text());
                    this.options.onSelect({'name': $('h1', that.$footer).text()});
				    $('.main_action', that.$footer).text(this.options.messageDelete);
			  }else {
				textNode.attr("class", "nodetext");
				rectNode.attr("class", that._isChild);
				this.selected.splice(this.selected.indexOf($('h1', that.$footer).text()), 1);
                this.options.offSelect($('h1', that.$footer).text());
				$('.main_action', that.$footer).text(this.options.messageAdd);
			}

		},
		_hideFooter: function(){
  			this.$footer.hide('slow');

		},

		_getElementByParent: function(data, base){
  			if (!_.isUndefined(base)) {
  			var that = this;
			var el = new Object();
		      el.name = base;
		      if (!_.isUndefined(data.types[base])) {
			     el.specific_properties = data.types[base].specific_properties;
			     childrens = data.types[base].subtypes;
			     if (childrens.length > 0) {
			         var children_array = [];
			         _.each(childrens, function(key, val) {
				        children_array.push(that._getElementByParent(data, key));
			         });
			         el.children = children_array;
			         return el;
			     }
			     else {
			         el.size = Math.floor((Math.random()*1000)+1);
			         return el;
			     }
		      }
		      else {
			     console.log("Error accessing to property " + base)
			     return null;
		      }
		  }
		  else {
		      return null;
		  }
		},

		_toggle: function(d){
				
			if (d.children) {
				d._children = d.children;
				d.children = null;
			} else {
				d.children = d._children;
				d._children = null;
			}
		},

        _color: function(d) {
            return d._children ? "#990000" : d.children ? "#ffffff" : "#ffffff";
        },
        
        _isChild: function(d) {
            return d._children ? "hasChild" : d.children ? "isLeaf" : "isLeaf";
        },

        _textcolor: function(d) {
            return "#000000";
            // return d._children ? "#ffffff" : d.children ? "#990000" : "#990000";
        },

		_loadDataInBlock: function (e) {
			$('h1', this.$footer).text(e.name);
			var sp = "";
			 _.each(e.specific_properties, function(val) { return sp += '<li>' + val + '</li>' });
			$('p', this.$footer).html('<ul>' + sp + '</ul>');
			if (this.selected.indexOf(e.name) != -1) {
				$('.main_action', this.$footer).text(this.options.messageDelete);
			}
			else {
				$('.main_action', this.$footer).text(this.options.messageAdd);
			}
		},
		
		loadValues: function() {
            var that = this;
            d3.json(this.options.jsonURL+"&ontologyName="+this.options.inputJson, function(json) {
                childrens = json.types["Thing"].subtypes;
                _.each(childrens, function(key) {
                    if (key == defaultValue) {
                        $('.selectbox-tree select', that.element).append("<option selected>" + key + "</option>");
                        $('.selectbox-text select', that.element).append("<option selected>" + key + "</option>");
                    }
                    else {
                        $('.selectbox-tree select', that.element).append("<option>" + key + "</option>");
                        $('.selectbox-text select', that.element).append("<option>" + key + "</option>");
                    }
                });
            });
            // Returns empty array (no selected tags at the beggining of the action)
            // TODO: It needs to be changed for updating selected tags previously
            return [];
		},
		showTree: function(){
			var that = this;
  			var m = [20, 120, 20, 120],
			w = 900 - m[1] - m[3],
			h = 800 - m[0] - m[2],
			i = 0,
			root;

			var tree = d3.layout.tree()
		      .size([h, w]);

			var diagonal = d3.svg.diagonal()
		      .projection(function(d) { return [d.y, d.x]; });


  			if ($(".treeViewer g", this.element).length == 0) {

			var vis = d3.select(this.element[0]).select(".treeViewer").append("svg:svg")
			.attr("width", w + m[1] + m[3])
			.attr("height", h + m[0] + m[2])
		      .append("svg:g")
			.attr("transform", "translate(" + m[3] + "," + m[0] + ")");

		    d3.json(this.options.jsonURL+"&ontologyName="+this.options.inputJson, function(json) {
		      root = that._getElementByParent(json, that.options.rootElement);
		      root.x0 = h / 2;
		      root.y0 = 0;

              $(".selectbox-tree select", that.element).change(function() {
                console.log("Se activa el cambio en el selectbox-tree");
                console.log(this.element);
                console.log(that.element);
                root = that._getElementByParent(json, $(".selectbox-tree select", that.element).find(":selected").text());
                if (!_.isUndefined(root.children)) {
                    root.children.forEach(toggleAll);
                }
                update(root, that);
              });

		      function toggleAll(d) {
			if (d.children) {
			  d.children.forEach(toggleAll);
			  that._toggle(d);
			}
		      }

              if (!_.isUndefined(root.children)) {
                root.children.forEach(toggleAll);
              }
		      update(root, that);
		    });

		    function update(source, that) {
		      
		      var duration = d3.event && d3.event.altKey ? 5000 : 500;

		      // Compute the new tree layout.
		      var nodes = tree.nodes(root).reverse();

		      // Normalize for fixed-depth.
		      nodes.forEach(function(d) { d.y = d.depth * 180; });

		      // Update the nodes…
		      var node = vis.selectAll("g.node")
			  .data(nodes, function(d) { return d.id || (d.id = ++i); });

		      // Enter any new nodes at the parent's previous position.
		      var nodeEnter = node.enter().append("svg:g")
			  .attr("class", "node")
			  .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; });

		      nodeEnter.append("svg:circle")
			  .attr("r", 1e-6)
			  .attr("class",  function(d) { return d._children ? "hasChild" : "isLeaf"; })
			  .on("click", function(d) { that._toggle(d); update(d, that); });

		      nodeEnter.append("svg:text")
			  .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
			  .attr("dy", ".35em")
			  .attr("text-anchor", function(d) {
			    return d.children ? "end" : "start";
			  })
			  .attr("transform", function(d) {
			    var mx = d._children ? "20" : "0";
			    return "translate(" + mx + "," + 0 + ")";
			  })
			  .text(function(d) { return d.name; })
			  .attr("class", function(d) {
			    return (that.selected.indexOf(d.name) != -1) ? "nodetext added" : "nodetext";
			  })
			  .style("fill-opacity", 1e-6)
			  .on('mouseover', function(d){
			    var el = d3.select(this);
			    if ((!_.isUndefined(el[0][0].attributes.class)) && (el[0][0].attributes.class.nodeValue == "added")) {
			      el.style("font-weight", "bolder")
				.text( function(d) { return d.name + " (-)"; } );
			    }
			    else {
			      el.style("font-weight", "bolder")
				.text( function(d) { return d.name + " (+)"; } );
			    }
			  })
			  .on('mouseout', function(d){
			    var el = d3.select(this);
			    el.style("font-weight", "normal")
			      .text( function(d) { return d.name; } );
			  })
			  .on("click", function(d) {
			    var el = d3.select(this);
			    if ((!_.isUndefined(el[0][0].attributes.class)) && (el[0][0].attributes.class.nodeValue == "added")) {
			      that._loadDataInBlock(d);
			      that.$footer.show("slow");
			    }
			    else {
			      that._loadDataInBlock(d);
			      that.$footer.show("slow");
			    }
			  });

		      // Transition nodes to their new position.
		      var nodeUpdate = node.transition()
			  .duration(duration)
			  .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

		      nodeUpdate.select("circle")
			  .attr("r", 4.5)
			  .attr("class",  function(d) { return d._children ? "hasChild" : "isLeaf"; })

		      nodeUpdate.select("text")
			  .style("fill-opacity", 1)
			  .attr("text-anchor", function(d) {
			    return d.children ? "end" : "start";
			  })
          .attr("transform", function(d) {
            var mx = d._children ? "20" : "0";
            return "translate(" + mx + "," + 0 + ")";
          });

      // Transition exiting nodes to the parent's new position.
      var nodeExit = node.exit().transition()
          .duration(duration)
          .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
          .remove();

      nodeExit.select("circle")
          .attr("r", 1e-6);

      nodeExit.select("text")
          .style("fill-opacity", 1e-6);

      // Update the links…
      var link = vis.selectAll("path.link")
          .data(tree.links(nodes), function(d) { return d.target.id; });

      // Enter any new links at the parent's previous position.
      link.enter().insert("svg:path", "g")
          .attr("class", "link")
          .attr("d", function(d) {
            var o = {x: source.x0, y: source.y0};
            return diagonal({source: o, target: o});
          })
        .transition()
          .duration(duration)
          .attr("d", diagonal);

      // Transition links to their new position.
      link.transition()
          .duration(duration)
          .attr("d", diagonal);

      // Transition exiting nodes to the parent's new position.
      link.exit().transition()
          .duration(duration)
          .attr("d", function(d) {
            var o = {x: source.x, y: source.y};
            return diagonal({source: o, target: o});
          })
          .remove();

      // Stash the old positions for transition.
      nodes.forEach(function(d) {
        d.x0 = d.x;
        d.y0 = d.y;
      });
    }

  }
		},

	showText: function(){

    var that = this;
    var w = 800,
    h = 1300,
    i = 0,
    barHeight = 20,
    barWidth = w * .5,
    duration = 400,
    root;

  var tree = d3.layout.tree()
      .size([h, 100]);

  var diagonal = d3.svg.diagonal()
      .projection(function(d) { return [d.x, d.y]; });

if ($(".textViewer g", this.element).length == 0) {

  var vis = d3.select(this.element[0]).select(".textViewer").append("svg")
      .attr("width", w)
      .attr("height", h)
    .append("svg:g")
      .attr("transform", "translate(20,30)");

  d3.json(that.options.jsonURL+"&ontologyName="+that.options.inputJson, function(json) {
    root = that._getElementByParent(json, that.options.rootElement);
    root.x0 = 0;
    root.y0 = 0;

    $(".selectbox-text select", this.element).change(function() {
        root = that._getElementByParent(json, $(".selectbox-text select", this.element).find(":selected").text());
        if (!_.isUndefined(root.children)) {
            root.children.forEach(toggleAll);
        }
        update(root, that);
    });

    function toggleAll(d) {
      if (d.children) {
        d.children.forEach(toggleAll);
        that._toggle(d);
      }
    }

    if (!_.isUndefined(root.children)) {
        root.children.forEach(toggleAll);
    }
    update(root, that);
  });

  function update(source, that) {
    // Compute the flattened node list. TODO use d3.layout.hierarchy.
    var nodes = tree.nodes(root);
    
    // Compute the "layout".
    var total_height = 0;
    nodes.forEach(function(n, i) {
        n.x = i * barHeight * 1.1;
        if (i == nodes.length - 1) total_height = n.x + barHeight;
    });
    // The total_height should be used for redrawing svg canvas with (height, total_height)
    
    // Update the nodes…
    var node = vis.selectAll("g.node")
        .data(nodes, function(d) { return d.id || (d.id = ++i); });
    
    var nodeEnter = node.enter().append("svg:g")
        .attr("class", "node")
        .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
        .style("opacity", 1e-6);

    // Enter any new nodes at the parent's previous position.
    nodeEnter.append("svg:rect")
        .attr("y", -barHeight / 2)
        .attr("dy", "1em")
        .attr("height", barHeight)
        .attr("width", barWidth)
        .attr("class", "bartext")
        .attr("class", that._isChild)
        .on("click", function(d) { that._toggle(d); update(d, that); });

    nodeEnter.append("svg:rect")
        .attr("y", -barHeight / 2)
        .attr("x", barWidth)
        .attr("height", barHeight)
        .attr("width", 20)
        .attr("cursor", "pointer")
        .attr("class", function(d) {
          return (that.selected.indexOf(d.name) != -1) ? "added selector" : "selector";
        })
        .on("click", function(d) {
          that._loadDataInBlock(d);
          that.$footer.show("slow");
        });
    
    nodeEnter.append("svg:text")
        .attr("dy", 3.5)
        .attr("dx", 5.5)
        .style("fill", that._textcolor)
        .text(function(d) { return d.name; });
        

    nodeEnter.append("svg:text")
        .attr("dy", 5)
        .attr("dx", barWidth + 5.5)
        .attr("class", "action");
        // .text(function(d) {
        //   return selected.indexOf(d.name) == -1 ? "+" : "-";
        // })
        // .on('mouseover', function(d){
        //   //
        // })
        // .on('mouseout', function(d){
        //   //
        // });
    
    // Transition nodes to their new position.
    nodeEnter.transition()
        .duration(duration)
        .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })
        .style("opacity", 1);
    
    node.transition()
        .duration(duration)
        .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })
        .style("opacity", 1);
    
    // Transition exiting nodes to the parent's new position.
    node.exit().transition()
        .duration(duration)
        .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
        .style("opacity", 1e-6)
        .remove();
    
    // Stash the old positions for transition.
    nodes.forEach(function(d) {
      d.x0 = d.x;
      d.y0 = d.y;
    });
  }

	}
}
 
		
	});
})(jQuery);






