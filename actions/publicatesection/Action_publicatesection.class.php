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

use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Utils\Logs\Log;
use Ximdex\Utils\Sync\SynchroFacade;

class Action_publicatesection extends ActionAbstract
{

    // Main method: shows initial form
    function index()
    {

        $idNode = (int)$this->request->getParam("nodeid");
        $node = new Node($idNode);
        $nodeTypeName = $node->nodeType->GetName();
        /*
        $nodeTypes = array('ImageFile', 'XmlDocument');

        foreach ($nodeTypes as $type) {
            $nodeType = new NodeType();
            $nodeType->SetByName($type);
            //$nameShowed = preg_match('/image/i', $type) > 0 ? 'Imagen' : 'Documento';
            //$publishabledNodeTypes[] = array('id' => $nodeType->get('IdNodeType'), 'name' => $nameShowed);
        }
		*/
        
        $values = array(
            'go_method' => 'publicate_section',
            //'publishabledtypes' => $publishabledNodeTypes,
            'synchronizer_to_use' => ModulesManager::isEnabled('ximSYNC') ? 'ximSYNC' : 'default',
            'ximpublish_tools_enabled' => ModulesManager::isEnabled('ximPUBLISHtools'),
            'folderName' => in_array($nodeTypeName, array('XimNewsSection', 'Section', 'XimNewsImagesFolder', 'XimNewsImagesRootFolder', 'ImagesFolder', 'ImagesRootFolder', 'CssRootFolder', 'CssFolder', 'CommonFolder', 'CommonRootFolder', 'XimNewsImagesFolder')) ? 'section' : 'server',
            'name' => $node->GetNodeName()
        );

        $serverID = $node->getServer();
        $nodeServer = new Node($serverID);
        $nameServer = $nodeServer->get('Name');
        $physicalServers = $nodeServer->class->GetPhysicalServerList(true);
        if (!(sizeof($physicalServers) > 0)) {
            $this->messages->add(sprintf(_("There is not any defined physical server in: '%s'"), $nameServer), MSG_TYPE_ERROR);
            $values['messages'] = $this->messages->messages;
        }

        $this->addJs('/actions/publicatesection/resources/js/index.js');

        $this->render($values, NULL, 'default-3.0.tpl');
    }

    function publicate_section()
    {

        $idNode = (int)$this->request->getParam("nodeid");
        $recurrence = ($this->request->getParam("rec") == "rec") ? true : false;
        $forcePublication = ($this->request->getParam("force_publication") == 1) ? true : false;
        $type = $this->request->getParam("types");
        $type = (isset($type) && $type > 0) ? $type : false;
        $dateUp = time();

        $node = new Node($idNode);
        $nodename = $node->get('Name');
        $nodeTypeName = $node->nodeType->GetName();
        $folderName = in_array($nodeTypeName, array('XimNewsSection', 'Section', 'XimNewsImagesRootFolder', 'XimNewsImagesFolder', 'ImagesFolder', 'ImagesRootFolder', 'CssFolder', 'CssRootFolder', 'CommonFolder', 'CommonRootFolder', 'XimNewsImagesFolder')) ? 'Section' : 'Server';

        $otfPublication = $node->getSimpleBooleanProperty('otf');
        $flagsPublication = array('markEnd' => true,
            'linked' => true,
            'recurrence' => $recurrence,
            'childtype' => $type,
            'workflow' => false,
            'force' => $forcePublication,
            'otfPublication' => $otfPublication);

        $result = SynchroFacade::pushDocInPublishingPool($idNode, $dateUp, NULL, $flagsPublication, $recurrence);

        $this->messages->add(sprintf(_("%s %s has been successfully sent to publish"), $folderName, $nodename), MSG_TYPE_NOTICE);

        $values = array(
            'messages' => $this->messages->messages,
        );

        $this->sendJSON($values);
    }

    function publication_progress()
    {

        if (!defined('LOGGER_LEVEL_DEBUG')) define('LOGGER_LEVEL_DEBUG', 0x0001);
        if (!defined('LOGGER_LEVEL_INFO')) define('LOGGER_LEVEL_INFO', 0x0002);
        if (!defined('LOGGER_LEVEL_WARNING')) define('LOGGER_LEVEL_WARNING', 0x0003);
        if (!defined('LOGGER_LEVEL_ERROR')) define('LOGGER_LEVEL_ERROR', 0x0004);
        if (!defined('LOGGER_LEVEL_FATAL')) define('LOGGER_LEVEL_FATAL', 0x0005);

        ModulesManager::file('/conf/install-modules.php');

        $file = 'publication_logger';
        $sort = 'DESC';
        $level = 'LOGGER_LEVEL_ALL';
        $level = eval("return $level;");

        $logger =& Log::getLogger($file);
        if (is_null($logger)) exit;
        $response = $logger->read();

        $parser = $response['parser'];

        ModulesManager::file('/inc/log/parser/' . $parser . '.class.php');

        $text = nl2br(htmlentities($response['text']));
        $cmd = sprintf('return %s::parse($text, $level, $sort);', $parser);

        $values['text'] = eval($cmd);

        $this->render($values, null, "only_template.tpl");
    }
}