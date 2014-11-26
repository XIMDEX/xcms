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



/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

require_once(XIMDEX_ROOT_PATH . "/inc/utils.php");

if (!defined('DEFAULT_LOCALE'))
  define('DEFAULT_LOCALE', 'es_ES');

class I18N {

	public static function setup($locale = null) {

		if (extension_loaded('gettext')) {

			if (empty($locale) || strlen($locale)>5 )
				$locale = XSession::get("locale");

			if (empty($locale))
				$locale = \App::getValue( 'locale');

			if (empty($locale) || !@file_exists(XIMDEX_ROOT_PATH . '/inc/i18n/locale/'.$locale) )
				$locale = DEFAULT_LOCALE;


			putenv("LC_ALL=$locale");
			putenv("LANG=$locale");
			setlocale(LC_ALL, "$locale.utf8");
		 	bindtextdomain("messages", XIMDEX_ROOT_PATH . '/inc/i18n/locale');
			textdomain("messages");
			bind_textdomain_codeset("messages", 'ISO8859-1');

		   XSession::set("locale", $locale);
		}
	}

}


// suggest HTTP_ACCEPT_LANGUAGE