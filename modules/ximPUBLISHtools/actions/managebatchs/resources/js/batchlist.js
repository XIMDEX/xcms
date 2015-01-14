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
				
				doPrioritizeSubmit: function (idBatch) {
					this.setFilterValues ();
					$('#frm_id_batch', this.element).val(idBatch);
					$('#frm_prioritize_batch', this.element).val('yes');
					document.frm_batchs.submit();
				},
				
				doDeprioritizeSubmit: function (idBatch) {
					this.setFilterValues ();
					$('#frm_id_batch', this.element).val(idBatch);
					$('#frm_deprioritize_batch', this.element).val('yes');
					document.frm_batchs.submit();
				},
				
				doDeactivateSubmit: function (idBatch) {
					this.setFilterValues ();
					$('#frm_id_batch', this.element).val(idBatch);
					$('#frm_deactivate_batch', this.element).val('yes');
					document.frm_batchs.submit();
				},
				
				doActivateSubmit: function (idBatch) {
					this.setFilterValues ();
					$('#frm_id_batch', this.element).val(idBatch);
					$('#frm_activate_batch', this.element).val('yes');
					document.frm_batchs.submit();
				},
				
				doFilterSubmit: function () {
					this.setFilterValues ();
					document.frm_batchs.submit();
				},
				
				setFilterValues: function () {
					$('#frm_filter_state_batch', this.element).val($('#frm_select_filter_state_batch', this.element).val());
					$('#frm_filter_active_batch', this.element).val($('#frm_select_filter_active_batch', this.element).val());
					
					if ($('#update', this.element).val() != 'Click Aqui...') {
						$('#frm_filter_up_date', this.element).val($('#update', this.element).val() + ' '
							+ $('#uphour', this.element).val() + ':'
							+ $('#upmin', this.element).val());
					}
					
					if ($('#downdate', this.element).val() != 'Click Aqui...') {
						$('#frm_filter_down_date', this.element).val($('#downdate', this.element).val() + ' '
							+ $('#downhour', this.element).val() + ':'
							+ $('#downmin', this.element).val());
					}
					
					$('#frm_filter_batch', this.element).val('yes');
				},
				
				showOrHideContent: function (divId, name, extra) {
					if (this.isVisibleAnyContents(name) && extra == 'all') {
						this.hideContent(name, divId);
						$('#' + divId, this.element).hide();
						return;
					}
					this.hideContent(name, 'none');
					$('#' + divId, this.element).toggle();
				},
				
				hideContent: function (divName, excluded) {
					$('div[name=' + divName + ']', this.element).each(
						function(e) {
							if ($(e).attr('id') != excluded || excluded == 'none') {
								$(e).hide();
							}
						}
					);
				},
				
				isVisibleAnyContents: function (divName) {
					$('div[name=' + divName + ']', this.element).each(
						function(e) {
							if ($(e).isVisible()) {
								return true;
							}
						}
					);
					return false;
				}
	        });
	        $.ui.canvas_i.getter = [];	        
	})(jQuery);
});