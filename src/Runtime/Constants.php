<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Runtime;

/**
 * Temporary Class to manage old Defines as costants
 */
class Constants
{
    // NodeId Codes
    const CREATE = 'C';
    const WRITE = 'W';
    const DELETE = 'D';
    const READ = 'R';
    const UPDATE = 'U';
    
    // BaseIO
    const MODE_NODETYPE = 0;
    const MODE_NODEATTRIB = 1;
    const ERROR_NO_PERMISSIONS = -1;
    const ERROR_INCORRECT_DATA = -2;
    const ERROR_NOT_REACHED = -3;
    const ERROR_NOT_ALLOWED = -4;
    const IMPORTED_STATUS_OK = 1;
    const IMPORTED_STATUS_OK_TO_PUBLISH = 2;
    const IMPORTED_STATUS_PENDING_LINKS = 3;
    
    // Metatype of nodetypes
    static $METATYPES_ARRAY = array(
        'FOLDER' => 'FOLDERNODE',
        'FOLDERNODE' => 'FOLDERNODE',
        'SERVERNODE' => 'FOLDERNODE',
        'SECTIONNODE' => 'SECTIONNODE',
        'FILENODE' => 'FILENODE',
        'IMAGENODE' => 'IMAGENODE',
        'COMMONNODE' => 'COMMONNODE',
        'RNGVISUALTEMPLATENODE' => 'FILENODE',
        'XSLTNODE' => 'FILENODE',
        'LINKNODE' => 'LINKNODE',
        'XMLCONTAINERNODE' => 'XMLCONTAINERNODE',
        'XMLDOCUMENTNODE' => 'XMLDOCUMENTNODE',
        'XIMLETNODE' => 'XMLDOCUMENTNODE',
        'IMAGEFILE' => 'FILENODE',
        'BINARYFILE' => 'COMMONNODE',
        'HTMLLAYOUTNODE' => 'FILENODE',
        'HTMLCOMPONENTNODE' => 'FILENODE',
        'HTMLVIEWNODE' => 'FILENODE',
        'HTMLDOCUMENTNODE' => 'XMLDOCUMENTNODE',
        'VIDEONODE' => 'VIDEONODE' 
    );

    // XimIO
    const REVISION_COPY = 0;
    const IMPORT_FILES = true;
    const UPDATE_LINKS = false;
    const HEADER = 'XIMIO-STRUCTURE';
    const RUN_HEURISTIC_MODE = true;
    const RUN_IMPORT_MODE = false;
    const PUBLISH_STATUS = 'Publish';
}
