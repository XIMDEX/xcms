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

function view( viewroot )
{
	this.docroot = viewroot;

	// Loading first view of document
	this.currentView = viewroot.childNodes[0];



	if( this.currentView == null )
	{
		err( "No se encontró la vista de edición del documento" );
	}
	this.currentViewName = this.currentView.getAttribute('uiname');
	// maintain cache of container matches and maps
	this.aContainers = new Array();
	this.aContainerMaps = new Array();

	// install methods
	this.getTemplate = _viewGetTemplate;
	this.getTemplateType = _viewGetTemplateType;
	this.getTemplateHtmlByName = _viewGetTemplateHtmlByName;
	this.getTemplateDefaultDisplayName = _viewGetTemplateDefaultDisplayName;
	this.getTemplateName = _viewGetTemplateName;
	this.getDisplays = _viewGetDisplays;
	this.getContainerMatches = _viewGetContainerMatches;
	this.getContainerMap = _viewGetContainerMap;
	this.compileContainerMap = _viewCompileContainerMap;

}


//
//						_viewGetTemplate
//
//	Looks up template node for specified template
//
function _viewGetTemplate( sTemplate )
{
	var node = this.currentView.selectSingleNode( "edx:template[@name = '" + sTemplate + "']" );

	if( node == null )
	{
		// look for common definition
		node = this.docroot.selectSingleNode( "/edx:editviews/edx:common/edx:template[@name = '" + sTemplate + "']" );
	}
	if( node == null )
	{
		err( "No hay informaci&oacute;n para la plantilla " + sTemplate + " en " + this.currentViewName + " ." );
	}
	return node;
}

//
//						_viewGetTemplateType
//
//	Looks up type of this template.
//
function _viewGetTemplateType( oTemplateNode )
{
		return oTemplateNode.selectSingleNode( "@type" ).nodeValue;

}

//
//						_viewGetTemplateDefaultDisplayName
//
function _viewGetTemplateDefaultDisplayName( oTemplateNode )
{
	var n = oTemplateNode.selectSingleNode( "edx:xhtml" );

	if( n == null )
	{
		err( "No se pudo obtener la plantilla por defecto" );
		return;
	}
	var s = n.getAttribute( "display" );
	if( s == null )
		return "default";
	else
		return s;
}

//
//						_viewGetTemplateHtmlByName
//
//	Looks up template node for specified template name
//
function _viewGetTemplateHtmlByName( oTemplateNode, sName )
{
	if( sName == null || sName == "default"  || sName == "Vista por defecto")
		return oTemplateNode.selectSingleNode( "edx:xhtml" );
	else
		return oTemplateNode.selectSingleNode( "edx:xhtml[@display = '" + sName + "']" );
}

//
//						_viewGetTemplateName
//
//	Looks up the display name for the spec'd template.
//
function _viewGetTemplateName( oTemplateNode )
{
	var node = oTemplateNode.selectSingleNode( "@uiname" );
	if( node == null )
	{
		// fall back to just the template name itself
		node = oTemplateNode.selectSingleNode( "@name" );
	}
	if( node != null )
		{
		return node.nodeValue;
		}
	else
	{
		err( "Error: plantilla sin nombre" );
		return "";
	}
}

//
//						_viewGetDisplays
//
//	Obtaining a list of template views (default view, properties, etc.)
//
function _viewGetDisplays( oTemplateNode )
{
	var nodes = oTemplateNode.selectNodes( "edx:xhtml" );
	var a = new Array();
	var i;
	var index = 0;

	for( i = 0; i < nodes.length; i++ )
	{
		var n = nodes[i].selectSingleNode( "@display" );
		if( n != null )
			{
			a[index++] = n.nodeValue;
			}
	}
	return a;
}

//
//						_viewGetContainerMatches
//
//	Returns an array with elements found in a container
//
function _viewGetContainerMatches( sTemplate )
{
	with( this )
	{
		// check for cached copy
		if( aContainers[sTemplate] != undefined )
			return aContainers[sTemplate];


		var oTemplateNode = getTemplate( sTemplate );
		if( oTemplateNode == null )
		{
			err( "getContainerMatches: no se encontr&oacute;n plantilla para '" + sTemplate + "'" );
			return null;
		}

		var nodes = oTemplateNode.selectNodes( "edx:match" );

		var a = new Array();
		var i;

		for( i = 0; i < nodes.length; i++ )
		{
			var sTag = nodes[i].getAttribute( "element" );

			if( sTag != null )
			{

				for (b = 0; b < nodes[i].childNodes.length; b++)
					{
					if (is_ignorable(nodes[i].childNodes[b]))
						{
						nodes[i].removeChild(nodes[i].childNodes[b]);
						}
					}

				// get template name from XHTML node
				if( nodes[i].childNodes.length != 1 )
				{
//					err( "getContainerMatches: a match must contain one and only one HTML node inside the match spec, tag = " + sTag );
					//err( "getContainerMatches: se encontró más de una especificación XHTML para la plantilla:  " + sTag );
					//return null;
				}
				var sMatchTemplate = nodes[i].childNodes[0].getAttribute( "edxtemplate" );
				if( sMatchTemplate == null )
				{
					//err( "getContainerMatches: no existe la propiedad edxtemplate la plantilla " + sTag );
					//return null;
				}

				// got candidate, check it's template for XML insert fragment
				var oins = this.currentView.selectSingleNode( "edx:template[@name='" + sMatchTemplate + "']" );

				if( oins != null )
				{

					// got a fragment?
					if( oins.selectSingleNode( "edx:insert" ) != null )
					{
						var sUI = oins.getAttribute( "uiname" );

						if( sUI == null )
							sUI = sTag;

						// create a full match spec
						var oMatch = new containerMatch( sTag, sMatchTemplate, sUI, oins );
						a[a.length] = oMatch;

					}
				}
			}
		}

		// preserve for future
		aContainers[sTemplate] = a;
		return a;
	}
}

