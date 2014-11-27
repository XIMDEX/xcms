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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) .  "/../../"));
}

require_once(XIMDEX_ROOT_PATH . '/services/pull/Pull.class.php');

// authentication

if (\Ximdex\Utils\Session::check()) {


	\Ximdex\Utils\Session::set('context', 'ximdex');

	$args = array('idportal' => Request::get('idportal'), 'idnode' => Request::get('idnode'),
			'idchannel' => Request::get('idchannel'));

	$method = Request::get('method');
	$method = empty($method) ? 'getcontent' : $method;

	$pull = new Pull();

	$idPortalVersion = Request::get('idportalversion');
	$args['idportalversion'] = empty($idPortalVersion) ? $pull->get_current_portal_version($args) : $idPortalVersion;

	echo $pull->$method($args);
} else {
	echo 'Access denied';
}
?>