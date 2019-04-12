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
    public static function getChildren(int $idNode, array $fields = null, int $level = null, array $filters = null, array $nodeTypeFlags = null)
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
                if (! is_array($opFilters)) {
                    Logger::error(ucfirst($operation) . ' filters parameter must be an array of fields');
                    return false;
                }
                foreach ($opFilters as $field => $values) {
                    if (! is_array($values)) {
                        Logger::error('Filter parameter for ' . $field . ' field must be an array of fields');
                        return false;
                    }
                    $sql .= ' and ' . $field . ' ' . $operator . ' (' . implode(',', $values) . ')';
                }
            }
        }
        if (! self::$db) {
            self::$db = new Db();
        }
        if (self::$db->query($sql) === false) {
            return false;
        }
        $children = array();
        if ($fields) {
            
            // The returned array will have the Depth as the primary level, the node ID as the second with an array with the specfied fields
            while (! self::$db->EOF) {
                foreach ($fields as $source => $sourceFields) {
                    foreach ($sourceFields as $field) {
                        $children[self::$db->getValue('Depth')][self::$db->getValue('IdChild')][$source][$field] = self::$db->getValue($field);
                    }
                }
                self::$db->next();
            }
        } else {
            
            // The returned array will have the Depth as key with the node ID as the value
            while (! self::$db->EOF) {
                $children[self::$db->getValue('Depth')][] = self::$db->getValue('IdChild');
                self::$db->next();
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
     * @param int $limit
     * @return bool|array
     */
    public static function getParents(int $idNode, string $value = null, string $index = null, array $nodeTypeFlags = null, int $limit = null)
    {
        if ($idNode < 1) {
            Logger::error('Getting parents in FastTraverse without node given');
            return false;
        }
        if ($index or $value) {
            if (! $index) {
                $index = 'Depth';
            }
            if (! $value) {
                $value = 'ft.IdNode';
            } elseif ($value == 'IdNodeType') {
                $value = 'node.IdNodeType';
            }
            $sql = 'select ' . $value . ' as _value, ' . $index . ' as _index from FastTraverse ft';
            $sql .= ' inner join Nodes node on (node.IdNode = ft.IdNode)';
        } else {
            $sql = 'select Depth as _index, ft.IdNode as _value from FastTraverse ft';
        }
        if ($nodeTypeFlags) {
            
            // Filter by node type flags
            if (! $index and ! $value) {
                
                // If the fields contain node or nodetype values, or node type flags, we need to make a join with Nodes table
                $sql .= ' inner join Nodes node on (node.IdNode = ft.IdNode)';
            }
            
            // We need to make a join to NodeType table in order to use the node type values and flags
            $sql .= ' inner join NodeTypes nt on (nt.IdNodeType = node.IdNodeType';
            foreach ($nodeTypeFlags as $flag => $value) {
                $sql .= ' and nt.' . $flag . ' is ' . (($value) ? 'true' : 'false');
            }
            $sql .= ')';
        }
        $sql .= ' where ft.IdChild = ' . $idNode . ' order by ft.Depth';
        if ($limit > 0) {
            $sql .= ' limit ' . $limit;
        }
        if (! self::$db) {
            self::$db = new Db();
        }
        if (self::$db->query($sql) === false) {
            Logger::error('Getting parents in FastTraverse with node: ' . $idNode . ' (' . self::$db->desErr . ')');
            return false;
        }
        $parents = array();
        while (! self::$db->EOF) {
            $parents[self::$db->getValue('_index')] = self::$db->getValue('_value');
            self::$db->next();
        }
        return $parents;
    }
}
