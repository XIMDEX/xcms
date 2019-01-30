<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\NodeTypes;

use Ximdex\Logger;
use Ximdex\Models\Node;

class Factory
{
    public static $baseNodeTypes = array(
        'actionnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ActionNode'),
        'channelnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ChannelNode'),
        'commonnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\CommonNode'),
        'filenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\FileNode'),
        'foldernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\FolderNode'),
        'groupnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\GroupNode'),
        'imagenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ImageNode'),
        'languagenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\LanguageNode'),
        'linknode' => array('ClassName' => '\\Ximdex\\NodeTypes\\LinkNode'),
        'nodetypenode'=> array('ClassName' => '\\Ximdex\\NodeTypes\\NodeTypeNode'),
        'projects'=> array('ClassName' => '\\Ximdex\\NodeTypes\\Projects'),
        'propertynode' => array('ClassName' => '\\Ximdex\\NodeTypes\\PropertyNode'),
        'relnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\RelNode'),
        'rngvisualtemplatenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\rngvisualtemplatenode'),
        'rolenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\RoleNode'),
        'root' => array('ClassName' => '\\Ximdex\\NodeTypes\\Root'),
        'sectionnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\SectionNode'),
        'servernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ServerNode'),
        'statenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\StateNode'),
        'usernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\UserNode'),
        'workflow_process' => array('ClassName' => '\\Ximdex\\NodeTypes\\WorkflowProcess'),
        'XimletNode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XimletNode'),
        'xmldocumentnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XmlDocumentNode'),
        'xmlcontainernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XmlContainerNode'),
        'xsltnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XsltNode')
    );

    public static function getNodeTypeByName(string $name, Node $node, ?string $module = '')
    {
        $_name = strtolower($name);
        if (isset(self::$baseNodeTypes[$_name])) {
            $className = self::$baseNodeTypes[$_name]['ClassName'];
            return new $className($node);
        }
        $className = '\\Ximdex\\NodeTypes\\' . $name;
        if (class_exists($className)) {
            return new $className($node);
        }
        Logger::fatal(sprintf('The nodetype associated to class %s does not exist', $className));
        exit();
    }
}
