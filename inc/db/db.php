<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/


// Include config.
if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}
if (!defined('MAIN_INSTALL_PARAMS')) {
	require_once(XIMDEX_ROOT_PATH . '/inc/modules/modules.const');
}


include_once XIMDEX_ROOT_PATH . '/inc/db/datamodel_config.php';
include_once XIMDEX_ROOT_PATH . '/inc/db/DB_Log.class.php';
// Include config.

if (isset($DB_TYPE_USAGE) && $DB_TYPE_USAGE == ADODB) {
	include_once(XIMDEX_ROOT_PATH . '/inc/db/DB_orm.class.php');
} else if($DB_TYPE_USAGE == ZERO ) {
	include_once(XIMDEX_ROOT_PATH . '/inc/db/DB_zero.class.php');
}
?>
