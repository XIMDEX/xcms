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


function region( p, sTemp, sPath, sOptions, index )
{
	if( region.prototype.regionInitd == undefined )
	{
		this.initProto = _regionInitProto;
		this.initProto( "region" );
		region.prototype.regionInitd = true;
	}
//alert('edx_spath -> ' + sPath);
	// init base class vars
	this.edxnode( p, sTemp, sPath, sOptions, index );

	// init class vars
	this.nodeClass = "region";
	this.type = "region";
	this.saveBorderTop = null;
	this.saveBorderLeft = null;
	this.saveBorderRight = null;
	this.saveBorderBottom = null;
	this.saveBackground = null;
	this.viewState = null;
	this.displayLinkNode = null;
}


//
//						_regionInitProto
//
function _regionInitProto( sClass )
{
	// call base class proto
	this.initProto = _edxnodeInitProto;
	this.initProto( sClass );
	
	// install our methods
	eval( sClass + ".prototype.region = region; " +
	      sClass + ".prototype.xhtml = _regionXHTML; " +
	      sClass + ".prototype.select = _regionSelect; " +
	      sClass + ".prototype.unselect = _regionUnselect; " +
	      sClass + ".prototype.onXmlNodeChange = _regionOnXmlNodeChange; " +
	      sClass + ".prototype.canDelete = _regionCanDelete; " +
	      sClass + ".prototype.permitChildDelete = _regionPermitChildDelete; " +
	      sClass + ".prototype.cleanup = _regionCleanup" );
}	

//
//						_regionXHTML
//
var sClass;
var my_rowspan;
var my_colspan;
var my_clase;
var my_id;
function _regionXHTML( oFrag )
{

	var oHtml;
	with( this )
	{

		var v = root.getView();

		// Search node to edit
		var editnode = getXmlNode();

		// Checking if it is linked with an attribute
		if( this.displayLink != undefined )
		{
			var n = editnode.selectSingleNode( "@" + displayLink );
			if( n != null )
			{
				viewState = n.value;
				displayLinkNode = n;
				root.watchChanges( n, this );
			}
		}
		if( viewState == null )
			{
			viewState = v.getTemplateDefaultDisplayName( getTemplate() );
			}
		oHtml = v.getTemplateHtmlByName( getTemplate(), viewState );

		if( oHtml == null )
		{
			err( "Error: no existe la plantilla en XHTML para la vista " + viewState );
			return;
		}

		if (navegador == "firefox15")
			{
				for (b = 0; b < oHtml.childNodes.length; b++)
					{
					if (is_ignorable(oHtml.childNodes[b]))
						{
						oHtml.removeChild(oHtml.childNodes[b]);
						
						}
					}
			}
		// clone the template node
		var oXml = oHtml.cloneNode( true );
		

	//alert(serializa_me(oXml));
		// Searching children
		if (navegador == "ie")
			{
			var children = oXml.selectNodes( "//*[@edxtemplate != '']" );
			}
		else
			{
			//revise this
			var children = oXml.selectNodes( './/*[@edxtemplate != ""]' );			
			
			}		
		var i;

		
		
		for( i = 0; i < children.length; i++ )
		{
			
			
			child = children[i];

			//alert('i -> ' + i);
			var sTemplate = utilGetXmlAttribute( child, "edxtemplate" );

			var sPath = utilGetXmlAttribute( child, "edxpath" );
			//alert('sPathrl ' + sPath);
			
			// Catching class of "view" label
			
			var sClass = utilGetXmlAttribute( child, "class" );
			// Catching width of "view" label
			var sAncho = utilGetXmlAttribute( child, "width" );
			// Catching alingment
			var sAlin = utilGetXmlAttribute( child, "align" );
			// Catching rowspan
			var sRowspan = utilGetXmlAttribute( child, "rowSpan" );
			var sColspan = utilGetXmlAttribute( child, "colSpan" );
			
//alert('sTemplate: ' + sTemplate);
		if (editnode){
			//If edited node is a valid label, checks if there is a XML label which corresponds
			
			if (editnode.tagName != undefined) {
				//Checking style
				my_clase = editnode.getAttribute( "clase" );
				//Checking width
				var my_ancho = editnode.getAttribute( "ancho" );
				//Checking alingment
				var my_alin = editnode.getAttribute( "alin" );
				//Checking rowspan
				my_rowspan = editnode.getAttribute( "filas" );
				
				my_colspan = editnode.getAttribute( "columnas" );
				}
			}
			if (variable_estilo){
				if (variable_estilo != "" && sClass=="@clase"){
						child.setAttribute( 'class', variable_estilo );
				}
			}
			if (my_clase != null && sClass=="@clase"){
				child.setAttribute( 'class', my_clase );
				my_clase="";
			}
			if (my_ancho != null && sAncho=="@ancho"){
				child.setAttribute( 'width', my_ancho );
				
				}
			if (my_alin != null && sAlin=="@alin"){
				child.setAttribute( 'align', my_alin );
				}
			if (my_rowspan != null && sRowspan=="@filas"){
				child.setAttribute( 'rowSpan', my_rowspan );
				}
			if (my_colspan != null && sColspan=="@columnas"){
				child.setAttribute( 'colSpan', my_colspan );
				}
			
			//alert(serializa_me(child));
			
			var sOptions = utilGetXmlAttribute( child, "edxoptions" );

			//alert(sOptions);
			var obj = factory( sTemplate, sPath, sOptions );


			// install backlinks to edxnode object structure
			obj.setHtmlAttributes( child );			
			
			// get its xhtml
			obj.xhtml( child  );
			
			if (my_rowspan != null && my_colspan != null){
			if (children[i].tagName == "td"){
				children[i].setAttribute( 'rowSpan', my_rowspan );
				children[i].setAttribute( 'colSpan', my_colspan );
				children[i].setAttribute( 'class', my_clase );
				}
			}
		}
		// if parent provided, append children
		if( oFrag != null )
		{
			for( i = 0; i < oXml.childNodes.length; i++ )
			{
				var oTmp = oXml.childNodes[i].cloneNode( true );
				oFrag.appendChild( oTmp );
			}
		}
		// done, return the XML fragment
		return oXml;
	}
}

