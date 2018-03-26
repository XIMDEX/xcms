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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\PipeCachesOrm;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;

define('CACHE_FOLDER', App::getValue('CacheRoot') . '/pipelines/');
define('DATA_FOLDER', App::getValue('FileRoot') . '/');
define('TMP_FOLDER', App::getValue('TempRoot') . '/');

/**
 * @brief Support the Cache system for the pipelines
 *
 * Supports the actions load cache and delete cache, the load cache make calls to
 * the PipeTransition class to get the transition content for a concrete status or
 * load the resulting transition from cache if it is already generated.
 */
class PipeCache extends PipeCachesOrm
{
    private $_args = NULL;
    private $_transition = NULL;

    /**
     * @param $id
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        if ($this->get('id') > 0) {
            $this->_transition = new PipeTransition($this->get('IdPipeTransition'));
        }
    }

    /**
     * @param $idVersion
     * @param $idTransition
     * @param $args
     * @param number $depth
     * @return boolean|string|NULL
     */
    function load($idVersion, $idTransition, $args = NULL, $depth = 0)
    {
        // Search in cache what we have
        Logger::info("PipeCache: Searching for cache resources for version: $idVersion  and transition: $idTransition");
        if (! isset($args['DISABLE_CACHE']) || ! $args['DISABLE_CACHE']) {
            $this->_args = $args;
            $results = $this->_getCache($idVersion, $idTransition);
            if ($results) {
                Logger::info('PipeCache: Previous cache resource has been found');
                $idCache = $this->_checkPropertyValues($idTransition, $results);
                if ($idCache) {
                    self::__construct($idCache);
                    if ($this->get('id') > 0) {
                        Logger::info("PipeCache: Cache was correctly estimated for a previous version. Version: $idVersion Transition: $idTransition", true);
                        return $this->_getPointer();
                    } else {
                        Logger::error("PipeCache: A cache was estimated but it doesn't exist. Version: $idVersion Transition: $idTransition");
                        return null;
                    }
                } else {
                    Logger::warning('PipeCache: there is a problem with the cache properties');
                }
            } else {
                Logger::info("PipeCache: Previous cache not found for version $idVersion and transition $idTransition");
            }
        } else {
            Logger::info('PipeCache: DISABLE CACHE is ACTIVE. No cache will be loaded');
        }
        
        // Si llegamos a este punto hay que regenerar la cache
        $this->_transition = new PipeTransition($idTransition);
        $previousTransition = $this->_transition->getPreviousTransition();
        if ($previousTransition) {
            Logger::info('PipeCache: Previous transition (' . $previousTransition . ') has been found for transition ' . $idTransition);
            $cache = new PipeCache();
            $pointer = $cache->load($idVersion, $previousTransition, $args, $depth + 1);
            if ($pointer) {
                return $this->_transition->generate($idVersion, $pointer, $args);
            }
            
            // the cache could not been loaded correctly, then data file will be loaded
            Logger::warning('PipeCache: There is a problem to obtain the cache data for transition ' . $idTransition 
                    . '. Obtaining no cache data instead');
        } else {
            Logger::info('PipeCache: There is no previous transition for transition ' . $idTransition . ' with version: ' . $idVersion);
        }
        if (isset($args['CONTENT'])) {
            
            // Generate the pointer file for the transition with the content given
            if (isset($_GET["nodeid"])) {
                $pointer = XIMDEX_ROOT_PATH . TMP_FOLDER . "preview_" . $_GET["nodeid"] . "_" . FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . TMP_FOLDER);
            } else {
                $pointer = XIMDEX_ROOT_PATH . TMP_FOLDER . FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . TMP_FOLDER);
            }
            if (! FsUtils::file_put_contents($pointer, $args['CONTENT'])) {
                Logger::error('PipeCache: Error writing file content for file ' . $pointer);
                return false;
            }
            $tmpFileToRemove = $pointer;
        } else {
            
            // Obtain the data from the version with no cache
            Logger::info('PipeCache: Getting data file for version ' . $idVersion);
            $version = new Version($idVersion);
            $pointer = XIMDEX_ROOT_PATH . DATA_FOLDER . $version->get('File');
            if (! $pointer) {
                Logger::error('PipeCache: There is not content for version ' . $idVersion . ' and transition ' . $idTransition);
                return false;
            }
        }
        Logger::info('PipeCache: Obtained file ' . $pointer . ' to generate the pipeline transition ' . $idTransition . ' for version ' . $idVersion);
        $res = $this->_transition->generate($idVersion, $pointer, $args);
        if (isset($tmpFileToRemove) and file_exists($tmpFileToRemove)) {
            @unlink($tmpFileToRemove);
        }
        return $res;
    }

    private function _getCache($idVersion, $idTransition, $allCaches = false)
    {
        if ($allCaches) {
            
            Logger::info('PipeCache: Loading all caches for version: ' . $idVersion . ' and transition: ' . $idTransition);
            $query = sprintf('Select pc.id' . ' FROM PipeCaches pc INNER JOIN PipeTransitions pt ON pc.IdPipeTransition = pt.id' . ' WHERE IdVersion = %s AND IdPipeTransition = %s', $idVersion, $idTransition);
        } else {
            
            Logger::info('PipeCache: Loading cache for version: ' . $idVersion . ' and transition: ' . $idTransition);
            $query = sprintf('Select pc.id' . ' FROM PipeCaches pc INNER JOIN PipeTransitions pt ON pc.IdPipeTransition = pt.id AND pt.Cacheable = 1' . ' WHERE IdVersion = %s AND IdPipeTransition = %s', $idVersion, $idTransition);
        }
        $result = $this->query($query, MONO, 'id');
        if (! $result) {
            Logger::info('PipeCache: There is not cache for the specified version: ' . $idVersion . ' and transition: ' . $idTransition);
        }
        return $result;
    }

    /**
     * Comprueba las propiedades de una cache y devuelve la cache que corresponde
     *
     * @param int $idTransition
     * @param array $idCaches
     */
    function _checkPropertyValues($idTransition, $idCaches)
    {
        Logger::info('PipeCache: Checking properties values for transition ' . $idTransition . ' and cache ' . print_r($idCaches, true));
        
        // Primero comprobamos si tenemos todos los argumentos, si no los tenemos damos un fatal
        if (empty($this->_args))
            $this->_args = array();
        $keys = array_keys($this->_args);
        $propertiesIds = array();
        $this->_transition = new PipeTransition($idTransition);
        if ($this->_transition->properties->count() > 0) {
            Logger::info('PipeCache: Properties values found');
            $this->_transition->properties->reset();
            while ($property = $this->_transition->properties->next()) {
                if (! $this->_searchKeyInArgs($property->get('Name'), $this->_args))
                    return false;
                $propertiesIds[] = $property->get('id');
                $association[$property->get('id')] = $property->get('Name');
            }
        } else {
            Logger::info('PipeCache: No properties values found');
            if (count($idCaches) == 1) {
                Logger::info('PipeCache: Getting cache ' . $idCaches[0]);
                return $idCaches[0];
            } else {
                Logger::fatal('PipeCache: last cache couldn\'t be estimated');
                return false;
            }
        }
        
        // Ahora por cada cache buscamos si tenemos un conjunto de tuplas en propertyValues que satisfagan nuestras condiciones
        $countProperties = count($propertiesIds);
        for ($i = 0; $i < $countProperties; $i ++) {
            $queryWhere[] = 'IdPipeProperty = %s';
        }
        if (isset($queryWhere)) {
            $queryArray[] = "(" . implode(' OR ', $queryWhere) . ")";
        }
        reset($idCaches);
        $queryArray[] = 'IdPipeCache = %s';
        while (list (, $idCache) = each($idCaches)) {
            $localQuery = implode(' AND ', $queryArray);
            $localArgs = array_merge($propertiesIds, array(
                $idCache
            ));
            $propertyValuesIterator = new \Ximdex\Pipeline\Iterators\IteratorPipePropertyValues($localQuery, $localArgs);
            $continue = false;
            while ($propertyValue = $propertyValuesIterator->next()) {
                if ($propertyValue->get('Value') != $this->_args[$association[$propertyValue->get('IdPipeProperty')]]) {
                    $continue = true;
                    break;
                }
            }
            if (! $continue) {
                Logger::info('PipeCache: Cache ' . $idCache . ' has been found for transition ' . $idTransition);
                return $idCache;
            }
        }
        return NULL;
    }

    private function _searchKeyInArgs($key, $args)
    {
        if (is_array($args)) {
            reset($args);
            while (list ($index, $value) = each($args)) {
                if ($index == $key) {
                    return $value;
                }
            }
        }
        return NULL;
    }

    private function _getPointer()
    {
        $cacheFile = XIMDEX_ROOT_PATH . CACHE_FOLDER . $this->get('File');
        if (is_file($cacheFile)) {
            return $cacheFile;
        }
        return false;
    }

    /**
     * Stores a cache for the given parameters
     *
     * @param $idVersion
     * @param $idTransition
     * @param $contentFile
     * @param $args
     * @return boolean
     */
    public function store($idVersion, $idTransition, & $contentFile, $args)
    {
        Logger::info("PipeCache: Storing cache for version $idVersion transition $idTransition args " . print_r($args, true));
        
        $this->_transition = new PipeTransition($idTransition);
        if (! ($this->_transition->get('id') > 0)) {
            Logger::fatal('PipeCache: Error storing cache, could not estimate the transition to which to associate the cache: ' . $idTransition);
            return false;
        }
        $caches = $this->_getCache($idVersion, $idTransition, true);
        $cacheFile = '';
        if (! empty($caches)) {
            if (count($caches) > 1) {
                Logger::warning('PipeCache: Multiple cache found we are going to delete them (should be only one)');
                foreach ($caches as $idPipeCache) {
                    $pipeCache = new PipeCache($idPipeCache);
                    $pipeCache->delete();
                }
            } elseif (count($caches) == 1) {
                $idCache = $this->_checkPropertyValues($idTransition, $caches);
                if ($idCache > 0) {
                    Logger::warning('PipeCache: Cache found returning file' . print_r($caches, true));
                    $pipeCache = new PipeCache($idCache);
                    $cacheFile = $pipeCache->get('File');
                }
            }
        }
        if (empty($cacheFile)) {
            Logger::info('PipeCache: Not found cache file');
            $this->set('IdVersion', $idVersion);
            $this->set('IdPipeTransition', $idTransition);
            $cacheFile = FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . CACHE_FOLDER);
            if (FsUtils::copy($contentFile, XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile, true)) {
                Logger::info('PipeCache: Saved ' . $contentFile . ' in the cache resource ' . XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile);
                $this->set('File', $cacheFile);
            } else {
                Logger::error('PipeCache: An error has ocurred while storing the cache file ' . $contentFile . ' in the target ' . XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile);
                return false;
            }
            Logger::info('PipeCache: Saving cache information...');
            $idCache = $this->add();
            if (! $idCache > 0) {
                Logger::error('PipeCache: An error has ocurred while storing the cache file information');
                return false;
            }
            Logger::info('PipeCache: Cache information was successfusly saved');
        } else {
            Logger::info('PipeCache: Found cache file ' . $cacheFile . ' (Saving in ' . XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile . ')');
            if (! FsUtils::copy($contentFile, XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile, true)) {
                Logger::error("PipeCache: There has been an error while replacing the cache file (Problem permissions on data/cache/pipelines)");
                return false;
            }
        }
        if (! isset($idCache)) {
            Logger::error("PipeCache: Cache ID not valid - idVersion: $idVersion idTransition: $idTransition file: $contentFile");
            return false;
        }
        $contentFile = XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile;
        if (empty($this->_transition->properties)) {
            return true;
        }
        $this->_transition->properties->reset();
        if ($this->_transition->properties->count() > 0) {
            $this->_transition->properties->reset();
            while ($property = $this->_transition->properties->next()) {
                $propertyValue = new \Ximdex\Pipeline\PipePropertyValue();
                $propertyValue->set('IdPipeProperty', $property->get('id'));
                $propertyValue->set('IdPipeCache', $idCache);
                $propertyValue->set('Value', $this->_searchKeyInArgs($property->get('Name'), $args));
                if (! $propertyValue->add()) {
                    Logger::error('PipeCache: Error while trying to store the property');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @see inc/helper/GenericData#delete()
     */
    public function delete()
    {
        if (! ($this->get('id') > 0))
            return false;
        $db = new \Ximdex\Runtime\Db();
        $query = sprintf("DELETE FROM PipePropertyValues WHERE IdPipeCache = %s", $db->sqlEscapeString($this->get('id')));
        $db->execute($query);
        FsUtils::delete(XIMDEX_ROOT_PATH . '/data/cache/pipelines/' . $this->get('File'));
        Logger::info('PipeCache: Deleted pipe property value with ID: ' . $db->sqlEscapeString($this->get('id')));
        return parent::delete();
    }

    public function upgradeCaches($oldIdVersion, $idVersion)
    {
        Logger::info('PipeCache: Upgrading pipeline cache from version ' . $oldIdVersion . ' to ' . $idVersion);
        $idPipeCaches = $this->find("id", "IdVersion=%s", array(
            $oldIdVersion
        ), MONO);
        $result = true;
        foreach ($idPipeCaches as $idPipeCache) {
            $pipeCache = new PipeCache($idPipeCache);
            $pipeCache->set("IdVersion", $idVersion);
            $result = $pipeCache->update() && $result;
        }
        return $result;
    }
}