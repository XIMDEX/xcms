<?php 
/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/helper/GenericData.class.php');

class PublishingReport_ORM extends GenericData   {
	var $_idField = 'IdReport';
	var $_table = 'PublishingReport';
	var $_metaData = array(
				'IdReport' => array('type' => 'int(11)', 'not_null' => 'true', 'auto_increment' => 'true'),
				'IdSection' => array('type' => 'int(11)', 'not_null' => 'false'),
				'IdNode' => array('type' => 'int(11)', 'not_null' => 'false'),
				'IdChannel' => array('type' => 'int(11)', 'not_null' => 'false'),
				'IdSyncServer' => array('type' => 'int(11)', 'not_null' => 'false'),
				'IdPortalVersion' => array('type' => 'int(11)', 'not_null' => 'false'),
				'PubTime' => array('type' => 'int(11)', 'not_null' => 'true'),
				'State' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'Progress' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'FileName' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'FilePath' => array('type' => 'varchar(255)', 'not_null' => 'false'),
				'IdSync' => array('type' => 'int(11)', 'not_null' => 'false'),
				'IdBatch' => array('type' => 'int(11)', 'not_null' => 'false'),
				'IdParentServer' => array('type' => 'int(11)', 'not_null' => 'false')
				);
	var $IdReport;
	var $IdSection;
	var $IdNode;
	var $IdChannel;
	var $IdSyncServer;
	var $IdPortalVersion;
	var $PubTime;
	var $State;
	var $Progress;
	var $FileName;
	var $FilePath;	
	var $IdSync;
	var $IdBatch;
	var $IdParentServer;
}
?>
