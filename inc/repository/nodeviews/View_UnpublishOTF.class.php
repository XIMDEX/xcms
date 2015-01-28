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



if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/TarArchiver.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/repository/nodeviews/View_SQL.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_UnpublishOTF extends Abstract_View implements Interface_View {

	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		if (!array_key_exists('CHANNEL', $args)) {
			XMD_Log::error('channel is mandatory');
			return NULL;
		}
		
		if (!array_key_exists('NODEID', $args)) {
			XMD_Log::error('nodeid is mandatory');
			return NULL;
		}

		$nodeName = $args['NODENAME'];
		$nodeId = $args['NODEID'];
		
		// Generates the sql to unpublish bulletin

		$sqlContent = $this->getSQLContent($nodeId);

		$tmpFolder = XIMDEX_ROOT_PATH . \App::getValue( 'TempRoot');
		$tarFile = $tmpFolder . '/' . $nodeName;
		$tmpSqlFile = $tmpFolder  . $nodeName . '.sql';

		if (!FsUtils::file_put_contents($tmpSqlFile, $sqlContent)) {
			return false;
		}

		// Making tar file

		$tarArchiver = new TarArchiver($tarFile);
		$tarArchiver->addEntity($tmpSqlFile);
		$tarFileName = $tarArchiver->pack();

		return $tarFileName;
	}
	
	private function getSQLContent($nodeId) {
		
		$deleteQuery = "DELETE FROM XimNewsColector;
						DELETE FROM RelNewsColector;";

		$insertQuery .= View_SQL::makeInsertQuery('XimNewsColector', '', 'NULL');
		$insertQuery .= View_SQL::makeInsertQuery('RelNewsColector', '', 'NULL');

		$sql = $deleteQuery . $insertQuery;

		return $sql;
	}
}

?>