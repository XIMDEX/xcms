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

class XimNewsBulletins_ORM extends GenericData   {
	var $_idField = 'IdBulletin';
	var $_table = 'XimNewsBulletins';
	var $_metaData = array(
				'IdBulletin' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
				'IdContainer' => array('type' => "int(12)", 'not_null' => 'true'),
				'IdColector' => array('type' => "int(12)", 'not_null' => 'true'),
				'IdLote' => array('type' => "int(12)", 'not_null' => 'false'),
				'Fecha' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'SetAsoc' => array('type' => "varchar(255)", 'not_null' => 'false'),
				'State' => array('type' => "varchar(255)", 'not_null' => 'true')
				);
	var $_uniqueConstraints = array(

				);
	var $_indexes = array('IdBulletin');
	var $IdBulletin = 0;
	var $IdContainer = 0;
	var $IdColector = 0;
	var $IdLote;
	var $Fecha = 0;
	var $SetAsoc;
	var $State = 'generated';
}
?>