//
//						_regionOnXmlNodeChange
//
//	Simply reload ourselves from the ground up.
//
function _regionOnXmlNodeChange( sender )
{
	// ignore updates from ourself
	if( sender == this )
		return;

	// cleanup current watcher		
	this.root.unwatchChanges( this.displayLinkNode, this );
	// reload ourselves
	this.load();
}

//
//						_regionSelect
//
//	Selects the current region, deselecting any previously selected region.
//
function _regionSelect()
{
	with( this )
	{

		// do toggle if previously selected was us
		if( root.selectedRegion == this )
		{
			unselect();

			if (navegador == "ie")
			{
				root.hobj.edxselectionchange.fire();
			}
			else
			{
				selChange();
			}
			return;
		}
		
		// unselect any previous region
		if( root.selectedRegion != null )
			root.selectedRegion.unselect();
		
		if( hobj.tagName != "TR" )
		{
			// capture default border state
			saveBorderTop = hobj.style.borderTop;
			saveBorderLeft = hobj.style.borderLeft;
			saveBorderRight = hobj.style.borderRight;
			saveBorderBottom = hobj.style.borderBottom;
				
			// draw the selection border
			hobj.style.border = "1px dashed blue";
		}
		else
		{
			saveBackground = hobj.style.backgroundColor;
			hobj.style.backgroundColor = "#e0e0e0";
		}
		root.selectedRegion = this;
		

		if (navegador == "ie")
			{
			root.hobj.edxselectionchange.fire();
			}
		else
			{
			selChange();
			}
		
	}
}

//
//						_regionUnselect
//
//	Deselects the current region, restoring previous display attribs.
//
function _regionUnselect()
{
	with( this )
	{
		if( hobj.tagName != "TR" )
		{
			hobj.style.borderTop = saveBorderTop;
			hobj.style.borderLeft = saveBorderLeft;
			hobj.style.borderRight = saveBorderRight;
			hobj.style.borderBottom = saveBorderBottom;
		}
		else
		{
			hobj.style.backgroundColor = saveBackground;
		}
		root.selectedRegion = null;
		//read_TAX2(null);
		//read_TAX(null);
	}
 
}

//
//						_regionCanDelete
//
function _regionCanDelete()
{
	with( this )
	{
		// non-splittable regions are permanent
		if( !allowSplit )
			return false;
		
		// see what parent thinks
		if( !parent.permitChildDelete() )
			return false;
		
		// guess we're good to go
		return true;
	}
}

//
//						_regionPermitChildDelete
//
function _regionPermitChildDelete()
{
	if( !this.allowSplit )
		return false;
	return true;
}

//
//						_regionCleanup
//
//	Called when the node is destoyed.
//
function _regionCleanup()
{
	if( this == this.root.selectedRegion )
	{
		this.root.selectedRegion = null;
	}
	if( this.displayLinkNode != null )
	{
		this.root.unwatchChanges( this.displayLinkNode, this );
	}
	this.edxnodeCleanup();
}
