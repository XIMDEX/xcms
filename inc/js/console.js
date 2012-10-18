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

(function() {

	/**
	 * Basic implementation of Firebug API,
	 * just for obtain some compatibility with IE.
	 */

	// -- DEBUG --
//	k = {};
//	var window = k;

	if (!Object.isObject(window.console)) {
		window.console = {
		
			LEVEL_DEBUG: 'DEBUG',
			LEVEL_LOG: 'LOG',
			LEVEL_INFO: 'INFO',
			LEVEL_WARN: 'WARN',
			LEVEL_ERROR: 'ERROR',
			
			_getConsole: function() {
				var container = document.getElementById('console');
				var area = document.getElementById('console-area');
				if (!area) {
					container = document.createElement('div');
					var button = document.createElement('button');
					button.appendChild(document.createTextNode('Hide'));
					button.onclick = function() {
						container.style.display = 'none';
					}
					container.appendChild(button);
					button = document.createElement('button');
					button.appendChild(document.createTextNode('Clear'));
					button.onclick = function() {
						var area = document.getElementById('console-area');
						while (area.childNodes.length > 0) {
							area.removeChild(area.childNodes[0]);
						}
					}
					container.appendChild(button);
					container.id = 'console';
					container.style.border = '1px solid #444444';
					container.style.backgroundColor = '#FFF8B1';
					container.style.position = 'absolute';
					container.style.width = '100%';
					container.style.height = '200px';
					container.style.top = '0px';
					container.style.overflow = 'hidden';
					container.style.padding = '8px';
					var body = document.getElementsByTagName('body')[0];
					body.appendChild(container);
					area = document.createElement('div');
					area.id = 'console-area';
					area.style.border = '1px solid #444444';
					area.style.backgroundColor = '#FFFFFF';
					area.style.position = 'absolute';
					area.style.width = '96%';
					area.style.top = '35px';
					area.style.bottom = '8px';
					area.style.left = 'auto';
					area.style.right = 'auto';
					area.style.overflow = 'auto';
					area.style.padding = '8px';
					container.appendChild(area);
				}
				container.style.display = '';
				return area;
			},
			
			_createLine: function(level, message) {
				
				// TODO: images?
				var imgContainer = document.createElement('div');
				imgContainer.style.width = '22px';
				imgContainer.style.height = '22px';
				imgContainer.style.position = 'relative';
				var float = !Object.isUndefined(imgContainer.style.cssFloat) ? 'cssFloat' : 'styleFloat';
				imgContainer.style[float] = 'left';
				var img = document.createElement('img');
				
				switch (level) {
					case this.LEVEL_DEBUG:
						break;
					case this.LEVEL_LOG:
						break;
					case this.LEVEL_INFO:
						img.src = 'icons/dialog-information.png';
						imgContainer.appendChild(img);
						break;
					case this.LEVEL_WARN:
						img.src = 'icons/emblem-important.png';
						imgContainer.appendChild(img);
						break;
					case this.LEVEL_ERROR:
						img.src = 'icons/dialog-error.png';
						imgContainer.appendChild(img);
						break;
				}
				
				var line = document.createElement('div');
				line.style.borderBottom = '1px solid #cccccc';
				line.style.paddingTop = '5px';
				line.style.fontFamily = 'Sans';
				line.style.fontSize = '11pt';
				line.style.position = 'relative';
				line.style.clear = 'both';
				line.style.width = '99%';
				if (float == 'styleFloat') line.style.height = '24px';
				line.appendChild(imgContainer);
				
				for (var i=0, l=message.length; i<l; i++) {
					
					var _content = document.createElement('div');
					_content.innerHTML = message[i];
					_content._originalContent = message[i]._originalContent;
					_content.className = "console-content";
					_content.style.marginLeft = '30px';
					_content.style.cursor = 'default';
					_content.style.position = 'relative';
					_content.style[float] = 'left';
					line.appendChild(_content);
					
					if (Object.isObject(_content._originalContent) || Object.isArray(_content._originalContent)) {
						if (_content.attachEvent) {
							_content.attachEvent('onclick', this._onClickCb.bind(this));
						} else if (_content.addEventListener) {
							_content.addEventListener('click', this._onClickCb.bind(this), false);
						}
					}
				}
				
				return line;
			},
			
			_onClickCb: function(event) {
				var target = event.target || event.srcElement;
				// TODO: show object attributes...
//				var msg = '';
//				for (var o in target) {
//					msg += 'object.%s\t%s\n'.printf(o, typeof(target));
//				}
//				alert(msg);
			},
			
			_write: function() {
				return;
				var args = $A(arguments);
				var level = args.shift();
				var k = this._getConsole();
				var message = [];
				for (var i=0, l=args.length; i<l; i++) {
					var originalContent = args[i];
					var parsedContent = new String(this._parseContent(originalContent));
					parsedContent._originalContent = originalContent;
					message.push(parsedContent);
				};
				k.appendChild(this._createLine(level, message));
				k.scrollTop = k.scrollHeight; 
			},
			
			_parseContent: function(content) {
				if (Object.isFunction(content)) {
					return 'function()';
				} else if (Object.isObject(content)) {
					return content.toString();
				} else if (Object.isArray(content)) {
					return '[%s]'.printf(content.toString());
				} else if (Object.isUndefined(content)) {
					return 'undefined';
				} else if (content === null) {
					return 'null';
				}
				return content;
			},
			
			debug: function() {
				var args = $A(arguments);
				args.splice(0, 0, this.LEVEL_DEBUG);
				this._write.apply(this, args);
			},
			log: function() {
				var args = $A(arguments);
				args.splice(0, 0, this.LEVEL_LOG);
				this._write.apply(this, args);
			},
			info: function() {
				var args = $A(arguments);
				args.splice(0, 0, this.LEVEL_INFO);
				this._write.apply(this, args);
			},
			warn: function() {
				var args = $A(arguments);
				args.splice(0, 0, this.LEVEL_WARN);
				this._write.apply(this, args);
			},
			error: function() {
				var args = $A(arguments);
				args.splice(0, 0, this.LEVEL_ERROR);
				this._write.apply(this, args);
			},
			assert: function() {
		
			},
			time: function() {
			},
			timeEnd: function() {
			}
		}
	}

})();
