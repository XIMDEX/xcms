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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Models\NodeType;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Models\RelNodeTypeMimeType;

class Action_setextensions extends ActionAbstract
{
    public function index()
    {
        $commonFolderNodeType = new NodeType(NodeTypeConstants::COMMON_ROOT_FOLDER);
        $commonAllowedExtensions = $commonFolderNodeType->getAllowedExtensions();
        $this->addCss('/actions/setextensions/resources/css/style.css');
        $values = array('commonAllowedExtensions' => json_encode($commonAllowedExtensions));
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function update_extensions()
    {
        $post = file_get_contents('php://input');
        $extensions = json_decode($post, true);
        $extensions = $extensions['states'];
        $patt = '/^((\w+|\*))?(,(\w+|\*))*\s*$/i';
        $res = true;
        foreach ($extensions as $id => $ext) {
            $res = preg_match($patt, trim($ext['extension']));
            $extensions[$id]['extension'] = preg_split('/,/', trim($ext['extension']), 0, PREG_SPLIT_NO_EMPTY);
            if (in_array('*', $extensions[$id]['extension'])) {
                $extensions[$id]['extension'] = array('*');
            }
        }
        if (! $res) {
            $values = array('result' => 'fail', 'message' => _('There are errors on extensions'));
            $this->sendJSON($values);
        }
        foreach ($extensions as $id => $ext) {
            foreach ($extensions as $id2 => $ext2) {
                if ($id < $id2) {
                    $count = count(array_intersect($ext['extension'], $ext2['extension']));
                    $res = $count == 0;
                }
            }
        }
        if (! $res) {
            $values = array('result' => 'fail', 'message' => _('Extensions have ambiguous values'));
            $this->sendJSON($values);
        }
        foreach ($extensions as $ext) {
            $e = new RelNodeTypeMimeType($ext['id']);
            if (count($ext['extension']) == 0) {
                $e->set('extension', '');
            } elseif (isset($ext['extension'][0]) && $ext['extension'][0] == '*') {
                $e->set('extension', '*');
            } else {
                $e->set('extension', ';' . implode(';', $ext['extension']) . ';');
            }
            $e->update();
        }
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $values = array(
            'result' => 'ok',
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'message' => _('The extensions have been updated')
        );
        $this->sendJSON($values);
    }
}
