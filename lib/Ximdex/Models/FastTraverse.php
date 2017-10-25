<?php
    namespace Ximdex\Models;
    
    use Ximdex\Models\ORM\FastTraverseOrm;
    use Ximdex\Runtime\Db;
    
    class FastTraverse extends FastTraverseOrm
    {
        /**
         * Getting an array with all the child nodes which depend of a parent node given
         * This array contains the IdNode field with the Depth value in its index
         * @param int $idNode
         * @return bool|string[]
         */
        function getChildren(int $idNode)
        {
            if ($idNode < 1)
                return false;
            $db = new Db();
            $sql = 'select idChild, Depth from FastTraverse where idNode = ' . $idNode;
            if ($db->Query($sql) === false)
                return false;
            $children = array();
            while (!$db->EOF)
            {
                $children[$db->GetValue('Depth')][] = $db->GetValue('idChild');
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
            $db = new Db();
            $sql = 'select IdNode, Depth from FastTraverse where idChild = ' . $idNode;
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