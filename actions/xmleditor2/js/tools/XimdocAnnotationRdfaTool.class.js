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
 *  @version $Revision: 8170 $
 */




/**
 * Annotate document.
 */

function XimdocAnnotationRdfaTool() {

	this._highlightStartTag = null;
	this._highlightEndTag = null;
	this._annotationDoc = null;

    this.initialize = function (editor) {
//    	console.log('ximdoc anotation rdfa');
        this.editor = editor;
        this.editor.logMessage(_('Annotation Rdfa tool initialized'));
    },
    
/*    this.updateState = function (options) {
    	console.log('options', options);
    },*/
    this.doAnnotate = function () {
//    	console.log('doAnnotate', this);
    	selection = this.editor.getSelection().parentElement();
//    	console.log(selection);
//    	$(selection).html('asdfasdfasdfasdfasdf');
    	
    	
    	
    } 
}

XimdocAnnotationRdfaTool.prototype = new XimdocTool();
