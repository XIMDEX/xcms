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

namespace XimdexApi\actions;

use Ximdex\Models\User;
use Ximdex\Runtime\Session;
use XimdexApi\core\Request;
use XimdexApi\core\Response;
use XimdexApi\core\Token;

class LoginAction extends Action
{
    protected const ROUTES = [
        'login' => 'login',
        'logout' => 'logout',
        'me' => 'getMe',
    ];
    protected const PUBLIC = [
        'login'
    ];

    public static function login(Request $request, Response $response)
    {
        $auth = new User();
        $user = $_POST["user"];
        $pwd = $_POST["pwd"];
        $response->setStatus(-1);
        $response->setMessage('Bad Credentials');
        if ($auth->login($user, $pwd)) {
            $response->setStatus(0);
            $response->setResponse(json_encode(['token' => Token::getToken($user)]));
            $response->setMessage('Auth:login');
        }
        $response->send();
    }

    public static function getMe(Request $r, Response $w)
    {
        $userID = (int) Session::get('userID');
        $user = new User($userID);
        $locale = $user->get('Locale');
        $locale = ! is_null($locale) ? $locale : 'en_US';
        $response = [
            'id' => $userID,
            'username' => $user->get('Login'),
            'name' => $user->get('Name'),
            'email' => $user->get('Email'),
            'locale' => $locale,
        ];
        $w->setResponse($response);
        $w->send();
    }

    public static function logout(Request $request, Response $response)
    {
        $auth = new User();
        $auth->logout();
        $response->setStatus(-1);
        $response->setMessage('Logged out');
        $response->send();
    }
}
