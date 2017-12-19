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


use Ximdex\Logger;
use Ximdex\Runtime\Request;

ModulesManager::file('/actions/browser3/inc/IDatasource.iface.php', 'APP');
ModulesManager::file('/actions/browser3/inc/AbstractDatasource.class.php', 'APP');


 // Generic interface for data source control.

class GenericDatasource extends AbstractDatasource {

	const DS_COMPOSER = 'Composer';
	const DS_TAGS = 'tags';

	static protected $confFile = null;
	static protected $datasource = null;

	static public function & getDatasource($datasource, $conf=array()) {

		$factory = new \Ximdex\Utils\Factory(APP_ROOT_PATH . '/actions/browser3/inc', 'Datasource_');
		$ds = $factory->instantiate($datasource, $conf);

		if (!is_object($ds)) {
			Logger::fatal(_('A class which does not exist is trying to be requested: Datasource_') . $datasource);
		}

		return $ds;
	}


	static public function & getInstance($bpath) {

			if (self::$confFile === null) {
				self::$confFile =  ModulesManager::file('/conf/browser.php');
			}


			$datasource = self::$confFile['defaultDatasource'];

			$conf = self::$confFile['datasources'][self::$confFile['defaultDatasource']];


            self::$datasource = self::getDatasource($datasource, $conf);
//		}


		return self::$datasource;
	}

	static private function getPath($request) {
		$nodeInfo = $request->getParam('nodeid');

		return empty($nodeInfo) ? '/' : $nodeInfo;
	}

	static public function read($request, $recursive = true) {
		$ds = self::getInstance(GenericDataSource::getPath($request));
		$request->setParam('children', true);
		return $ds->read($request, $recursive);
	}

	static public function quickRead($request, $recursive = true) {
		$ds = self::getInstance(GenericDataSource::getPath($request));
		$request->setParam('children', true);
		return $ds->quickRead($request, $recursive);
	}

	static public function readFiltered($request, $recursive = true) {
		$ds = self::getInstance(GenericDataSource::getPath($request));
		$request->setParam('children', true);
		return $ds->readFiltered($request, $recursive);
	}

	static public function parents($request) {
		$ds = self::getInstance(GenericDataSource::getPath($request));
		return $ds->parents($request);
	}

	static public function search($request) {
		$path = '/';

		if ($request->getParam('filters') == 'tags') {
			$path = '/Tags';
		}

		$ds = self::getInstance($path);
		$results = $ds->search($request);
		return $results;
	}

	static public function write($request) {
		$ds = self::getInstance(GenericDataSource::getPath($request));
		return $ds->write($request);
	}

	static public function nodetypes($request) {
		$ds = self::getInstance(GenericDataSource::getPath($request));
		return $ds->nodetypes($request);
	}

	static public function normalizeEntities($entities) {

		$entities = !is_array($entities) ? array($entities) : $entities;

		$ret = array();
		$request = new Request();
		foreach ($entities as $entity) {

			if (empty($entity)) {
				continue;
			}

			$ds = self::getInstance($entity);
			if (is_numeric($entity)) {
				$ret[] = $entity;
				continue;
			}

			$request->setParam('nodeid', $entity);
			$request->setParam('children', false);
			$entity = $ds->read($request);

			$ret[] =  $entity['nodeid']?? $entity['bpath'];

		}

		return $ret;
	}

}