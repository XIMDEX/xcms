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

use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Session;
use Ximdex\Models\User;
use XimdexApi\core\Token;

class Action_Xedit extends ActionAbstract
{
    public function index()
    {
        $id = $this->request->getParam('nodeid');
        $strDoc = new StructuredDocument($id);
        if ($strDoc->GetSymLink()) {
            $masterNode = new Node($strDoc->GetSymLink());
            $this->render(['path_master' => $masterNode->GetPath()], 'linked_document', 'default-3.0.tpl');
            return false;
        }
        $userID = (int) Session::get('userID');
        $user = new User($userID);
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
            'ximdex_API' => App::getValue('UrlHost') . App::GetValue('UrlRoot') . '/api/',
            'url' => App::GetValue('HTMLEditorURL'),
            'enabled' => App::GetValue('HTMLEditorEnabled'),
            'token' => Token::getToken($user->get('Login'))
        );
        $this->addCss('/actions/xedit/resources/css/iframe.css');
        $this->render($values, NULL, 'default-3.0.tpl');
    }
}
