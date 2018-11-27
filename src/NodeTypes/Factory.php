<?php

namespace Ximdex\NodeTypes;

use Ximdex\Logger;

class Factory
{
    public static $baseNodeTypes = array(
        'actionnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ActionNode'),
        'channelnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ChannelNode'),
        'commonnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\CommonNode'),
        'filenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\FileNode'),
        'foldernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\FolderNode'),
        'groupnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\GroupNode'),
        'imagenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ImageNode') ,
        'languagenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\LanguageNode') ,
        'linknode' => array('ClassName' => '\\Ximdex\\NodeTypes\\LinkNode'),
        'nodetypenode'=> array('ClassName' => '\\Ximdex\\NodeTypes\\NodeTypeNode') ,
        'projects'=> array('ClassName' => '\\Ximdex\\NodeTypes\\Projects') ,
        'propertynode' => array('ClassName' => '\\Ximdex\\NodeTypes\\PropertyNode'),
        'relnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\RelNode'),
        'rngvisualtemplatenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\rngvisualtemplatenode') ,
        'rolenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\RoleNode'),
        'root' => array('ClassName' => '\\Ximdex\\NodeTypes\\Root'),
        'sectionnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\SectionNode') ,
        'servernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ServerNode') ,
        'statenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\StateNode') ,
        'usernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\UserNode'),
        'workflow_process' => array('ClassName' => '\\Ximdex\\NodeTypes\\WorkflowProcess'),
        'XimletNode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XimletNode'),
        'xmldocumentnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XmlDocumentNode'),
        'xmlcontainernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XmlContainerNode'),
        'xsltnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\XsltNode'),
    );

    /**
     * @param $name
     * @param $node
     * @param string $module
     * @return mixed
     */
    public static function getNodeTypeByName($name, $node, $module = '')
    {
        // TODO atovar
        $_name = strtolower($name);
        if (isset(self::$baseNodeTypes[$_name])) {
            $className = self::$baseNodeTypes[$_name]['ClassName'];
            return new $className($node);
        }
        
        $className = "\\Ximdex\\NodeTypes\\" . $name;
        
        if (class_exists($className)) {
            return new $className($node);
        }
        Logger::fatal(sprintf('The nodetype associated to %s does not exist', $fileToInclude));
        die(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
    }
}
