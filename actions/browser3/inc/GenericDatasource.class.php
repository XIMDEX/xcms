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




ModulesManager::file('/actions/browser3/inc/IDatasource.iface.php');
ModulesManager::file('/actions/browser3/inc/AbstractDatasource.class.php');
ModulesManager::file('/inc/xvfs/XVFS.class.php');


 // Generic interface for data source control.

class GenericDatasource extends AbstractDatasource {

	const DS_COMPOSER = 'Composer';
	const DS_XVFS = 'XVFS';
	const DS_TAGS = 'tags';

	static protected $confFile = null;
	static protected $datasource = null;

//	public function __construct() {
//	}

	static public function & getDatasource($datasource, $conf=array()) {
		$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . '/actions/browser3/inc', 'Datasource_');
		$ds = $factory->instantiate($datasource, $conf);
		if (!is_object($ds)) {
			XMD_Log::fatal(_('A class which does not exist is trying to be requested: Datasource_') . $datasource);
		}

		return $ds;
	}


	static public function & getInstance($bpath) {

//		if (self::$datasource === null) {
			if (self::$confFile === null) {
				self::$confFile =  ModulesManager::file('/conf/browser.conf');
			}

			$datasource = self::$confFile['defaultDatasource'];

			if ($datasource == self::DS_XVFS) {
				foreach (self::$confFile['datasources'][self::$confFile['defaultDatasource']]['MOUNTPOINTS'] as $mp) {
					$ret = XVFS::mount($mp['mountpoint'], $mp['uri']);
					if (!$ret) {
						// Log this; already logged by xvfs
					}
				}

				$backEnd = XVFS::_getBackend($bpath);
				$backEndString = strtolower(get_class($backEnd));

				$backEndtoConst = array(
						'xvfs_backend_xnodes' => self::DS_XVFS,
						'xvfs_backend_tags' => self::DS_TAGS
						);

				if (array_key_exists($backEndString, $backEndtoConst)) {
					$datasource = $backEndtoConst[$backEndString];
				}
			}

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

			// NOTE: __nodeid comes from Datasource_XVFS
			$ret[] =
				isset($entity['__nodeid'])
				? $entity['__nodeid']
				: (
					isset($entity['nodeid'])
					? $entity['nodeid']
					: $entity['bpath']
					);

		}

		return $ret;
	}

}

?>