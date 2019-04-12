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

use Ximdex\MVC\FrontController;
use Ximdex\Modules\Manager;
use Ximdex\Runtime\App;

include_once dirname(__DIR__) . '/bootstrap.php';

include_once __DIR__ . '/src/autoload.php';

// General class
Manager::file('/install/InstallController.class.php');

// FROM MVC
if (! defined('RENDERER_ROOT_PATH')) {
    define('RENDERER_ROOT_PATH', XIMDEX_ROOT_PATH . '/src/MVC/Render');
}
if (! defined('SMARTY_TMP_PATH')) {
    define('SMARTY_TMP_PATH', XIMDEX_ROOT_PATH . App::getValue('TempRoot'));
}

// Main thread
if (! InstallController::isInstalled()) {
    if (strpos($_SERVER['REQUEST_URI'], 'public_xmd') !== false) {
        
        // The folder public_xmd is not a good place to run the installer
        header('Location:../');
    } else {
        $installController = new InstallController();
        $installController->dispatch();
    }
} else {
    /*
    if (strpos($_SERVER['REQUEST_URI'], App::getValue('UrlFrontController')) === false) {
        
        // Got to public_xmd folder
        header('Location:' . App::getValue('UrlRoot') . App::getValue('UrlFrontController') . '?' . $_SERVER['QUERY_STRING']);
    } else {
    */
        // Check coherence with HTTP_ACCEPT_LANGUAGE
        $locale = \Ximdex\Runtime\Session::get('locale');
        Ximdex\I18n\I18N::setup($locale);
        $frontController = new FrontController();
        $frontController->dispatch();
    // }
}
