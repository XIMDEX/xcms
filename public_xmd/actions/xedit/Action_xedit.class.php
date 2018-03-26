<?php

use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Session;
use Ximdex\Models\User;
use XimdexApi\core\Token;

class Action_Xedit extends ActionAbstract
{
    function index()
    {
        $userID = (int)Session::get('userID');
        $user = new User($userID);

        $id = $this->request->getParam('nodeid');
        $node = new Node($id);
        if (!$node->GetID()) {
            $this->messages->add(_('Requested document does not exist') . ' (ID: ' . $id . ')', MSG_TYPE_ERROR);
            $this->renderMessages();
            return false;
        }
        $type = $this->request->getParam('type');
        $values = array(
            'type' => $type,
            'id' => $id,
            'ximdex_API' => App::getValue('UrlHost') . App::GetValue('UrlRoot') . '/api',
            'url' => App::GetValue('HTMLEditorURL'),
            'enabled' => App::GetValue('HTMLEditorEnabled'),
            'token' => Token::getToken($user->get('Login'))
        );
        $this->addCss('/actions/xedit/resources/css/iframe.css');
        $this->render($values, NULL, 'default-3.0.tpl');
    }
}