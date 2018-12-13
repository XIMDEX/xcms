<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Runtime\DataFactory;

define('CACHE_FOLDER', App::getValue('CacheRoot') . '/pipelines/');
define('DATA_FOLDER', App::getValue('FileRoot') . '/');
define('TMP_FOLDER', App::getValue('TempRoot') . '/');

/**
 * @deprecated
 * @brief Support the Cache system for the pipelines
 *
 * Supports the actions load cache and delete cache, the load cache make calls to
 * the PipeTransition class to get the transition content for a concrete status or
 * load the resulting transition from cache if it is already generated.
 */
class PipeCache extends PipeCachesOrm
{
    private $args;
    private $transition;

    /**
     * @param $id
     */
    public function __construct(int $id = null)
    {
        parent::__construct($id);
        if ($this->get('id') > 0) {
            $this->transition = new PipeTransition($this->get('IdPipeTransition'));
        }
    }

    /**
     * @param $idVersion
     * @param $idTransition
     * @param $args
     * @param number $depth
     * @return boolean|string|NULL
     */
    public function load(?int $idVersion, int $idTransition, array $args = [], int $depth = 0)
    {
        // Search in cache what we have
        Logger::debug('PipeCache: Searching for cache resources for version: ' . $idVersion . ' and transition: ' . $idTransition);
        if (! isset($args['DISABLE_CACHE']) || ! $args['DISABLE_CACHE']) {
            $this->args = $args;
            $results = $this->getCache($idVersion, $idTransition);
            if ($results) {
                Logger::debug('PipeCache: Previous cache resource has been found');
                $idCache = $this->_checkPropertyValues($idTransition, $results);
                if ($idCache) {
                    self::__construct($idCache);
                    if ($this->get('id') > 0) {
                        Logger::info('PipeCache: Cache was correctly estimated for a previous version. Version: ' . $idVersion 
                            . ' Transition: ' . $idTransition);
                        return $this->getPointer();
                    } else {
                        Logger::error('PipeCache: A cache was estimated but it doesn\'t exist. Version: ' . $idVersion 
                            . ' Transition: ' . $idTransition);
                        return null;
                    }
                } else {
                    Logger::warning('PipeCache: there is a problem with the cache properties');
                }
            } else {
                Logger::debug('PipeCache: Previous cache not found for version: ' . $idVersion . ' and transition: ' . $idTransition);
            }
        } else {
            Logger::debug('PipeCache: DISABLE CACHE is ACTIVE. No cache will be loaded');
        }
        
        // Si llegamos a este punto hay que regenerar la cache
        $this->transition = new PipeTransition($idTransition);
        $previousTransition = $this->transition->getPreviousTransition();
        if ($previousTransition) {
            Logger::debug('PipeCache: Previous transition (' . $previousTransition . ') has been found for transition ' . $idTransition);
            $cache = new PipeCache();
            $pointer = $cache->load($idVersion, $previousTransition, $args, $depth + 1);
            if ($pointer) {
                return $this->transition->generate($idVersion, $pointer, $args);
            }
            
            // the cache could not been loaded correctly, then data file will be loaded
            Logger::warning('PipeCache: There is a problem to obtain the cache data for transition ' . $idTransition 
                    . '. Obtaining no cache data instead');
        } else {
            Logger::debug('PipeCache: There is no previous transition for transition ' . $idTransition . ' with version: ' . $idVersion);
        }
        if (isset($args['CONTENT'])) {
            
            // Generate the pointer file for the transition with the content given
            if (isset($_GET['nodeid'])) {
                $pointer = XIMDEX_ROOT_PATH . TMP_FOLDER . 'preview_' . $_GET['nodeid'] . '_' . FsUtils::getUniqueFile(XIMDEX_ROOT_PATH 
                    . TMP_FOLDER);
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
            Logger::debug('PipeCache: Getting data file for version ' . $idVersion);
            $version = new Version($idVersion);
            $pointer = XIMDEX_ROOT_PATH . DATA_FOLDER . $version->get('File');
            if (! $pointer) {
                Logger::error('PipeCache: There is not content for version ' . $idVersion . ' and transition ' . $idTransition);
                return false;
            }
        }
        Logger::debug('PipeCache: Obtained file ' . $pointer . ' to generate the pipeline transition ' . $idTransition 
            . ' for version ' . $idVersion);
        $res = $this->transition->generate($idVersion, $pointer, $args);
        if (isset($tmpFileToRemove) and file_exists($tmpFileToRemove)) {
            @unlink($tmpFileToRemove);
        }
        return $res;
    }
    
    private function getCache(int $idVersion, int $idTransition, bool $allCaches = false)
    {
        $info = 'PipeCache: Loading all caches for version: ' . $idVersion . ' and transition: ' . $idTransition;
        if ($allCaches) {
            $info .= ' (All caches)';
        }
        Logger::debug($info);
        $query = sprintf('Select pc.id FROM PipeCaches pc INNER JOIN PipeTransitions pt ON pc.IdPipeTransition = pt.id'
            . ' WHERE IdVersion = %s AND IdPipeTransition = %s', $idVersion, $idTransition);
        if (! $allCaches) {
            $query .= ' AND pt.Cacheable = 1';
        }
        $result = $this->query($query, MONO);
        if (! $result) {
            Logger::debug('PipeCache: There is not cache for the specified version: ' . $idVersion . ' and transition: ' . $idTransition);
        }
        return $result;
    }

    /**
     * Comprueba las propiedades de una cache y devuelve la cache que corresponde
     *
     * @param int $idTransition
     * @param array $idCaches
     */
    function _checkPropertyValues(int $idTransition, array $idCaches)
    {
        Logger::debug('PipeCache: Checking properties values for transition ' . $idTransition . ' and cache ' . print_r($idCaches, true));
        
        // Primero comprobamos si tenemos todos los argumentos, si no los tenemos damos un fatal
        if (empty($this->args)) {
            $this->args = array();
        }
        $propertiesIds = array();
        $this->transition = new PipeTransition($idTransition);
        $association = [];
        if ($this->transition->properties->count() > 0) {
            Logger::debug('PipeCache: Properties values found');
            $this->transition->properties->reset();
            while ($property = $this->transition->properties->next()) {
                if (! $this->searchKeyInArgs($property->get('Name'), $this->args))
                    return false;
                $propertiesIds[] = $property->get('id');
                $association[$property->get('id')] = $property->get('Name');
            }
        } else {
            Logger::debug('PipeCache: No properties values found');
            if (count($idCaches) == 1) {
                Logger::debug('PipeCache: Getting cache ' . $idCaches[0]);
                return $idCaches[0];
            } else {
                Logger::fatal('PipeCache: last cache couldn\'t be estimated');
                return false;
            }
        }
        
        // Ahora por cada cache buscamos si tenemos un conjunto de tuplas en propertyValues que satisfagan nuestras condiciones
        $countProperties = count($propertiesIds);
        $queryWhere = [];
        for ($i = 0; $i < $countProperties; $i ++) {
            $queryWhere[] = 'IdPipeProperty = %s';
        }
        $queryArray = [];
        if (isset($queryWhere)) {
            $queryArray[] = '(' . implode(' OR ', $queryWhere) . ')';
        }
        reset($idCaches);
        $queryArray[] = 'IdPipeCache = %s';
        foreach ($idCaches as $idCache) {
            $localQuery = implode(' AND ', $queryArray);
            $localArgs = array_merge($propertiesIds, array(
                $idCache
            ));
            $propertyValuesIterator = new \Ximdex\Pipeline\Iterators\IteratorPipePropertyValues($localQuery, $localArgs);
            $continue = false;
            while ($propertyValue = $propertyValuesIterator->next()) {
                if ($propertyValue->get('Value') != $this->args[$association[$propertyValue->get('IdPipeProperty')]]) {
                    $continue = true;
                    break;
                }
            }
            if (! $continue) {
                Logger::debug('PipeCache: Cache ' . $idCache . ' has been found for transition ' . $idTransition);
                return $idCache;
            }
        }
        return null;
    }

    private function searchKeyInArgs($key, $args)
    {
        if (is_array($args)) {
            foreach ($args as $index => $value) {
                if ($index == $key) {
                    return $value;
                }
            }
        }
        return null;
    }

    private function getPointer()
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
     * @param int $idVersion
     * @param int $idTransition
     * @param string $contentFile
     * @param array $args
     * @return bool
     */
    public function store(int $idVersion, int $idTransition, string & $contentFile, array $args = []) : bool
    {
        Logger::debug('PipeCache: Storing cache for version: ' . $idVersion . ' transition: ' . $idTransition . ' args: ' . print_r($args, true));
        $this->transition = new PipeTransition($idTransition);
        if (! $this->transition->get('id')) {
            Logger::fatal('PipeCache: Error storing cache, could not estimate the transition to which to associate the cache: ' . $idTransition);
            return false;
        }
        if (! $this->transition->get('Cacheable')) {
            Logger::warning('No cache will be stored for not cacheable transition: ' . $idTransition);
            return true;
        }
        $caches = $this->getCache($idVersion, $idTransition, true);
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
            Logger::debug('PipeCache: Not found cache file');
            $this->set('IdVersion', $idVersion);
            $this->set('IdPipeTransition', $idTransition);
            
            // If this version is a published one (with version > 0 and subversion = 0) the cache file will be the previous one
            $version = new Version($idVersion);
            if ($version->get('Version') > 0 and $version->get('SubVersion') == 0) {
                $dataFactory = new DataFactory($version->get('IdNode'));
                $previousVersionId = $dataFactory->GetPreviousVersion($idVersion);
                if (! $previousVersionId) {
                    Logger::error('Cannot load a previous cache version for ' . $version->get('Version') . '.0');
                    return false;
                }
                $previousVersion = new Version($previousVersionId);
                $previousCache = new static();
                if ($pointer = $previousCache->load($previousVersion->get('IdVersion'), $idTransition, $args)) {
                    $cacheFile = basename($pointer);
                }
            }
            if (! $cacheFile) {
                $cacheFile = FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . CACHE_FOLDER);
                if (FsUtils::copy($contentFile, XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile, true)) {
                    Logger::debug('PipeCache: Saved ' . $contentFile . ' in the cache resource ' . XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile);
                } else {
                    Logger::error('PipeCache: An error has ocurred while storing the cache file ' . $contentFile . ' in the target ' 
                        . XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile);
                    return false;
                }
            }
            $this->set('File', $cacheFile);
            Logger::debug('PipeCache: Saving cache information...');
            $idCache = $this->add();
            if (! $idCache) {
                Logger::error('PipeCache: An error has ocurred while storing the cache file information');
                return false;
            }
            Logger::debug('PipeCache: Cache information was successfusly saved');
        } else {
            Logger::debug('PipeCache: Found cache file ' . $cacheFile . ' (Saving in ' . XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile . ')');
            if (! FsUtils::copy($contentFile, XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile, true)) {
                Logger::error('PipeCache: There has been an error while replacing the cache file (Problem permissions on data/cache/pipelines)');
                return false;
            }
        }
        if (! isset($idCache)) {
            Logger::error('PipeCache: Cache ID not valid - idVersion: ' . $idVersion . ' idTransition: ' . $idTransition 
                . ' file: ' . $contentFile);
            return false;
        }
        $contentFile = XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile;
        if (empty($this->transition->properties)) {
            return true;
        }
        $this->transition->properties->reset();
        if ($this->transition->properties->count() > 0) {
            $this->transition->properties->reset();
            while ($property = $this->transition->properties->next()) {
                $propertyValue = new \Ximdex\Pipeline\PipePropertyValue();
                $propertyValue->set('IdPipeProperty', $property->get('id'));
                $propertyValue->set('IdPipeCache', $idCache);
                $propertyValue->set('Value', $this->searchKeyInArgs($property->get('Name'), $args));
                if (! $propertyValue->add()) {
                    Logger::error('PipeCache: Error while trying to store the property');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::delete()
     */
    public function delete()
    {
        if (! $this->get('id')) {
            return false;
        }
        $db = new \Ximdex\Runtime\Db();
        $query = sprintf('DELETE FROM PipePropertyValues WHERE IdPipeCache = %s', $db->sqlEscapeString($this->get('id')));
        $db->execute($query);
        FsUtils::delete(XIMDEX_ROOT_PATH . '/data/cache/pipelines/' . $this->get('File'));
        Logger::debug('PipeCache: Deleted pipe property value with ID: ' . $db->sqlEscapeString($this->get('id')));
        return parent::delete();
    }

    public function upgradeCaches(int $oldIdVersion, int $idVersion)
    {
        Logger::debug('PipeCache: Upgrading pipeline cache from version ' . $oldIdVersion . ' to ' . $idVersion);
        $idPipeCaches = $this->find('id', 'IdVersion = %s', array(
            $oldIdVersion
        ), MONO);
        $result = true;
        foreach ($idPipeCaches as $idPipeCache) {
            $pipeCache = new PipeCache($idPipeCache);
            $pipeCache->set('IdVersion', $idVersion);
            $result = $pipeCache->update() && $result;
        }
        return $result;
    }
}
