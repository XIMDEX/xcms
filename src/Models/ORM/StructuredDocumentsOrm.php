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

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

class StructuredDocumentsOrm extends GenericData
{
    var $_idField = 'IdDoc';
    var $_table = 'StructuredDocuments';
    var $_metaData = array(
        'IdDoc' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'IdCreator' => array('type' => "int(12)", 'not_null' => 'false'),
        'CreationDate' => array('type' => "timestamp", 'not_null' => 'true'),
        'UpdateDate' => array('type' => "timestamp", 'not_null' => 'true'),
        'IdLanguage' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdTemplate' => array('type' => "int(12)", 'not_null' => 'true'),
        'TargetLink' => array('type' => "int(12)", 'not_null' => 'false'),
        'XsltErrors' => array('type' => "text", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('IdDoc');
    var $IdDoc = 0;
    var $Name;
    var $IdCreator = 0;
    var $CreationDate = 'CURRENT_TIMESTAMP';
    var $UpdateDate = 'CURRENT_TIMESTAMP';
    var $IdLanguage = 0;
    var $IdTemplate = 0;
    var $TargetLink;
    var $XsltErrors;
}