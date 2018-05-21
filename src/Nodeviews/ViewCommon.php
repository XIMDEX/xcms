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
namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Deps\LinksManager;
use Ximdex\Models\Version;
use Ximdex\Runtime\App;

class ViewCommon extends AbstractView implements IView
{

    private $_filePath;

    function transform($idVersion = NULL, $pointer = NULL, $args = NULL)
    {
        if (! $this->_setFilePath($idVersion, $args))
            return NULL;
        
        if (! is_file($this->_filePath)) {
            Logger::error('VIEW COMMON: Se ha solicitado cargar un archivo inexistente. FilePath: ' . $this->_filePath);
            return NULL;
        }
        
        if (! array_key_exists('REPLACEMACROS', $args)) {
            return $pointer;
        }
        
        // Replaces macros in content
        $content = $this->retrieveContent($this->_filePath);
        
        $linksManager = new LinksManager();
        $content = $linksManager->removeDotDot($content);
        $content = $linksManager->removePathTo($content);
        
        return $this->storeTmpContent($content);
    }

    private function _setFilePath($idVersion = NULL, $args = array())
    {
        if (! is_null($idVersion)) {
            $version = new Version($idVersion);
            $file = $version->get('File');
            $this->_filePath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $file;
        } else {
            // Retrieves Params:
            if (array_key_exists('FILEPATH', $args)) {
                $this->_filePath = $args['FILEPATH'];
            }
            // Check Params:
            if (! isset($this->_filePath) || $this->_filePath == "") {
                Logger::error('VIEW COMMON: No se ha especificado la version ni el path del fichero correspondiente al nodo ' . $args['NODENAME'] . ' que quiere renderizar');
                return NULL;
            }
        }
        
        return true;
    }
}