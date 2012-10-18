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



/* Functions related with autoscrolling embebbed in Xedit */
$(function() {
	var intval = "";
	var myIframe;
	var dir = "";// FUTURE IMPROVEMENT: make this variable local
	// Up direction
	$('.scrollup').bind('mouseenter', function() {
		autoscrolling_start('up');
	});
	$('.scrollup').bind('mouseleave', autoscrolling_stop);

	// Down direction
	$('.scrolldown').bind('mouseenter', function() {
		autoscrolling_start('down');
	});
	$('.scrolldown').bind('mouseleave', autoscrolling_stop);

	// executes when the mouse is hover the div#autoscroll in Xedit
	function autoscrolling_start(direccion) {
		dir = direccion;
		if (intval == "") {
			intval = setInterval(playAutoScroll, 100);
		} else {
			autoscrolling_stop();
		} // stop scrolling
	}
	// executes when the mouse is out of the div#autoscroll in Xedit
	function autoscrolling_stop() {
		if (intval != "") {
			window.clearInterval(intval); // stoping scrolling
			intval = ""; // restarting variables
			dir = "";
		}
	}
	// executes the scroll on the iframe
	function playAutoScroll() {
		myIframe = document.getElementById('kupu-editor'); // rescue the iframe
															// DOM object
		if (dir == "down") {
			myIframe.contentWindow.scrollBy(0, 10);
		} // 5 pixels down
		else {
			myIframe.contentWindow.scrollBy(0, -10);
		} // 5 pixels up
	}
	/*
	 * FUTURE IMPROVEMENT TO COMPLETE function goToTop(){ myIframe =
	 * document.getElementById('kupu-editor'); //rescue the iframe DOM object
	 * myIframe.contentWindow.scrollTo(0,0); } function goToBottom(){ myIframe =
	 * document.getElementById('kupu-editor'); //rescue the iframe DOM object
	 * var myheight=myIframe.contentWindow.innerHeight;
	 * myIframe.contentWindow.scrollTo(0,900); }
	 */

});
