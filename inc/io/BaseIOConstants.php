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




if (!defined('ERROR_NO_PERMISSIONS')) define ('ERROR_NO_PERMISSIONS', -1);
if (!defined('ERROR_INCORRECT_DATA')) define ('ERROR_INCORRECT_DATA', -2);
if (!defined('ERROR_NOT_REACHED')) define ('ERROR_NOT_REACHED', -3);
if (!defined('ERROR_NOT_ALLOWED')) define ('ERROR_NOT_ALLOWED', -4);

if (!defined('IMPORTED_STATUS_OK')) define ('IMPORTED_STATUS_OK', 1);
if (!defined('IMPORTED_STATUS_OK_TO_PUBLISH')) define ('IMPORTED_STATUS_OK_TO_PUBLISH', 2);
if (!defined('IMPORTED_STATUS_PENDING_LINKS')) define ('IMPORTED_STATUS_PENDING_LINKS', 3);


// Array of classes => metatype which is belonging

$metaTypesArray = array(
						'FOLDER' => 'FOLDERNODE',
						'FOLDERNODE' => 'FOLDERNODE',
						'SERVERNODE' => 'FOLDERNODE',
						'PDFSECTIONNODE' => 'FOLDERNODE',
						'XIMNEWSCOLECTORNODETYPE' => 'FOLDERNODE',
						'XIMNEWSIMAGESFOLDER' => 'FOLDERNODE',
						'XIMNEWSDATESECTION' => 'FOLDERNODE',
						'SECTIONNODE' => 'SECTIONNODE',
						'XIMPORTAALLOWEDCONTENT' => 'FILENODE',
						'FILENODE' => 'FILENODE',
						'VISUALTEMPLATENODE' => 'FILENODE',
						'RNGVISUALTEMPLATENODE' => 'FILENODE',
						'TEMPLATENODE' => 'FILENODE',
						'XSLTNODE' => 'FILENODE',
						'XIMNEWSIMAGEFILE' => 'FILENODE',
						'LINKNODE' => 'LINKNODE',
						'XMLCONTAINERNODE' => 'XMLCONTAINERNODE',
						'PDFDOCUMENTFOLDERNODE' => 'XMLCONTAINERNODE',
						'XMLDOCUMENTNODE' => 'XMLDOCUMENTNODE',
						'XIMLETNODE' => 'XMLDOCUMENTNODE',
						'XIMNEWSBULLETINNODETYPE' => 'XMLDOCUMENTNODE',
						'XIMNEWSNEWLANGUAGE' => 'XMLDOCUMENTNODE',
						'PDFDOCUMENTLANGNODE' => 'XMLDOCUMENTNODE',
						'XIMPORTA' => 'XIMPORTA',
						'TRASHNODE' => 'TRASH',
						'IMAGEFILE' => 'FILENODE',
						'XIMNEWSBULLETINLANGUAGEXIMLET' => 'XMLDOCUMENTNODE',
						'XIMNEWSBULLETINLANGUAGEXIMLETCONTAINER' => 'XMLCONTAINERNODE',
						'TOLFOLDERNODE' => 'FOLDERNODE',
						'TOLDOCUMENTNODE' => 'XMLDOCUMENTNODE'
				);
?>