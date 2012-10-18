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


$('form#publication_form').submit(function () {
	$('#publishing_message').toggle();
	$('#form_layer').toggle();
});

function start_reading() {
	//var executer = new PeriodicalExecuter (read_log, 2);
	var t = XimTimer.getInstance();
	t.addObserver(read_log, 2000);
	t.start();
}

function read_log() {

	var url = window.url_root + '/xmd/loadaction.php?actionid=' + window.actionId + '&nodeid=' + window.nodeId + '&method=publication_progress';

	$.get(
		url,
		function(data, textStatus) {
			$('#div_log').html(data);
		}
	);
}
