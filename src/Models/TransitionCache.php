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
        'file' => array('type' => 'varchar(255)', 'not_null' => 'true')
    );
    public $_uniqueConstraints = [];
    public $_indexes = ['id'];
    public $id;
    public $versionId;
    public $transitionId;
    public $file;
    
    public function load(int $versionId, int $transitionId) : ?int
    {
        $id = $this->find('id', 'versionId = ' . $versionId . ' AND transitionId = ' . $transitionId, null, MONO);
        if ($id === false) {
            throw new \Exception('Cannot get a cache for transition: ' . $transitionId . ' and version: ' . $versionId);
        }
        if (! $id) {
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
    
    public function store(int $versionId, int $transitionId, string $contentFile)
    {
        $transition = new Transition($transitionId);
        if (! $transition->get('id')) {
            throw new \Exception('Error storing cache, could not estimate the transition to associate the cache for code: ' . $transitionId);
        }
        if (! $transition->get('cacheable')) {
            throw new \Exception('No cache will be stored for not cacheable transition: ' . $transitionId);
        }
        $file = FsUtils::getUniqueFile(XIMDEX_CACHE_PATH);
        if (! FsUtils::copy($contentFile, XIMDEX_CACHE_PATH . '/' . $file)) {
            throw new \Exception('An error has ocurred while storing the cache file from ' . $contentFile);
        }
        $this->versionId = $versionId;
        $this->transitionId = $transitionId;
        $this->file = $file;
        
        // If there is a cache previously stored overwrite it
        $id = $this->load($versionId, $transitionId);
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
        FsUtils::delete(XIMDEX_CACHE_PATH . '/' . $this->get('file'));
        return parent::delete();
    }
}
