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
use Ximdex\Modules\Module;


ModulesManager::file('/inc/io/BaseIO.class.php');
 ModulesManager::file('/inc/model/RelRolesActions.class.php');
ModulesManager::file('/inc/model/role.php');

ModulesManager::file('/inc/utils.php');
ModulesManager::file('/inc/model/orm/RelRolesStates_ORM.class.php');
//ModuleSManager::file(MODULE_XIMLOADER_PATH . '/Module_ximLOADER.class.php');
ModulesManager::file('/actions/addfoldernode/model/ProjectTemplate.class.php');
ModulesManager::file('/actions/addfoldernode/conf/addfoldernode.conf');
ModulesManager::file('/actions/addfoldernode/Action_addfoldernode.class.php');

class Module_ximTOUR extends Module
{

    public function __construct()
    {
        // Call Module constructor.
        parent::__construct('ximTOUR', dirname(__FILE__));
    }

    //Function which installs the module
    function install()
    {
        $projects = new Node(10000);
        $projectid = $projects->GetChildByName("Picasso");
        if (!($projectid > 0)) {
            $addFolderNode = new Action_addfoldernode();
            $nodeID = 10000;
            $name = "Picasso";
            $addFolderNode->name = $name;
            $channels = [10001];
            $addFolderNode->request->setParam('channels_listed', $channels);
            $languages = [10002, 10003];
            $addFolderNode->request->setParam("theme", "picasso");

            $nodeTypeId = 5013;
            $nodeTypeName = "Project";

            $nodeType = new NodeType();
            $nodeType->SetByName($nodeTypeName);

            $folder = new Node();
            $idFolder = $folder->CreateNode($name, $nodeID, $nodeTypeId, null);

            // Adding channel and language properties (if project)
            if ($idFolder > 0 && $nodeTypeName == 'Project') {
                $node = new Node($idFolder);
                if(!empty($channels) && is_array($channels) ){
                    $node->setProperty('channel', $channels);
                    $addFolderNode->channels = $channels;
                }

                if(!empty($languages) && is_array($languages) ){
                    $node->setProperty('language', $languages);
                    $addFolderNode->languages = $languages;
                }
                $addFolderNode->createProjectNodes($idFolder);
            }
        }
        $this->loadConstructorSQL("ximTOUR.constructor.sql");
        return parent::install();
    }

    function uninstall()
    {
        $this->removeStateFile();
        $node = new Node(10000);
        $idNode = $node->GetChildByName("Picasso");
        if ($idNode) {
            $nodePicasso = new Node($idNode);
            $nodePicasso->delete();
        }

        $this->loadDestructorSQL("ximTOUR.destructor.sql");
        parent::uninstall();
    }
}
