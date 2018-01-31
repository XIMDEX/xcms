<?php
    namespace Ximdex\Models;
    
    use Ximdex\Models\ORM\FastTraverseOrm;
    
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
        function getChildren(int $idNode, bool $nodeTypes = false, int $level = null, array $filters = [])
        {
            if ($idNode < 1)
                return false;
            $db = new \Ximdex\Runtime\Db();
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
         * @param int $idChildren
         * @return bool|string[]
         */
        function getParents(int $idNode)
        {
            if ($idNode < 1)
                return false;
            $db = new \Ximdex\Runtime\Db();
            $sql = 'select IdNode, Depth from FastTraverse where IdChild = ' . $idNode;
            if ($db->Query($sql) === false)
                return false;
            $parents = array();
            while (!$db->EOF)
            {
                $parents[$db->GetValue('Depth')][] = $db->GetValue('IdNode');
                $db->Next();
            }
            return $parents;
        }
    }