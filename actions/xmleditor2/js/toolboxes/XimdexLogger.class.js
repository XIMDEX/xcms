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
 *  @version $Revision: 8537 $
 */

var XimdexLogger = Object.xo_create(FloatingToolBox, {
	/* writes messages to a debug tool and throws errors */

	_init: function(options) {

		XimdexLogger._construct(this, options);

		this.maxlength = options.maxlength;
		this.active = true;	// TODO: parametrize kupu_config.xml
		this.activateButton = null;

        	$('#kupu-toolbox-debug').unbind().remove();
        	$(this.element).attr('id', 'kupu-toolbox-debuglog');

        	this.initialize(options.tool, options.editor);
	},

	initialize: function(tool, editor) {

		this.buttons = {
			Desactivar: this.setActive.bind(this),
			Limpiar: this.updateButtonHandler.bind(this)
		};

		AttributesToolBox._super(this, 'initialize', tool, editor);

		this.setTitle(_('Debug log'));
		this.setOption({
			title: _('Debug log'),
			width: 480,
			height: 300
		});
	},

	setActive: function(active) {

		if (!this.activateButton) {
			this.activateButton = $(this.element).parent().find('button').filter(function() {
	    			var text = $(this).html();
	    			return text == 'Desactivar' || text == 'Activar';
	    		})
//	    		.html(this.active ? 'Desactivar' : 'Activar')
	    		.get(0);
		}

		this.active = Object.isBoolean(active) ? active.valueOf() : !this.active;
		$(this.activateButton).html(this.active ? 'Desactivar' : 'Activar');
	},

	updateButtonHandler: function(event) {
		$(this.element).empty();
	},

	log: function(message, severity) {

		if (!this.active || Object.isEmpty(this.element)) return;

		/* log a message */
		if (this.maxlength) {
		    if (this.element.childNodes.length > this.maxlength - 1) {
	        	this.element.removeChild(this.element.childNodes[0]);
		    }
		}
		var now = new Date();
		var time = this.formatTime(now);

		var div = document.createElement('div');
		var span = document.createElement('span');
		var subtext = document.createTextNode (time);
		//var text = document.createTextNode(window.i18n_message_catalog.acents(message));

		span.appendChild(subtext);
		//div.appendChild(text);
		div.innerHTML = window.i18n_message_catalog.acents(message);
		div.appendChild(span);

		var firstChild = $('div', this.element)[0];
		if (!firstChild) {
			this.element.appendChild(div);
		} else {
			this.element.insertBefore(div, firstChild);
		}
	},

	formatTime: function(time) {
		var hours = (time.getHours() < 10) ? '0' + time.getHours() : time.getHours();
		var minutes = (time.getMinutes() < 10) ? '0' + time.getMinutes() : time.getMinutes();
		var seconds = (time.getSeconds() < 10) ? '0' + time.getSeconds() : time.getSeconds();
		return hours + ':' + minutes + ':' + seconds;
	}

});

