<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 28/1/16
 * Time: 18:38
 */

namespace Ximdex\NodeTypes;


use ModulesManager;
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
    public static function getNodeTypeByName($name, &$node, $module = '')
    {
        $_name = strtolower($name);
        if (isset(self::$baseNodeTypes[$_name])) {
            $className = self::$baseNodeTypes[$_name]['ClassName'];
            return new $className($node);
        }

        if (!empty($module)) {
            $fileToInclude = sprintf('%s%s/src/NodeTypes/%s.php', XIMDEX_ROOT_PATH, ModulesManager::path($module), strtolower($name));
        } else {
            $fileToInclude = sprintf('%s/src/NodeTypes/%s.php', XIMDEX_ROOT_PATH, strtolower($name));
        }
        if (is_file($fileToInclude)) {
            include_once($fileToInclude);
        }
        if (class_exists($name)) {
            return new $name($node);
        }

        $className = "\\Ximdex\\NodeTypes\\" . $name ;
        if ( class_exists( $className )) {
            return new $className($node);
        }
        
        Logger::info(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
        die(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
    }
}