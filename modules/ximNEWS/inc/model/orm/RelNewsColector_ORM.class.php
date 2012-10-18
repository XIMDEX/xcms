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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




 


ModulesManager::file('/inc/helper/GenericData.class.php');

class RelNewsColector_ORM extends GenericData   {
	var $_idField = 'IdRel';
	var $_table = 'RelNewsColector';
	var $_metaData = array(
				'IdRel' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true'),
				'IdNew' => array('type' => 'int(12)', 'not_null' => 'true'),
				'IdColector' => array('type' => 'int(12)', 'not_null' => 'true'),
				'State' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'SetAsoc' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'PosInSet' => array('type' => 'int(12)', 'not_null' => 'false'),
				'Page' => array('type' => 'int(12)', 'not_null' => 'false'),
				'PosInSet2' => array('type' => 'int(12)', 'not_null' => 'false'),
				'Page2' => array('type' => 'int(12)', 'not_null' => 'false'),
				'LangId' => array('type' => 'int(12)', 'not_null' => 'false'),
				'FechaIn' => array('type' => 'int(12)', 'not_null' => 'false'),
				'FechaOut' => array('type' => 'int(12)', 'not_null' => 'false'),
				'Version' => array('type' => 'int(12)', 'not_null' => 'false'),
				'SubVersion' => array('type' => 'int(12)', 'not_null' => 'false'),
				'IdCache' => array('type' => 'int(12)', 'not_null' => 'false')
				);
	var $IdRel;
	var $IdNew = 0;
	var $IdColector = 0;
	var $State = 'pending';
	var $SetAsoc;
	var $PosInSet;
	var $Page;
	var $PosInSet2;
	var $Page2;
	var $LangId;
	var $FechaIn;
	var $FechaOut;
	var $Version;
	var $SubVersion;
	var $IdCache;	
}
?>
