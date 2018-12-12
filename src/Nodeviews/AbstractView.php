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

namespace Ximdex\Nodeviews;

use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;
use Ximdex\Models\Channel;
use Ximdex\Models\Node;

abstract class AbstractView implements IView
{
    protected $node;
    protected $channel;
    
    public function transform(int $idVersion = null, string $pointer = null, array $args = null)
    {
        Logger::info('Transforming with ' . class_basename($this));
     
        // Base node
        if (! isset($args['NODEID']) || empty($args['NODEID'])) {
            Logger::error('Argument nodeId not found in ViewPrepareHTML');
            return null;
        }
        $node = new Node($args['NODEID']);
        if (! $node->GetID()) {
            Logger::error('Node not found for ID: ' . $args['NODEID']);
            return null;
        }
        $this->node = $node;
        
        // Channel
        if (isset($args['CHANNEL']) and $args['CHANNEL']) {
            $channel = new Channel($args['CHANNEL']);
            if (! $channel->GetID()) {
                Logger::error('Channel not found for ID: ' . $args['CHANNEL']);
                return null;
            }
            $this->channel = $channel;
        } else {
            $this->channel = null;
        }
        return '';
    }
    
    public function storeTmpContent(string $content) : ?string
    {
        // Si el contenido es una variable que contiene false ha ocurrido un error
        if ($content !== false)
        {
            $basePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . '/';
            $pointer = FsUtils::getUniqueFile($basePath);
            if (isset($_GET['nodeid'])) {
                $file = $basePath . 'preview_' . $_GET['nodeid'] . '_' . $pointer;
            } else {
                $file = $basePath . $pointer;
            }
            Logger::debug('Storing temporal file in ' . $file);
            if (FsUtils::file_put_contents($file, $content)) {
                Logger::info($file . ' has been saved');
                return $file;
            }
        }
        if (isset($file)) {
            Logger::error('An error has happened trying to store the temporal file with content ' . $file);
        }
        else {
            Logger::error('An error has happened with content to save (previous error)');
        }
        return null;
    }

    public function retrieveContent(string $pointer)
    {
        return FsUtils::file_get_contents($pointer);
    }
}
