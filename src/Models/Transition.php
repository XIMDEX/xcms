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

use Ximdex\Logger;
use Ximdex\Data\GenericData;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\Factory;
use Ximdex\Nodeviews\AbstractView;
use Ximdex\Utils\FsUtils;

class Transition extends GenericData
{
    const CALLBACK_FOLDER = XIMDEX_ROOT_PATH . '/src/Nodeviews/';
    
    public $_idField = 'id';
    public $_table = 'Transitions';
    public $_metaData = array(
        'id' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'cacheable' => array('type' => 'tinyint(1)', 'not_null' => 'true'),
        'name' => array('type' => 'varchar(50)', 'not_null' => 'true'),
        'viewClass' => array('type' => 'varchar(50)', 'not_null' => 'false'),
        'previousTransitionId' => array('type' => 'int(12)', 'not_null' => 'false')
    );
    public $_uniqueConstraints = ['name'];
    public $_indexes = ['id'];
    public $id;
    public $cacheable = 0;
    public $name;
    public $viewClass = null;
    public $previousTransitionId = null;
    
    public function __construct(int $id = 0)
    {
        $this->cache = new TransitionCache();
        parent::__construct($id);
    }
    
    public function getPreviousTransition() : ?int
    {
        if (! $this->previousTransitionId) {
            return null;
        }
        $id = $this->find('id', 'id = ' . $this->previousTransitionId, null, MONO);
        if ($id === false) {
            throw new \Exception('Cannot get a previous transition for ' . $this->name);
        }
        if (! $id) {
            return null;
        }
        return (int) $id[0];
    }
    
    public function process(string $transition, array $args, int $versionId = null) : string
    {
        Logger::info('Processing transition: ' . $transition . ' for version: ' . $versionId);
        
        // Load transition for the given process name
        $id = $this->find('id', 'name = \'' . $transition . '\'', null, MONO);
        if (! isset($id[0])) {
            throw new \Exception('Cannot load a transition with process name: ' . $transition);
        }
        $this->__construct($id[0]);
        
        // Return the cache file if there is one for this transition
        if ($this->cacheable and (! isset($args['DISABLE_CACHE']) or $args['DISABLE_CACHE'] == 0)) {
            $id = $this->cache->load($versionId, $this->get('id'));
            if ($id) {
                
                // Cache content for this transition has been found, return the associated file
                $cache = new TransitionCache($id);
                Logger::info('Cache file ' . $cache->get('file') . ' was found for version ' . $versionId . ' in ' 
                    . $transition . ' transition', false, 'magenta');
                return XIMDEX_CACHE_PATH . '/' . $cache->get('file');
            }
        }
        
        // Load previous transition
        $previousTransitionId = $this->getPreviousTransition();
        if ($previousTransitionId) {
            
            // Load content from previous transition
            $previosTransition = new static($previousTransitionId);
            if (! $previosTransition->get('id')) {
                throw new \Exception('Cannot load a previous transition with code: ' . $previousTransitionId);
            }
            $pointer = (new static())->process($previosTransition->get('name'), $args, $versionId);
        } else {
            
            // This is the original transition, get content from version file or given content in args
            if (isset($args['CONTENT'])) {
                
                // Generate the pointer file for the transition with the content given
                if (! $pointer = AbstractView::storeTmpContent($args['CONTENT'])) {
                    throw new \Exception('Cannot write a transition content to temporal file in transition: ' . $transition);
                }
            } else {
                
                // Obtain the data file from the given version
                $version = new Version($versionId);
                if (! $version->get('IdVersion')) {
                    throw new \Exception('Cannot load a version with code: ' . $versionId);
                }
                $pointer = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $version->get('File');
                if (! $pointer) {
                    throw new \Exception('There is not content file for version ' . $versionId . ' and transition ' . $transition);
                }
            }
        }
        return $this->callback($pointer, $args, $versionId);
    }
    
    private function callback(string $pointer, array $args, int $versionId = null, string $function = 'transform') : string
    {
        // If this version is a published one (with version > 0 and subversion = 0) the cache file will be the previous one
        $version = new Version($versionId);
        if ($version->get('Version') > 0 and $version->get('SubVersion') == 0) {
            
            // Get previous version
            $dataFactory = new DataFactory($version->get('IdNode'));
            if (! $id = $dataFactory->GetPreviousVersion($versionId)) {
                throw new \Exception('Cannot load a previous version for ' . $version->get('Version') . '.0');
            }
            $previousVersion = new Version($id);
            
            // Get the previous cache if exists and use it instead doing a new tranformation
            if ($id = (new TransitionCache())->load($previousVersion->get('IdVersion'), $this->id)) {
                $cache = new TransitionCache($id);
                if (! file_exists(XIMDEX_CACHE_PATH . '/' . $cache->get('file'))) {
                    Logger::warning('Transition cache with non-existant file in system storage with code: ' . $cache->get('id') 
                        . '. Ignoring it');
                } else {
                    return XIMDEX_CACHE_PATH . '/' . $cache->get('file');
                }
            }
        }
        
        // Do the transformation if method transform exists in the view class
        $factory = new Factory(self::CALLBACK_FOLDER, 'View');
        $viewClass = $factory->instantiate($this->get('viewClass'), null, 'Ximdex\Nodeviews');
        if (method_exists($viewClass, $function)) {
            $transformedPointer = $viewClass->$function($versionId, $pointer, $args);
            
            // Remove possible temporal file
            if (strpos($pointer, XIMDEX_ROOT_PATH . App::getValue('TempRoot')) === 0) {
                FsUtils::delete($pointer);
            }
            if ($transformedPointer === false or $transformedPointer === null) {
                throw new \Exception('Cannot make the transformation for class: ' . $this->get('viewClass') . ' with file: ' . $pointer);
            }
            
            // Cache generation
            if ($this->cacheable and (! isset($args['DISABLE_CACHE']) or $args['DISABLE_CACHE'] == 0)) {
                $this->cache->store($versionId, $this->get('id'), $transformedPointer);
            }
        } else {
            $transformedPointer = $pointer;
        }
        return $transformedPointer;
    }
}
