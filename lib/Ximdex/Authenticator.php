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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */


namespace Ximdex;

use ModulesManager;
use Ximdex\Models\User;
use Ximdex\Runtime\App;
use Ximdex\Utils\Session;


class Authenticator
{


    /**
     * Constructor
     */
    public function __construct()
    {


    }

    /**
     *
     * @param $name
     * @param $password
     * @return boolean
     */
    function login($name, $password)
    {


        if ($this->authenticate($name, $password)) {

            // Is a valid user !
            $user = new User();
            $user->setByLogin($name);
            $user_id = $user->getID();
            $user = new user($user_id);

            $user_locale = $user->get('Locale');

            if (empty($user_locale))
                $user_locale =  App::getValue('locale');

            // STOPPER
            $stopperFilePath =  App::getValue("AppRoot") . App::getValue("TempRoot") . "/login.stop";
            if ($user->getID() != "301" && file_exists($stopperFilePath)) {
                // login closed
                return false;
            }

            if (ModulesManager::isEnabled("ximDEMOS")) {
                $user_demo = (int)$user->isDemo();
            } else {
                $user_demo = 0;
            }

            unset($user);

            if (ModulesManager::isEnabled('ximADM')) {
                ModulesManager::file('/inc/Status.class.php', 'ximADM');

                $user_status = new Status();
                $user_status->remove($user_id);
                $user_status->init($user_id);
            }

            // TODO: Add new session system.
            Session::set('user_name', $name);
            Session::set('user_demo', $user_demo);
            Session::set('logged', $user_id);
            Session::set('userID', $user_id);
            Session::set('locale', $user_locale);
            Session::set('loginTimestamp', time());
            $session_info = session_get_cookie_params();
            $session_lifetime = $session_info['lifetime']; // session cookie lifetime in seconds
            $session_duration = $session_lifetime != 0 ? $session_lifetime : session_cache_expire() * 60;
            $loginTimestamp = Session::get("loginTimestamp");
            setcookie("loginTimestamp", $loginTimestamp, 0,  '/' );
            setcookie("sessionLength", $session_duration , 0,  '/' );
            /**/


            return true;
        }
        return false;
    }

    /*
     *
     */
    function logout()
    {

        // TODO: Add new session system.
            Session::destroy();

    }

    /**
     * SQL Authenticate
     * @param $username
     * @param $password
     * @return bool
     */
    function authenticate($username, $password)
    {
        $user = new User();
        $user->SetByLogin($username);
        if ($user->CheckPassword($password)) {
            return true;
        } else {
            return false;
        }
    }


}