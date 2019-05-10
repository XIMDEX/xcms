<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Data\GenericData;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Runtime\DataFactory;

// Path to store the cache files
if (! defined('XIMDEX_CACHE_PATH')) {
    define('XIMDEX_CACHE_PATH', XIMDEX_ROOT_PATH . App::getValue('CacheRoot') . '/transitions');
}

class TransitionCache extends GenericData
{
    public $_idField = 'id';
    
    public $_table = 'TransitionsCache';
    
    public $_metaData = array(
        'id' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'versionId' => array('type' => 'int(12)', 'not_null' => 'true'),
        'transitionId' => array('type' => 'int(12)', 'not_null' => 'true'),
        'file' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'channelId' => array('type' => 'int(12)', 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = [['versionId', 'transitionId', 'channelId']];
    
    public $_indexes = ['id', 'versionId', 'transitionId', 'channelId'];
    
    public $id;
    
    public $versionId;
    
    public $transitionId;
    
    public $file;
    
    public $channelId;
    
    public function load(int $versionId, int $transitionId, int $channelId = null) : ?int
    {
        $criteria = 'versionId = ' . $versionId . ' AND transitionId = ' . $transitionId;
        if ($channelId) {
            $criteria .= ' AND channelId = ' . $channelId;
        } else {
            $criteria .= ' AND channelId IS NULL';
        }
        $id = $this->find('id', $criteria, null, MONO);
        if ($id === false) {
            throw new \Exception('Cannot get a cache for transition: ' . $transitionId . ' and version: ' . $versionId);
        }
        if (! $id) {
            
            // If the version is a published one (ex. 5.0), will search the previous cache saved (PREV.MAX)
            $version = new Version($versionId);
            if ($version->get('SubVersion') == 0 and $version->get('Version') > 0) {
                $data = new DataFactory($version->get('IdNode'));
                if ($id = $data->getPreviousVersion($versionId)) {
                    return $this->load($id, $transitionId, $channelId);
                }
            }
            return null;
        }
        $id = (int) $id[0];
        
        // Check cache data file: no file, will remove the database info
        $cache = new static($id);
        if (! file_exists(XIMDEX_CACHE_PATH . '/' . $cache->get('file'))) {
            $cache->delete();
            return null;
        }
        return $id;
    }
    
    public function store(int $versionId, int $transitionId, string $content, int $channelId = null)
    {
        $transition = new Transition($transitionId);
        if (! $transition->get('id')) {
            throw new \Exception('Error storing cache, could not estimate the transition to associate the cache for code: ' . $transitionId);
        }
        if (! $transition->get('cacheable')) {
            throw new \Exception('No cache will be stored for not cacheable transition: ' . $transitionId);
        }
        $file = FsUtils::getUniqueFile(XIMDEX_CACHE_PATH);
        if (! FsUtils::file_put_contents(XIMDEX_CACHE_PATH . '/' . $file, $content)) {
            throw new \Exception('An error has ocurred while storing the cache file');
        }
        $this->versionId = $versionId;
        $this->transitionId = $transitionId;
        $this->channelId = $channelId;
        $this->file = $file;
        
        // If there is a cache previously stored overwrite it
        $id = $this->load($versionId, $transitionId, $channelId);
        if ($id) {
            $this->id = $id;
            if (! $this->update()) {
                throw new \Exception('An error has ocurred while updating the transition cache data');
            }
        }
        elseif (! $this->add()) {
            throw new \Exception('An error has ocurred while creating the transition cache data');
        }
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
        $file = XIMDEX_CACHE_PATH . '/' . $this->get('file');
        if (FsUtils::file_exists($file)) {
            FsUtils::delete($file);
        }
        return parent::delete();
    }
}
