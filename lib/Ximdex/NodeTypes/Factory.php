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
        'root' => array('ClassName' => '\\Ximdex\\NodeTypes\\Root'),
        'propertynode' => array('ClassName' => '\\Ximdex\\NodeTypes\\PropertyNode'),
        'actionnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ActionNode'),
        'foldernode' => array('ClassName' => '\\Ximdex\\NodeTypes\\FolderNode'),
        'channelnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\ChannelNode'),
        'relnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\RelNode'),
        'rolenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\RoleNode'),
        'filenode' => array('ClassName' => '\\Ximdex\\NodeTypes\\FileNode'),
        'groupnode' => array('ClassName' => '\\Ximdex\\NodeTypes\\GroupNode'),


    );

    /**
     * @param $name
     * @param $node
     * @param string $module
     * @return mixed
     */
    public static function getNodeTypeByName($name, &$node, $module = '')
    {
        if (isset(self::$baseNodeTypes[$name])) {
            $className = self::$baseNodeTypes[$name]['ClassName'];
            return new $className($node);
        }

        if (!empty($module)) {
            $fileToInclude = sprintf('%s%s/inc/nodetypes/%s.php', XIMDEX_ROOT_PATH, ModulesManager::path($module), strtolower($name));
        } else {
            $fileToInclude = sprintf('%s/inc/nodetypes/%s.php', XIMDEX_ROOT_PATH, strtolower($name));
        }
        if (is_file($fileToInclude)) {
            include_once($fileToInclude);
        }
        if (class_exists($name)) {
            return new $name($node);
        }
        Logger::info(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
        die(sprintf(_('Fatal error: the nodetype associated to %s does not exist'), $fileToInclude));
    }
}