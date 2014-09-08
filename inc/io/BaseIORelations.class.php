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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/io/BaseIOConstants.php');
ModulesManager::file('/inc/model/RelNewsColector.php', 'ximNEWS');

class BaseIORelations {
	/**
	 * 
	 * @param $data
	 * @return unknown_type
	 */
	function build($data) {
		if (!isset($data['NODETYPENAME'])) {
			return ERROR_INCORRECT_DATA;
		}
		$relationType = $data['NODETYPENAME'];
		switch ($relationType) {
			case 'RELNEWSCOLECTOR':
				//var_dump($data);
				$rnc = new RelNewsColector();
				$rnc->set('IdNew', $data['IDNEW']);
				$rnc->set('IdColector', $data['IDCOLECTOR']);
				$rnc->set('State', $data['STATE']);
				$rnc->set('SetAsoc', $data['SETASOC']);
				$rnc->set('PosInSet', $data['POSINSET']);
				$rnc->set('Page', $data['PAGE']);
				$rnc->set('PosInSet2', $data['POSINSET2']);
				$rnc->set('Page2', $data['PAGE2']);
				$rnc->set('LangId', $data['LANGID']);
				$rnc->set('FechaIn', $data['FECHAIN']);
				$rnc->set('FechaOut', $data['FECHAOUT']);
				$rnc->set('Version', $data['VERSION']);
				$rnc->set('SubVersion', $data['SUBVERSION']);
				$rnc->set('IdCache', $data['IDCACHE']);
				$result = $rnc->add();
				
				//var_dump($rnc->messages);
				if (!($result > 0)) {
					XMD_Log::warning(_('Error inserting information about relnewscolector relation (BaseIORelations)'));
				}
				break;
		}
		return ERROR_INCORRECT_DATA;
	}
}
?>