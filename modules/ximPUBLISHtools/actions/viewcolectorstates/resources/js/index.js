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


$('document').ready(function() {		

	(function($) {
	        $.widget('ui.canvas_i', {
	        
                _init: function() {
		        	this.periodical_refresh_colectors_data(10000);
                },
	        
				toggle_details: function(id) {
					if($('#node_content_details_' + id, this.element).toggle().filter(':visible').length > 0)
						$('#toggle_details_' + id).text('Ocultar Detalles');
					else
						$('#toggle_details_' + id).text('Ver Detalles');
				},
				
				get_colectors_data: function() {
					var pars = '';
					var id_colector = null;
					var actual_page = null;
					
					var pages_by_colectors = $('.page', this.element);
					
					for(var i = 0; i < pages_by_colectors.length; i ++) {
						actual_page = parseInt(pages_by_colectors[i].innerHTML);
						id_colector = pages_by_colectors[i].readAttribute('id');
						id_colector = id_colector.replace('page_', '');
						pars += '&pag[' + id_colector + ']=' + actual_page;
					}

					url = this.options.qm.url + '/xmd/loadaction.php?actionid=' 
                		+ this.options.qm.get_value('actionid') 
                		+ '&nodeid=' + this.options.qm.get_value('nodeid')
                    	+ pars;

	            	$.get(url, {method: 'getData'}, function(data) {
	            		if(data) {
	            			var colectors_data = eval(data);
	            			this.refresh_colectors_data(colectors_data);
	            		}
	            	}.bind(this));
				},
				
				change_page: function(id_colector, direction) {
					var actual_page = parseInt($('#page_' + id_colector).innerHTML);
					if(direction == 'next') {
						pag = actual_page + 1;
					} else if(direction == 'prev') {
						pag = actual_page - 1;
					}
					$('#page_' + id_colector).text(parseInt(pag));
				},
				
				refresh_colectors_data: function(colectors_data) {
					var num_colectors = colectors_data.length;
					for(var i = 0; i < num_colectors; i++) {
						if(($('#filter_colector_state').val() == '2' && colectors_data[i].state_id != 0) || 
							($('#filter_colector_state').val() == '3' && (colectors_data[i].state_id != 2 && colectors_data[i].state_id != 1)) ||
							($('#filter_colector_state').val() == '4' && (colectors_data[i].state_id != 3 && colectors_data[i].state_id != 4)) ||
							($('#filter_colector_state').val() == '5' && colectors_data[i].state_id != 5))
							$('#nodo_' + colectors_data[i].id_colector).hide();
						else
							$('#nodo_' + colectors_data[i].id_colector).show();
						
						// Check if it's necessary updating colector data...
						if(colectors_data[i].state_id == 2 && colectors_data[i].start_generation == null) {
							continue;
						}
						
						$('.pending_relation_' + colectors_data[i].id_colector).each(function(i, item) {
							$(item).remove();
						});
						
						// Refresh news data
						if(colectors_data[i].state == 'Generado' || colectors_data[i].state == 'Generándose') {
							for(var id_new in colectors_data[i].pending_relations) {
								var state = colectors_data[i].pending_relations[id_new].State == 'pending' ? 'Pendiente' : 'Asociada';
								var src = url_root + '/modules/pro/ximPUBLISHtools/images/reports/' + (colectors_data[i].pending_relations[id_new].State == 'pending' ? 'out.gif' : 'in.gif');
								var content = '<tr style="background-color: #f9f9f9" class="pending_relation_' + colectors_data[i].id_colector + '" id="id_new_' + colectors_data[i].id_colector + 
												'_' + id_new + '" bgcolor="{cycle values="#f9f9f9,#eeeeee"}">' +
												'<td class="fichero_pendiente"><span id="new_name_{$id_new}_' + colectors_data[i].id_colector + '">' + 
												colectors_data[i].pending_relations[id_new].NewName + '</span></td>' +
												'<td class="fichero_pendiente"><span id="new_user_name_{$id_new}_' + colectors_data[i].id_colector + '">' + 
												colectors_data[i].pending_relations[id_new].UserName + '</span></td>' +
												'<td class="fichero_pendiente"><span id="new_time_{$id_new}_' + colectors_data[i].id_colector + '">' + 
												this.stamp_to_date(colectors_data[i].pending_relations[id_new].Time) + '</span></td>' +
												'<td class="fichero_pendiente"><span id="new_fechain_{$id_new}_' + colectors_data[i].id_colector + '">' + 
												this.stamp_to_date(colectors_data[i].pending_relations[id_new].FechaIn) + '</span></td>' +
												'<td class="fichero_pendiente"><span id="new_fechaout_{$id_new}_' + colectors_data[i].id_colector + '">' + 
												this.stamp_to_date(colectors_data[i].pending_relations[id_new].FechaOut) + '</span></td>' +
												'<td>' +
												//'<span id="new_state_' + id_new + '_' + colectors_data[i].id_colector + '">' + state + '</span>' +
												'<img id="new_image_' + id_new + '_' + colectors_data[i].id_colector + '" src="' + src + '" width="17" height="15" ' + 
												'title="Pendiente"/>' +
												'</td>' +
												'</tr>';

								$('#tabla_detalles_' + colectors_data[i].id_colector).append(content);
							}
						}
						
						// Refresh state
						$('#state_title_' + colectors_data[i].id_colector).text(colectors_data[i].state);
						$('#state_content_' + colectors_data[i].id_colector).text(colectors_data[i].state);
						if(colectors_data[i].state == 'Generándose' || colectors_data[i].state == 'Generado y Publicándose')
							$('#state_image_' + colectors_data[i].id_colector).show();
						else
							$('#state_image_' + colectors_data[i].id_colector).hide();
				
						// Refresh user name y last generation date
						$('#user_name_' + colectors_data[i].id_colector).text(colectors_data[i].user_name);
						$('#last_generation_' + colectors_data[i].id_colector).text(this.stamp_to_date(colectors_data[i].last_generation));
						
						// Refresh progress bar
						$('#progress_' + colectors_data[i].id_colector).text(colectors_data[i].progress);
						$('#progress_img_' + colectors_data[i].id_colector).attr('style', 'width:' + colectors_data[i].progress + 'px;height:6px;');
						
						// Refresh generation dates...
						$('#start_generation_' + colectors_data[i].id_colector).text(colectors_data[i].start_generation ? this.stamp_to_date(colectors_data[i].start_generation) : '-');
						//$('#end_generation_' + colectors_data[i].id_colector).text(colectors_data[i].end_generation ? this.stamp_to_date(colectors_data[i].end_generation) : '-');
						$('#end_publication_' + colectors_data[i].id_colector).text(colectors_data[i].end_publication ? this.stamp_to_date(colectors_data[i].end_publication) : '-');
						$('#start_generation_title_' + colectors_data[i].id_colector).text(colectors_data[i].start_generation ? 'Comienzo de la generación:' : '');
						//$('#end_generation_title_' + colectors_data[i].id_colector).text(colectors_data[i].end_generation ? 'Comienzo de la publicación:' : '');
						$('#end_publication_title_' + colectors_data[i].id_colector).text(colectors_data[i].end_publication ? 'Fin de la generación:' : '');
					}
				},
				
				periodical_refresh_colectors_data: function(interval) {
					this.get_colectors_data();
					/*var t = XimTimer.getInstance();
					t.addObserver(this.get_colectors_data, interval);
					t.start();*/
				},
				
				stamp_to_date: function(stamp) {
					if(!stamp)
						return '-';
					var new_date = new Date();
					new_date.setTime(parseInt(stamp) * 1000);
					
					var date_string = ((new_date.getDate() < 10) ? '0' : '') + new_date.getDate() + '/';
					date_string += ((new_date.getMonth() < 9) ? '0' : '') + (new_date.getMonth() + 1) + '/';
					date_string += new_date.getFullYear();
					date_string += ' ' + ((new_date.getHours() < 10) ? '0' : '') + new_date.getHours();
					date_string += ':' + ((new_date.getMinutes() < 10) ? '0' : '') + new_date.getMinutes();
					date_string += ':' + ((new_date.getSeconds() < 10) ? '0' : '') + new_date.getSeconds();
					
					return date_string;
				}
	        });
	        $.ui.canvas_i.getter = [];	        
	})(jQuery);
});
