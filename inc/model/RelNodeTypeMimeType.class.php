<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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




class RelNodeTypeMimeType  extends \Ximdex\Data\GenericData
{

    var $_idField = 'idRelNodeTypeMimeType';
    var $_table = 'RelNodeTypeMimeType';
    var $_metaData = array(
        'idRelNodeTypeMimeType' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'idNodeType' => array('type' => "int(12)", 'not_null' => 'true'),
        'extension' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'filter' => array('type' => "char(50)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('idRelNodeTypeMimeType');
    var $idRelNodeTypeMimeType;
    var $idNodeType = 0;
    var $extension;
    var $filter;

    function getFileExtension($nodetype)
    {
        $filter = $this->find('filter', 'idnodetype = %s', array($nodetype), MONO);
        if (strcmp($filter[0], 'ptd') == 0) {
            $ext = ($nodetype == \Ximdex\NodeTypes\NodeType::TEMPLATE) ? "xml" : "xsl";
        } elseif (strcmp($filter[0], 'pvd') == 0) {
            $ext = "xml";
        } else {
            $ext = $filter[0];
        }
        return $ext;
    }
}