//
//						_viewGetContainerMap
//
//	Looks for or builds a tag map for spec'd container template.
//
function _viewGetContainerMap( sTemplate )
{
	with( this )
	{
		if( aContainerMaps[sTemplate] == undefined )
			compileContainerMap( sTemplate );
		return aContainerMaps[sTemplate];
	}
}

//
//						_viewCompileContainerMap
//
//	Compiles a tag map for spec'd container template.
//
function _viewCompileContainerMap( sTemplate )
{
	with( this )
	{
		// look up template
		var oTemp = getTemplate( sTemplate );
		if( oTemp == null )
			return;

		// see what type we have
		var sType = getTemplateType( oTemp );
		if( sType == "container" )
		{
			// parse container match contents
			var nodes = oTemp.selectNodes( "edx:match" );

			// make sure we have some matches
			if( nodes.length == 0 )
			{
				// pretty dumb container, but maybe it's a development stub, no error
				aContainerMaps[sTemplate] = "*";	// show no sub-containment
				return;
			}

			// put an empty map in place to keep us from trying to compile ourselves
			var a = new Array();
			aContainerMaps[sTemplate] = a;

			// parse matched element list
			var i;
			for( i = 0; i < nodes.length; i++ )
			{
				var n = nodes[i];

				for (b = 0; b < n.childNodes.length; b++)
					{
						if (is_ignorable(n.childNodes[b]))
							{
							n.removeChild(n.childNodes[b]);
							}
						}
				var sTag = n.getAttribute( "element" );
				if( sTag == null || sTag == "" )
				{
					err( "Bad match spec in container '" + sTemplate + "'.  Must specify element to match." );
					continue;
				}

				// verify we see one and only one node of XHTML inside the match
				if( n.childNodes.length != 1 )
				{
					err( "Bad XHTML spec for match on tag '" + sTag + "'.  Must contain one and only one node of XHTML." );
					continue;
				}

				// get the XHTML node for this match
				var oXhtml = n.childNodes[0];

				// get the template and options (if spec'd)
				var sTmp = oXhtml.getAttribute( "edxtemplate" );
				if( sTmp == null || sTmp == "" )
				{
					// must not be a "live" node, just show as end of line
					a[sTag] = "*";
					continue;
				}

				// figure out if splittable
				var bSplit;
				if( sTmp.substr( 0, 6 ) == "field:" )
				{
					a[sTag] = "*";
					continue;
				}
				else if( sTmp.substr( 0, 7 ) == "widget:" )
				{
					a[sTag] = "*";
					continue;
				}
				else if( getTemplateType( getTemplate( sTmp ) ) == "container" )
					bSplit = true;
				else
					bSplit = false;

				// see if explicit options say otherwise
				var sOptions = oXhtml.getAttribute( "edxoptions" );
				if( sOptions != null && sOptions != "" )
				{
					var aOps = utilParseOptions( sOptions );
					var j;
					for( j = 0; j < aOps.length; j++ )
					{
						if( aOps[j].name == "allow-split" && aOps[j].value == "true" )
						{
							bSplit = true;
							break;
						}
					}
				}

				// if it's not splittable, end of the line
				if( !bSplit )
				{
					a[sTag] = "*";
					continue;
				}

				// descend into it
				getContainerMap( sTmp );
				a[sTag] = aContainerMaps[sTmp];
			}

			// note: our map is already in place, we're done
		}
		else
		{
			// must be wrapper region, look inside default XHTML for a container
			var oXhtml = getTemplateHtmlByName( oTemp, null );
			if( oXhtml == null )
				return;

			// get all nodes with edxtemplates
			var nodes = oXhtml.selectNodes( ".//*[@edxtemplate != '']" );
			var i;

			// look for a nested container
			var sCont = null;
			for( i = 0; i < nodes.length; i++ )
			{
				var n = nodes[i];
				var sTemp = n.getAttribute( "edxtemplate" );
				if( sTemp.substr(0,6) == "field:" || sTemp.substr(0,7) == "widget:" )
					continue;

				var oTmp = getTemplate( sTemp );
				if( oTmp == null )
				{
					err( "No se encontr&oacute; plantilla para '" + sTemp + "'" );
					return;
				}
				if( getTemplateType( oTmp ) == "container" )
				{
					if( sCont != null )
					{
						err( "La plantilla no puede tener dos contendores: " + sTemplate );
						return;
					}
					sCont = sTemp;
				}
			}
			if( sCont == null )
			{
				// not an error, just the end of the line.  we mark this tag
				// to show we've visited it
				aContainerMaps[sTemplate] = "*";
			}
			else
			{
				// descend into this container (if necessary)



				getContainerMap( sCont );






				// and we ourselves share that map then since we're merely a wrapper for it
				aContainerMaps[sTemplate] = aContainerMaps[sCont];
			}
		}
	}
}
