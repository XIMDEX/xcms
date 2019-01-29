<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Request;
use Ximdex\I18n\I18N;

require_once XIMDEX_ROOT_PATH . '/conf/stats.php';

class Action_login extends ActionAbstract
{
    public function index()
    {
        if (isset($_COOKIE['expired'])) {
            $this->showLogin('Your session has expired. Please, enter your data again.');
        } else {
            $this->showLogin();
        }
        $this->logSuccessAction();
    }

    private function showLogin(string $msg = null)
    {
        $values = $this->getDefaultVars();
        $this->addJs('/assets/js/browser_checker.js');
        if (! empty($msg)) {
            $values['message'] = _($msg);
        } else {
            $values['message'] = null;
        }
        $this->render($values, 'index', 'only_template.tpl');
        die();
    }

    public function getDefaultVars()
    {
        $values = array();
        I18N::setup();
        $values['ximid'] = App::getValue('ximid');
        $values['versionname'] = App::getValue('VersionName');
        $values['news_content'] = $this->getNews();
        $values['title'] = sprintf(_('Access to %s'), App::getValue('VersionName'));
        return $values;
    }

    private function getNews()
    {
        $lang = strtolower(DEFAULT_LOCALE);
        $REMOTE_NEWS = STATS_SERVER . '/stats/getnews.php';
        $ctx = stream_context_create(
            array(
                'http' => array(
                    'timeout' => 3
                )
            )
        );
        $url = $REMOTE_NEWS . '?lang=' . $lang;

        // Get remote content
        $news_content = @file_get_contents($url, 0, $ctx);
        if (empty($news_content)) {
            $file = 'index_' . $lang . '.html';
            $news_path = APP_ROOT_PATH . '/assets/news/';
            if (file_exists($news_path . $file)) {
                return file_get_contents($news_path . $file);
            } else {
                return file_get_contents($news_path . 'index.html');
            }
        }
        return $news_content;
    }

    public function check()
    {
        $stopper = file_exists(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/login.stop');
        $user_lower = strtolower(Request::post('user'));
        $user = Request::post('user');
        $password = Request::post('password');
        $this->check_disk_space();
        setcookie('expired', '', time() - 3600);
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
        $_user = new User();
        $success = $_user->login($user, $password);
        if ($success) {
            $userObject = new User();
            $userObject->setByLogin($user);
            $userObject->afterLogin();
            $this->logSuccessAction();
            if (Request::get('backto')) {
                header(sprintf('Location: %s', base64_decode(Request::get('backto'))));
            } else {
                header(sprintf('Location: %s', App::getUrl('/')));
            }
            return;
        } else {
            $this->logUnsuccessAction();
            $this->showLogin('Username or password are incorrect. Please, try again.');
        }
    }

    private function check_disk_space()
    {
        return true;
    }
}
