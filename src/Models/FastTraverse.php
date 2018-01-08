<?php
    namespace Ximdex\Models;
    
    use Ximdex\Models\ORM\FastTraverseOrm;
    
    class FastTraverse extends FastTraverseOrm
    {
        /**
         * Getting an array with all the child nodes which depend of a parent node given
         * This array contains the IdNode field with the Depth value in its index
         * If the parameter nodeTypes is a true value, the index of each node will be its ID and the value the Node Type ID
         * @param int $idNode
         * @param bool $nodeTypes
         * @param int $level
         * @return bool|string[]
         */
        function getChildren(int $idNode, bool $nodeTypes = false, int $level = null)
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
            if ($db->Query($sql) === false)
                return false;
            $children = array();
            if ($nodeTypes)
            {
                while (!$db->EOF)
                {
                    $children[$db->GetValue('Depth')][$db->GetValue('IdChild')] = $db->GetValue('IdNodeType');
                    $db->Next();
                }
            }
            else
            {
                while (!$db->EOF)
                {
                    $children[$db->GetValue('Depth')][] = $db->GetValue('IdChild');
                    $db->Next();
                }
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