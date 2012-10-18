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


//<script>
/*
 * ScrollButton
 *
 * This script was designed for use with DHTML Menu 4
 *
 * This script was created by Erik Arvidsson
 * (http://webfx.eae.net/contact.html#erik)
 * for WebFX (http://webfx.eae.net)
 * Copyright 2002
 * 
 * For usage see license at http://webfx.eae.net/license.html	
 *
 * Version: 1.0
 * Created: 2002-05-28
 * Updated: 
 *
 */

////////////////////////////////////////////////////////////////////////////////////
// scroolButtonCache
//

var scrollButtonCache = {
	_count:		0,
	_idPrefix:	"-scroll-button-cache-",
	
	getId:	function () {
		return this._idPrefix + this._count++;
	},
	
	remove:	function ( o ) {
		delete this[ o.id ];
	}
};

function ScrollButton( oEl, oScrollContainer, nDir ) {
	this.htmlElement = oEl;
	this.scrollContainer = oScrollContainer;
	this.dir = nDir;
	
	this.id = scrollButtonCache.getId();
	scrollButtonCache[ this.id ] = this;
	
	this.makeEventListeners();
	this.attachEvents();
}

ScrollButton.scrollIntervalPause = 100;
ScrollButton.scrollAmount = 18;

ScrollButton.prototype.startScroll = function () {
	this._interval = window.setInterval(
		"ScrollButton.eventListeners.oninterval(\"" + this.id + "\")",
		ScrollButton.scrollIntervalPause );
};

ScrollButton.prototype.endScroll = function () {
	if ( this._interval != null ) {
		window.clearInterval( this._interval );
		delete this._interval;
	}
};

ScrollButton.prototype.makeEventListeners = function () {
	if ( this.eventListeners != null )
		return;

	this.eventListeners = {
		onmouseover:	new Function( "ScrollButton.eventListeners.onmouseover(\"" + this.id + "\")" ),
		onmouseout:		new Function( "ScrollButton.eventListeners.onmouseout(\"" + this.id + "\")" ),
		onbeforeunload:	new Function( "ScrollButton.eventListeners.onbeforeunload(\"" + this.id + "\")" )		
	};
};

ScrollButton.prototype.attachEvents = function () {
	if ( this.eventListeners == null )
		return;

	this.htmlElement.attachEvent( "onmouseover", this.eventListeners.onmouseover );
	this.htmlElement.attachEvent( "onmouseout", this.eventListeners.onmouseout );
	window.attachEvent( "onbeforeunload", this.eventListeners.onbeforeunload );
};

ScrollButton.prototype.detachEvents = function () {
	if ( this.eventListeners == null )
		return;

	this.htmlElement.detachEvent( "onmouseover", this.eventListeners.onmouseover );
	this.htmlElement.detachEvent( "onmouseout", this.eventListeners.onmouseout );
	window.detachEvent( "onbeforeunload", this.eventListeners.onbeforeunload );
};

ScrollButton.prototype.destroy = function () {
	this.endScroll();
	this.detachEvents();
	
	this.htmlElement = null;
	this.scrollContainer = null;
	this.eventListeners = null;
	
	scrollButtonCache.remove( this );
};

ScrollButton.eventListeners = {
	onmouseover:	function ( id ) {
		scrollButtonCache[id].startScroll();
	},
	
	onmouseout:		function ( id ) {
		scrollButtonCache[id].endScroll();
	},
	
	oninterval:		function ( id ) {
		var oThis = scrollButtonCache[id];
		switch ( oThis.dir ) {
			case 8:
				oThis.scrollContainer.scrollTop -= ScrollButton.scrollAmount;
				break;
			
			case 2:
				oThis.scrollContainer.scrollTop += ScrollButton.scrollAmount;
				break;
		
			case 4:
				oThis.scrollContainer.scrollLeft -= ScrollButton.scrollAmount;
				break;
			
			case 6:
				oThis.scrollContainer.scrollLeft += ScrollButton.scrollAmount;
				break;
		}
	},
	
	onbeforeunload:	function ( id ) {
		scrollButtonCache[id].destroy();
	}
};