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

use Ximdex\Authenticator;
use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Request;

require_once(XIMDEX_ROOT_PATH . '/conf/stats.php');
ModulesManager::file('/inc/i18n/I18N.class.php');

class Action_login extends ActionAbstract
{

    function index()
    {
        if (isset($_COOKIE['expired'])) {
            $this->showLogin('Your session has expired. Please, enter your data again.');
        } else {
            $this->showLogin();
        }
        $this->logSuccessAction();
    }

    function showLogin($msg = NULL)
    {
        $values = $this->getDefaultVars();
        $this->addJs("/public_xmd/assets/js/browser_checker.js");

        if (!empty($msg))
            $values["message"] = _($msg);
        else
            $values["message"] = NULL;

        //Login change if ximDEMOS is active and the Smarty exists.
        if (ModulesManager::isEnabled('ximDEMOS') && file_exists(APP_ROOT_PATH . "/actions/login/template/Smarty/indexDEMO.tpl"))
            $this->render($values, 'indexDEMO', 'only_template.tpl');
        else
            $this->render($values, 'index', 'only_template.tpl');
        die();
    }

    function getDefaultVars()
    {
        $values = array();

        I18N::setup();
        $values["ximid"] = App::getValue("ximid");
        $values["versionname"] = App::getValue("VersionName");
        $values["news_content"] = $this->get_news();
        $values["title"] = sprintf(_("Access to %s"), App::getValue("VersionName"));

        return $values;
    }

    function get_news()
    {
        $lang = strtolower(DEFAULT_LOCALE);

        $REMOTE_NEWS = STATS_SERVER . "/stats/getnews.php";

        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 3
                )
            )
        );

        //$url = $REMOTE_NEWS . "?lang=" . strtolower(DEFAULT_LOCALE) . "&ximid=" . App::getValue('ximid');
        $url = $REMOTE_NEWS . "?lang=" . $lang;

        //get remote content
       $news_content = @file_get_contents($url, 0, $ctx);


        if (empty($news_content)) {
            $file = "index_" . $lang . ".html";
            $news_path = APP_ROOT_PATH . "/assets/news/";

            if (file_exists($news_path . $file)) {
                return file_get_contents($news_path . $file);
            }else{
                return file_get_contents($news_path . "index.html");
            }
        }

        return $news_content;
    }

    function check()
    {
        $stopper = file_exists(XIMDEX_ROOT_PATH . App::getValue("TempRoot") . "/login.stop");
        $user_lower = strtolower(Request::post('user'));
        $user = Request::post('user');
        $password = Request::post('password');
        $formsent = Request::post('login');

        $this->check_disk_space();
        setcookie("expired", "", time() - 3600);

        if (empty($user) && empty($password)) {
            $this->showLogin('You should write a username and password.');
        } elseif (empty($user)) {
            $this->showLogin('You should write a username.');
        } elseif (empty($password)) {
            $this->showLogin('You should write a password.');
        }


        if ('ximdex' != $user_lower && $stopper) {
            $this->showLogin('Access blocked. At this moment maintenance tasks are being performed. Sorry for the inconveniences.');
        }

        $authenticator = new Authenticator();
        $success = $authenticator->login($user, $password);

        if ($success) {
            $userObject = new User();
            $userObject->setByLogin($user);
            $userObject->afterLogin();
            \Ximdex\Utils\Session::set('context', 'ximdex');
            $this->logSuccessAction();

            if (Request::get('backto')) {
                header(sprintf('Location: %s', base64_decode(Request::get('backto'))));
            } else {
                header(sprintf("Location: %s/", App::getValue('UrlRoot')));
            }

            die();

        } else {
            $this->logUnsuccessAction();
            $this->showLogin('Username or password are incorrect. Please, try again.');
        }
    }

    function check_disk_space()
    {
       return true ;

    }
}
