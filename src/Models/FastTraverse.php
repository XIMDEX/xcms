<?php

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\FastTraverseOrm;
use Ximdex\Runtime\Db;

class FastTraverse extends FastTraverseOrm
{
    public static $db;
    
    /**
     * Get an array with all the child nodes which depend of a parent node given
     * This array contains the IdNode field with the Depth value in its index by default
     * If other fields are needed, fields parameter can be used to declare each one
     * Also an array named filters with field name and value (ex. 'Id' => '10001') can be used to make the result more precise
     * The level parameter can specify the maximun depth level to obtain
     * Filters must be an array of fields to include or exclude some field values (using index 'exclude' or 'include')
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
        
        // Filters add some criteria to obtain specified nodes (ex. 'include' => ['IdNodeType' =>  [5014, 5015]])
        if ($filters) {
            foreach ($filters as $operation => $opFilters) {
                if ($operation == 'exclude') {
                    $operator = 'not in';
                } else {
                    $operator = 'in';
                }
                if (!is_array($opFilters)) {
                    Logger::error(ucfirst($operation) . ' filters parameter must be an array of fields');
                    return false;
                }
                foreach ($opFilters as $field => $values) {
                    if (!is_array($values)) {
                        Logger::error('Filter parameter for ' . $field . ' field must be an array of fields');
                        return false;
                    }
                    $sql .= ' and ' . $field . ' ' . $operator . ' (' . implode(',', $values) . ')';
                }
            }
        }
        if (!self::$db) {
            self::$db = new Db();
        }
        if (self::$db->Query($sql) === false) {
            return false;
        }
        $children = array();
        if ($fields) {
            // The returned array will have the Depth as the primary level, the node ID as the second with an array with the specfied fields
            while (! self::$db->EOF) {
                foreach ($fields as $source => $sourceFields) {
                    foreach ($sourceFields as $field) {
                        $children[self::$db->GetValue('Depth')][self::$db->GetValue('IdChild')][$source][$field] = self::$db->GetValue($field);
                    }
                }
                self::$db->Next();
            }
        } else {
            // The returned array will have the Depth as key with the node ID as the value
            while (! self::$db->EOF) {
                $children[self::$db->GetValue('Depth')][] = self::$db->GetValue('IdChild');
                self::$db->Next();
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
     * @param array $nodeTypeFlags
     * @return bool|array
     */
    public static function get_parents(int $idNode, string $value = null, string $index = null, array $nodeTypeFlags = null)
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
        if ($nodeTypeFlags) {
            
            // Filter by node type flags
            if (!$index and !$value) {
                
                // If the fields contain node or nodetype values, or node type flags, we need to make a join with Nodes table
                $sql .= ' inner join Nodes node on (node.IdNode = ft.IdChild)';
            }
            
            // We need to make a join to NodeType table in order to use the node type values and flags
            $sql .= ' inner join NodeTypes nt on (nt.IdNodeType = node.IdNodeType';
            foreach ($nodeTypeFlags as $flag => $value) {
                $sql .= ' and nt.' . $flag . ' is ' . (($value) ? 'true' : 'false');
            }
            $sql .= ')';
        }
        $sql .= ' where ft.IdChild = ' . $idNode . ' order by ft.Depth';
        if (!self::$db) {
            self::$db = new Db();
        }
        if (self::$db->Query($sql) === false) {
            Logger::error('Getting parents in FastTraverse with node: ' . $idNode . ' (' . self::$db->desErr . ')');
            return false;
        }
        $parents = array();
        while (! self::$db->EOF) {
            $parents[self::$db->GetValue('_index')] = self::$db->GetValue('_value');
            self::$db->Next();
        }
        return $parents;
    }
}
