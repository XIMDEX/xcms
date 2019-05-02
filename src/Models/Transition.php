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
use Ximdex\Utils\Factory;
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
        
        // Channel
        if (isset($args['CHANNEL']) and $args['CHANNEL']) {
            $channel = (int) $args['CHANNEL'];
        } else {
            $channel = null;
        }
        
        // Return the cache file if there is one for this transition
        if ($this->cacheable and (! isset($args['DISABLE_CACHE']) or $args['DISABLE_CACHE'] == 0)) {
            $id = $this->cache->load($versionId, $this->get('id'), $channel);
            if ($id) {
                
                // Cache content for this transition has been found, return the associated file content
                $cache = new TransitionCache($id);
                Logger::info('Cache file ' . $cache->get('file') . ' was found for version ' . $versionId . ' in ' . $transition 
                    . ' transition', false, 'magenta');
                $content = FsUtils::file_get_contents(XIMDEX_CACHE_PATH . '/' . $cache->get('file'));
                if ($content === false) {
                    throw new \Exception('Cannot load a transition cache file with name: ' . $cache->get('file'));
                }
                return $content;
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
            $content = (new static())->process($previosTransition->get('name'), $args, $versionId);
        } else {
            
            // This is the original transition, get content from version file or given content in args
            if (isset($args['CONTENT'])) {
                $content = $args['CONTENT'];
            } else {
                
                // Obtain the data file from the given version
                $version = new Version($versionId);
                if (! $version->get('IdVersion')) {
                    throw new \Exception('Cannot load a version with code: ' . $versionId);
                }
                $content = FsUtils::file_get_contents(XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $version->get('File'));
                if ($content === false) {
                    throw new \Exception('There is not content file for version ' . $versionId . ' (In transition ' . $transition . ')');
                }
            }
        }
        $content = $this->callback($content, $args, $versionId);
        
        // Cache generation
        if (! App::getValue('DisableCache') and $this->cacheable and $versionId) {
            $this->cache->store($versionId, $this->get('id'), $content, $channel);
        }
        return $content;
    }
    
    private function callback(string $content, array $args, int $versionId = null, string $function = 'transform') : string
    {
        // Do the transformation if method transform exists in the view class
        $factory = new Factory(self::CALLBACK_FOLDER, 'View');
        $viewClass = $factory->instantiate($this->get('viewClass'), null, 'Ximdex\Nodeviews');
        if (method_exists($viewClass, $function)) {
            $content = $viewClass->$function($versionId, $content, $args);
            if ($content === false) {
                throw new \Exception('Cannot make the transformation for class: ' . $this->get('viewClass') . ' and version: ' 
                    . $versionId);
            }
        } else {
            throw new \Exception('There is not a transformation method called ' . $function . ' for class: ' 
                . $this->get('viewClass'));
        }
        return $content;
    }
}
