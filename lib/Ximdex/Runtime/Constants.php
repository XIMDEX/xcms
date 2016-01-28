<?php
/**
 * Temporary Class to manage old Defines as costants
 */

namespace Ximdex\Runtime;


class Constants
{
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
    const ERROR_NOT_ALLOWED = -4 ;
    const IMPORTED_STATUS_OK = 1 ;
    const IMPORTED_STATUS_OK_TO_PUBLISH = 2 ;
    const IMPORTED_STATUS_PENDING_LINKS = 3 ;
    // 
}