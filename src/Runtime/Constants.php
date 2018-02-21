<?php

/**
 * Temporary Class to manage old Defines as costants
 */
namespace Ximdex\Runtime;

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

    const EDITION_STATUS_ID = 7;
    // metatype of nodetypes
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
        'BINARYFILE' => 'COMMONNODE'
    );

    // ximIO
    const REVISION_COPY = 0;
    const IMPORT_FILES = true;
    const UPDATE_LINKS = false;
    const HEADER = 'XIMIO-STRUCTURE';
    const RUN_HEURISTIC_MODE = true;
    const RUN_IMPORT_MODE = false;
    const PUBLISH_STATUS = 'Publish';
}