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
 *  @version $Revision: 8093 $
 */




/**
 * This class creates a little menu with the scroll up and down options.
 */
function ScrollerToolBox() {

	this._body = null;
	this._scroller = null;

	this.initialize = function(tool, editor) {
    	this.tool = tool;
        this.editor = editor;
        this._body = editor.getBody();
		this.afterUpdateContent(null);
        this.editor.logMessage(_('ScrollerToolBox tool initialized'));
    };

	this._elementIsallowed = function(element) {
		if (!element['rngElement']) return false;
		var rngElement = element.rngElement;
		var allowed = (!rngElement.type.contains('apply')) && (rngElement.tagName != 'docxap');
		//allowed = allowed && element.getAttribute('editable') != 'no';
		return allowed;
	};

	this.beforeUpdateContent = function(options) {
		try {
			$('img', this._scroller).unbind('click');
			this._scroller.empty().remove();
		} catch(e) {
		}
		this._scroller = null;
	};

	/**
	 * Updates the events handlers after update the editor content
	 */
	this.afterUpdateContent = function(options) {

		// Important!
		this._body = this.editor.getBody();

		// TODO: Make images no editable...

		var imgUp = '<img id="scrollUp" src="'+window.url_root + '/xmd/icons/moveup.gif" alt="Subir elemento" title="Subir elemento">';
		var imgDown = '<img id="scrollDown" src="'+window.url_root + '/xmd/icons/movedown.gif" alt="Bajar elemento" title="Bajar elemento">';
		var html = '<div id="scroller">' + imgDown + imgUp + '</div>';

		// Append the menu to document body, it will be positioned later
		this._scroller = $(html, this._body).appendTo(this._body);

		// This doesn't works for images
		this._scroller.designMode = 'Off';
		this._scroller.hide();

		$('#scrollUp', this._scroller).click(this._moveUp.bind(this));
		$('#scrollDown', this._scroller).click(this._moveDown.bind(this));
	};

	/**
	 * Change the menu position
	 */
	this._moveScroller = function(elem) {

		var _elem = $(elem);
		var top = _elem.position().top + _elem.height();
		var left = _elem.position().left + _elem.width() - this._scroller.width();

		this._scroller.css({
			position: 'absolute',
			top: top+'px',
			left: left+'px'
		});

		this._scroller._xmlTarget = elem;
	};

	/**
	 * Shows the scroller menu
	 */
	this.onMouseOver = function(options) {
		if (!this._scroller || !this._elementIsallowed(options.selNode)) return;
		this._moveScroller(options.selNode);
		this._scroller.show().css({position: 'absolute', 'background-color': '#FFFFFF'});
	};

	/**
	 * Hides the scroller menu
	 */
	this.onMouseOut = function(options) {
		if (!this._scroller || !this._elementIsallowed(options.selNode)) return;

		var hide = true;
		try {
			var related = options.event.relatedTarget;
			hide = !['scrollUp', 'scrollDown'].contains(related.id);
		} catch (e) {
			//console.error(e);
		}
		if (!hide) return;

		this._scroller.hide();
	};

	/**
	 * Scrolls up an element
	 */
	this._moveUp = function(event) {
		var elem = this._scroller._xmlTarget.ximElement;
		if (elem.scrollUp()) {
			this.setActionDescription(_('Scroll up'));
			this.editor.updateEditor({caller: this});
		}
	};

	/**
	 * Scrolls down an element
	 */
	this._moveDown = function(event) {
		var elem = this._scroller._xmlTarget.ximElement;
		if (elem.scrollDown()) {
			this.setActionDescription(_('Scroll down'));
			this.editor.updateEditor({caller: this});
		}
	};

};

ScrollerToolBox.prototype = new XimdocToolBox();
