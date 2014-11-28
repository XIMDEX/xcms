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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



 
class Action_browserimages extends ActionAbstract {
   // M�todo principal: presenta el formulario inicial
    function index () {

	//Forma antigua de hacerlo. Hay que migrarlo a la nueva plantilla
	//include_once (XIMDEX_ROOT_PATH."/modules/ximNEWS/actions/browserimages/init.php");

	$idNode = $this->request->getParam("nodeid");
        $actionID = $this->request->getParam("actionid");

        $node = new Node($idNode);
        $nodeName = $node->get('Name');

	$lotes_list = ximNEWS_Adapter::getLotes($idNode);

        $this->addJs('/actions/browserimages/resources/js/browser_images.js', 'ximNEWS');
        $this->addCss('/actions/browserimages/resources/css/index.css', 'ximNEWS');
        $query = \Ximdex\Runtime\App::get('\Ximdex\Utils\QueryManager');
	$action = $query->getPage() . $query->buildWith(array('method' => 'mi_metodo'));
        $values = array(
                        'id_node' => $idNode,
                       // 'languages' => $languages_list,
                       // 'channels' => $channels_list,
                       // 'templates' => $templates_listx,
                        'lotes' => sizeof($lotes_list) > 0 ? $lots_list : NULL,
                        'action_url' => $action
        );

	$this->_render($values, '', 'default-3.0.tpl');
    }
}
