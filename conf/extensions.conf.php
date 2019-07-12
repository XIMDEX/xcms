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
 * @version $Revision: 8735 $
 */

use Ximdex\Runtime\App;

class Extensions
{
    const JQUERY_PATH = '/vendor/jquery';
    const JQUERY      = self::JQUERY_PATH . '/jquery-1.8.3.min.js';
    const JQUERY_UI   = self::JQUERY_PATH . '/jquery-ui-1.9.1.custom.min.js';
    const SMARTY      = '/vendor/smarty/libs/Smarty.class.php';
    const BOOTSTRAP   = '/vendor/bootstrap/dist';
    
    /**
     * If function is called then we retrieve  UrlRoot + constant
     * 
     * @param String $_func  attribute name. e.g: Extensions::smarty() -> smarty
     * @param Array $_args ignored
     * @return string UrlRoot+attribute value
     */
      public static function __callStatic($_func, $_args)
      {    
          $const = strtoupper($_func);
          $value = constant("self::$const");
          
          // For now only the CMS vendor extensions
          $res = App::getValue('UrlFrontController') . $value;
          if (isset($_args[0]) and $_args[0]) {
              $res = App::getValue('UrlRoot') . $res;
          }
          return $res;
      }
}
