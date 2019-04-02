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

use Ximdex\Models\ORM\ChannelsOrm;

class Channel extends ChannelsOrm
{
    public $nodeID;
    
    public $autoCleanErr;
    
    public $msgErr;
    
    public $errorList = array
    (
        1 => 'Database connection error',
        2 => 'Channel does not exist',
        3 => 'Database inconsistency'
    );
    
    public $flagErr;
    
    public $numErr;
    
    const RENDERTYPE_STATIC = 'static';
    
    const RENDERTYPE_INCLUDE = 'include';
    
    const RENDERTYPE_DYNAMIC = 'dynamic';
    
    const RENDERTYPE_INDEX = 'index';
    
    const RENDER_TYPES = [self::RENDERTYPE_STATIC => 'Static', self::RENDERTYPE_INCLUDE => 'Include', self::RENDERTYPE_DYNAMIC => 'Dynamic'
        , self::RENDERTYPE_INDEX => 'Ximdex Index Format'];
    
    const JSON_CHANNEL = 10010;

    public function getChannelsForNode(int $idNode)
    {
        $node = new Node($idNode);
        $channs = $node->getProperty('channel');
        if (! is_array($channs)) {
            
            // Inherits the system properties
            $channs = array();
            $systemChannels = $this->find('IdChannel');
            foreach ($systemChannels as $channel) {
                $channs[] = $channel['IdChannel'];
            }
        }
        $channels = array();
        if (count($channs) > 0) {
            foreach ($channs as $channelId) {
                $channel = new Channel($channelId);
                $channels[] = array(
                    'IdChannel' => $channelId,
                    'Name' => $channel->get('Name'),
                    'Description' => $channel->get('Description')
                );
            }
        }
        return count($channels) > 0 ? $channels : null;
    }

    public function getAllChannels(array $order = null)
    {
        $validDirs = array('ASC', 'DESC');
        $sql = 'SELECT IdChannel FROM Channels';
        if (! empty($order) && is_array($order) && isset($order['FIELD'])) {
            $sql .= sprintf(' ORDER BY %s %s', $order['FIELD'],
                isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = [];
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->GetValue('IdChannel');
            $dbObj->Next();
        }
        return $salida ? $salida : NULL;
    }

    public function getID()
    {
        if ($this->get('IdChannel')) {
            return $this->get('IdChannel');
        }
        $this->setError(2);
        return false;
    }

    public function getName()
    {
        return $this->get('Name');
    }

    /**
     * Changes the current channel name
     * 
     * @param string $name
     * @return boolean|string|NULL|number
     */
    public function setName(string $name)
    {
        if (! $this->get('IdChannel')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current channel description
     * 
     * @return string (description)
     */
    public function getDescription()
    {
        return $this->get('Description');
    }

    /**
     * Changes the current channel description.
     * 
     * @param string $description
     * @return boolean|string|NULL|number
     */
    public function setDescription(string $description)
    {
        if (! $this->get('IdChannel')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current channel extension by default
     * 
     * @return string (description)
     */
    public function getExtension()
    {
        return $this->get('DefaultExtension');
    }

    /**
     * Changes the default extension for current channel
     * 
     * @param string $ext
     * @return boolean|string|NULL|number
     */
    public function SetExtension(string $ext)
    {
        if (! $this->get('IdChannel')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('DefaultExtension', $ext);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function GetFormat()
    {
        return $this->get('Format');
    }

    /**
     * Changes the default format for the current channel
     * 
     * @param string $format
     * @return bool|int|null|string
     */
    public function SetFormat(string $format)
    {
        if (! $this->get('IdChannel')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Format', $format);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Creates a new channel and loads its id in the class docID
     * 
     * @param string $name
     * @param string $defaultExtension
     * @param string $format
     * @param string $description
     * @param int $idChannel
     * @param string $filter
     * @param string $renderMode
     * @param string $outputType
     * @param string $renderType
     * @param string $language
     * @return boolean|string
     */
    public function createNewChannel(string $name, string $defaultExtension, string $format = null, string $description = null
        , int $idChannel = null, string $filter = '', string $renderMode = null, string $outputType = '', string $renderType = null
        , string $language = null)
    {
        $this->set('IdChannel', (int) $idChannel);
        $this->set('Name', $name);
        $this->set('Description', $description);
        $this->set('DefaultExtension', $defaultExtension);
        $this->set('Format', $format);
        $this->set('Filter', $filter);
        $this->set('RenderMode', $renderMode);
        $this->set('OutputType', $outputType);
        $this->set('Default_Channel', 0);
        $this->set('RenderType', $renderType);
        $this->set('idLanguage', $language);
        if ($this->add()) {
            return $this->get('IdChannel');
        }
        return false;
    }

    /**
     * Deletes current channel
     * 
     * @return boolean|number|string
     */
    public function deleteChannel()
    {
        if (! $this->get('IdChannel')) {
            $this->setError(2);
            return false;
        }
        return $this->delete();
    }

    public function getFilter()
    {
        return $this->get('Filter');
    }

    public function setFilter(string $filter)
    {
        if (! $this->get('IdChannel')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Filter', $filter);
        if ($result) {
            return $this->update();
        }
        return false;
    }
    
    public function clearError()
    {
        $this->flagErr = false;
    }

    public function setError(int $code)
    {
        $this->flagErr = true;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    public function hasError()
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr) {
            $this->clearError();
        }
        return $aux;
    }

    /**
     * Set property Default_Channel to 0 for all
     * Channels except the channel with id $idchannel
     * 
     * @param int $idchannel
     * @return boolean
     */
    public function setDefaultChannelToZero(int $idchannel)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        return $dbObj->Execute(sprintf('UPDATE Channels SET Default_Channel = 0 WHERE IdChannel <> %s', $idchannel));
    }
    
    public function getRenderType() : string
    {
        return $this->RenderType;
    }
    
    public function getIdLanguage() : string
    {
        return $this->idLanguage;
    }
    
    public function setRenderType(string $renderType) : void
    {
        $this->RenderType = $renderType;
    }
    
    public function setIdLanguage(string $idLanguage) : void
    {
        $this->idLanguage = $idLanguage;
    }
    
    public function hasServers() : bool
    {
        if (! $this->IdChannel) {
            return false;
        }
        $relServers = new RelServersChannels();
        $servers = $relServers->count('IdChannel = ' . $this->IdChannel);
        if ($servers === false) {
            return false;
        }
        return $servers > 0;
    }
}
