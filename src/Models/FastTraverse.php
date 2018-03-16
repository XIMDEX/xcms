<?php

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\FastTraverseOrm;
use Ximdex\Runtime\Db;

class FastTraverse extends FastTraverseOrm
{
    /**
     * Get an array with all the child nodes which depend of a parent node given
     * This array contains the IdNode field with the Depth value in its index
     * If the parameter nodeTypes is a true value, the index of each node will be its ID and the value the Node Type ID
     * Also an array named $filters with field name and value (ex. 'Id' => '10001') can be used to make the result more precise
     * The level parameter can specify the maximun depth level to obtain
     *
     * @param int $idNode
     * @param array $fields
     * @param int $level
     * @param array $filters
     * @param array $nodeTypeFlags
     * @return bool|array
     */
    public static function get_children(int $idNode, array $fields = null, int $level = null, array $filters = null, array $nodeTypeFlags = null)
    {
        if ($idNode < 1) {
            Logger::error('Getting children in FastTraverse without node given');
            return false;
        }
        $db = new Db();
        $sql = 'select ft.IdChild, ft.Depth';
        if ($fields) {
            if (isset($fields['nodeType'])) {
                foreach ($fields['nodeType'] as $field) {
                    $sql .= ', nt.' . $field;
                }
            }
            if (isset($fields['node'])) {
                foreach ($fields['node'] as $field) {
                    $sql .= ', node.' . $field;
                }
            }
        }
        $sql .= ' from FastTraverse ft';
        if (isset($fields['node']) or isset($fields['nodeType']) or $nodeTypeFlags) {
            
            // If the fields contain node or nodetype values, or node type flags, we need to make a join with Nodes table
            $sql .= ' inner join Nodes node on (node.IdNode = ft.IdChild)';
            if (isset($fields['nodeType']) or $nodeTypeFlags) {
                
                // We need to make a join to NodeType table in order to use the node type values and flags
                $sql .= ' inner join NodeTypes nt on (nt.IdNodeType = node.IdNodeType';
                
                // Filter for node type flags
                if ($nodeTypeFlags) {
                    foreach ($nodeTypeFlags as $flag => $value) {
                        $sql .= ' and nt.' . $flag . ' is ' . (($value) ? 'true' : 'false'); 
                    }
                }
                $sql .= ')';
            }
        }
        $sql .= ' where ft.IdNode = ' . $idNode;
        
        // Get only a specified level
        if ($level) {
            $sql .= ' and ft.Depth <= ' . $level;
        }
        
        // Filters add some criteria to obtain specified nodes
        if ($filters) {
            foreach ($filters as $field => $values) {
                if (is_array($values)) {
                    $sql .= ' and ' . $field . ' in (' . implode(',', $values) . ')';
                } else {
                    $sql .= ' and ' . $field . ' = \'' . $values . '\'';
                }
            }
        }
        if ($db->Query($sql) === false) {
            return false;
        }
        $children = array();
        if ($fields) {
            
            // The returned array will have the Depth as the primary level, the node ID as the second with an array with the specfied fields
            while (! $db->EOF) {
                foreach ($fields as $source => $sourceFields) {
                    foreach ($sourceFields as $field) {
                        $children[$db->GetValue('Depth')][$db->GetValue('IdChild')][$source][$field] = $db->GetValue($field);
                    }
                }
                $db->Next();
            }
        } else {
            
            // The returned array will have the Depth as key with the node ID as the value
            while (! $db->EOF) {
                $children[$db->GetValue('Depth')][] = $db->GetValue('IdChild');
                $db->Next();
            }
        }
        return $children;
    }

    /**
     * Get an array with all the parent nodes which depend of a child node given
     * This array contains the IdNode field with the Depth value in its index
     *
     * @param int $idNode
     * @param string $index
     * @return bool|array
     */
    public static function get_parents(int $idNode, string $value = null, string $index = null)
    {
        if ($idNode < 1) {
            Logger::error('Getting parents in FastTraverse without node given');
            return false;
        }
        if ($index or $value) {
            if (! $index) {
                $index = 'ft.IdNode';
            }
            if (! $value) {
                $value = 'Depth';
            }
            $sql = 'select ' . $value . ' as _value, ' . $index . ' as _index from FastTraverse ft';
            $sql .= ' inner join Nodes node on (node.IdNode = ft.IdNode)';
        } else {
            $sql = 'select ft.IdNode as _index, Depth as _value from FastTraverse ft';
        }
        $sql .= ' where ft.IdChild = ' . $idNode . ' order by ft.Depth';
        $db = new Db();
        if ($db->Query($sql) === false) {
            Logger::error('Getting parents in FastTraverse with node: ' . $idNode . ' (' . $db->desErr . ')');
            return false;
        }
        $parents = array();
        while (! $db->EOF) {
            $parents[$db->GetValue('_index')] = $db->GetValue('_value');
            $db->Next();
        }
        return $parents;
    }
}