<?php

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\FastTraverseOrm;
use Ximdex\Runtime\Db;

class FastTraverse extends FastTraverseOrm
{
    /**
     * Getting an array with all the child nodes which depend of a parent node given
     * This array contains the IdNode field with the Depth value in its index
     * If the parameter nodeTypes is a true value, the index of each node will be its ID and the value the Node Type ID
     * Also an array named $filters with field name and value (ex. 'Id' => '10001') can be used to make the result more precise 
     * @param int $idNode
     * @param bool $nodeTypes
     * @param int $level
     * @param array $filters
     * @return bool|string[]
     */
    public static function get_children(int $idNode, bool $nodeTypes = false, int $level = null, array $filters = [])
    {
        if ($idNode < 1)
        {
            Logger::error('Getting children in FastTraverse without node given');
            return false;
        }
        $db = new Db();
        $sql = 'select ft.IdChild, ft.Depth';
        if ($nodeTypes)
            $sql .= ', node.IdNodeType';
        $sql .= ' from FastTraverse ft';
        if ($nodeTypes)
            $sql .= ' inner join Nodes node on (node.IdNode = ft.IdChild)';
        $sql .= ' where ft.IdNode = ' . $idNode;
        if ($level)
            $sql .= ' and ft.Depth = ' . $level;
        if ($filters)
            foreach ($filters as $field => $values)
            {
                if (is_array($values))
                    $sql .= ' and ' . $field . ' in (' . implode(',', $values) . ')';
                else
                    $sql .= ' and ' . $field . ' = \'' . $values . '\'';
            }
        if ($db->Query($sql) === false)
            return false;
        $children = array();
        if ($nodeTypes)
            while (!$db->EOF)
            {
                $children[$db->GetValue('Depth')][$db->GetValue('IdChild')] = $db->GetValue('IdNodeType');
                $db->Next();
            }
        else
            while (!$db->EOF)
            {
                $children[$db->GetValue('Depth')][] = $db->GetValue('IdChild');
                $db->Next();
            }
        return $children;
    }

    /**
     * Getting an array with all the parent nodes which depend of a child node given
     * This array contains the IdNode field with the Depth value in its index
     * @param int $idNode
     * @param string $index
     * @return boolean|array
     */
    public static function get_parents(int $idNode, string $value = null, string $index = null)
    {
        if ($idNode < 1)
        {
            Logger::error('Getting parents in FastTraverse without node given');
            return false;
        }
        if ($index or $value)
        {
            if (!$index)
                $index = 'IdNode';
            if (!$value)
                $value = 'Depth';
            $sql = 'select node.' . $value . ', node.' . $index . ' from FastTraverse ft';
            $sql .= ' inner join Nodes node on (node.IdNode = ft.IdNode)';
        }
        else
        {
            $index = 'IdNode';
            $value = 'Depth';
            $sql = 'select ft.' . $index . ', ft.' . $value . ' from FastTraverse ft';
        }
        $sql .= ' where ft.IdChild = ' . $idNode . ' order by ft.Depth';
        $db = new Db();
        if ($db->Query($sql) === false)
        {
            Logger::error('Getting parents in FastTraverse with node: ' . $idNode . ' (' . $db->desErr . ')');
            return false;
        }
        $parents = array();
        while (!$db->EOF)
        {
            $parents[$db->GetValue($index)] = $db->GetValue($value);
            $db->Next();
        }
        return $parents;
    }
}