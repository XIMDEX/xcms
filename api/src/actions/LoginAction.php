<?php
/**
 * Created by PhpStorm.
 * User: jvargas
 * Date: 19/02/16
 * Time: 14:39
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
        $response->setMessage("Bad Credentials");
        if ($auth->login($user, $pwd)) {
            $response->setStatus(0);
            $response->setResponse(json_encode(['token' => Token::getToken($user)]));
            $response->setMessage("Auth:login");
        }
        $response->send();

    }

    public static function getMe(Request $r, Response $w)
    {

        $userID = (int)Session::get('userID');
        $user = new User($userID);
        $locale = $user->get('Locale');
        $locale = !is_null($locale) ? $locale : 'en_US';
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
        $response->setMessage("Logged out");
        $response->send();

    }

}