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



	/********************************************************
			INSTALL_PARAMS.CONF.PHP
		 Warning: Do not touch this file manually
	*********************************************************/


	/* DATABASE_PARAMS (do not remove this comment, please) */
        $DBHOST = "localhost";
        $DBPORT = "3306";
        $DBUSER = "ximdex";
        $DBPASSWD = "ximdex";
        $DBNAME = "ximdex";


	/* XIMDEX_PARAMS (do not remove this comment, please) */
		  if (!defined('XIMDEX_TIMEZONE'))
			define("XIMDEX_TIMEZONE", "UTC");

		  date_default_timezone_set(XIMDEX_TIMEZONE);

        $XIMDEX_ROOT_PATH = "/var/www/html/ximdex";

		  if (!defined('DEFAULT_LOCALE'))
			 define('DEFAULT_LOCALE', 'es_ES');

        $USE_SQL_LOG = false;
?>