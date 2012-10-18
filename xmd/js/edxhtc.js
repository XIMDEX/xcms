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


var eobj;		// ref to edxnode class object
var rootid;		// root ID
//
//						doInit
//
function doInit()
{
	// verify IE6 client
	var version = window.navigator.appVersion;
	if( version.indexOf( "MSIE 6.0" ) < 0 )
	{
		err( "Sorry, you must have IE 6.0 to run this behavior." );
		return;
	}
	
	if( edxtemplate == null )
	{
		err( "Missing edxtemplate on root node: all EDX elements must specify a edxtemplate value." );
		return;
	}
	
	if( edxpath == null )
		edxpath = "/";
		// instantiate personality object
	this.eobj = new root( null, this.edxtemplate, this.edxpath );
	
	// associate with HTML since we're already here
	this.eobj.associate( this );
	this.edxid = eobj.id;
	rootid = this.id;
	// see if we're ready right away to load up
	eobj.loadDocs();
	
    // attach onpropertychange
    attachEvent("onpropertychange", doPropChange);
}

//
//						doPropChange
//
function doPropChange()
{
    var propertyName = window.event.propertyName;

	//
	//  Detach the onpropertychange event to prevent it from firing while
	//  the changes are handled
	//
	detachEvent("onpropertychange", doPropChange);

	switch(propertyName)
	{
		case "xmlurl":
			// instantiate fresh personality object
			this.innerHTML = "";
			this.eobj = new root( null, this.edxtemplate, this.edxpath );

			// load new docs
			eobj.associate( this );
			eobj.loadDocs();
			break;

		case "viewurl":
			eobj.loadDocs();
			break;

		case "edxpath":
			err( "edxpath property cannot be modified dynamically on an editnode" );
			break;
			
		default:
			break;
	}

	//  Re-attach the onpropertychange event
	attachEvent("onpropertychange", doPropChange);
}

//
//						getXmlNode
//
//	Gets the node in the XML tree assoc'd with this node.
//
function getXmlNode()
{
	return eobj.getXmlNode();
}

//
//						getXml
//
//	Returns the document XML as a string
//
function getXml()
{
	var node = this.eobj.getEditDocRoot();
	if( node != null )
		return node.xml;
	else
		return "";
}

//
//						enableIcons
//
function enableIcons( flg )
{
	var i;
	for( i = 0; i < this.all.length; i++ )
	{
		var h = this.all(i);
		try
		{
			if( h.edxtemplate != null && h.edxtemplate.substr(0,11) == "widget:icon" )
			{
				h.style.display = flg ? "block" : "none";
			}
		}
		catch(e) {}
	}
}

//
//						canMoveUp
//
//	Looks for selected item and checks to see if it can move up.
//
function canMoveUp()
{
	if( eobj.selectedRegion != null )
	{
		r = eobj.selectedRegion;
		if( r.parent == null )	// trap for root node
			return false;
		if( r.parent.type == "container" )
		{
			var b = r.parent.canMoveUp( r );
			return b;
		}
	}
	return false;
}

//
//						canMoveDown
//
//	Looks for selected item and checks to see if it can move down.
//
function canMoveDown()
{
	if( eobj.selectedRegion != null )
	{
		r = eobj.selectedRegion;
		if( r.parent == null )	// trap for root node
			return false;
		if( r.parent.type == "container" )
		{
			return r.parent.canMoveDown( r );
		}
	}
	return false;
}

//
//						moveUp
//
//	Looks for selected item and checks to see if it can move up.
//
function moveUp()
{
	if( eobj.selectedRegion != null )
	{
		r = eobj.selectedRegion;
		if( r.parent.type == "container" )
		{
			r.parent.moveUp( r );
		}
	}
}

//
//						moveDown
//
//	Looks for selected item and checks to see if it can move down.
//
function moveDown()
{
	if( eobj.selectedRegion != null )
	{
		r = eobj.selectedRegion;
		if( r.parent.type == "container" )
		{
			r.parent.moveDown( r );
		}
	}
}

//
//						canApplyTag
//
//	Sees if we can apply spec'd tag to all regions of current field selection.
//
function canApplyTag( sTag )
{
	return eobj.canApplyTag( sTag );
}


//
//						applyTag
//
//	Wraps current field selection in spec'd tag.
//
function applyTag( sTag )
{
	eobj.applyTag( sTag );
	
}

//
//						edxCommand
//
function edxCommand( sCmd )
{
	switch( sCmd )
	{
	case "Bold":
	case "Underline":
	case "Italic":
	case "CreateLink":
		var ff = eobj.focusField;
		if( ff != null && ff.fieldType == "rich" )
		{
			ff.hobj.focus();
			window.document.execCommand( sCmd );
		}
		break;
	default:
		break;
	}
}

//
//						canUndo
//
function canUndo()
{
	return eobj.getXmlManager().canUndo();
}

//
//						canRedo
//
function canRedo()
{
	return eobj.getXmlManager().canRedo();
}


//
//						undo
//
function undo()
{
	eobj.getXmlManager().undo();
}

//
//						redo
//
function redo()
{
	eobj.getXmlManager().redo();
}

//
//						clearHistory
//
function clearHistory()
{
	eobj.getXmlManager().clearHistory();
}