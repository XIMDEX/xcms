<?php
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




 

ModulesManager::file('/inc/utils.inc');

XSession::check();
		
////
//// Initiation of action flow.
//// 

if ($_GET["nodeid"] && !is_null($_GET["version"]) && !is_null($_GET["subversion"]))
	{  
	$nodeID		= $_GET["nodeid"];
	$version	= $_GET["version"];
	$subVersion	= $_GET["subversion"];
	$data		= new DataFactory($nodeID);
	$fileContent= $data->GetContent($version, $subVersion);
	$gmDate =  gmdate("D, d M Y H:i:s");
	
	/// Expiration headers
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");				// Date in the past
	header("Last-Modified: " . $gmDate . " GMT");	// always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");	// HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");										// HTTP/1.0
	header('ETag: "'.md5($nodeID.$gmDate).'"'); 
	/// Content headers
	header("Content-Length: ".strlen(strval($fileContent)));
	header("Content-transfer-encoding: binary\n"); 
	echo $fileContent;
	}
else
	{
	trace();
   	gPrintHeader();
    gPrintBodyBegin();
   	gPrintMsg(_("Error with parameters"));
	gPrintBodyEnd();
	}
?>