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

use Ximdex\Models\ORM\ChannelsOrm;


class Channel extends ChannelsOrm
{
    /**
     * @var
     */
    var $nodeID;
    var $autoCleanErr;
    var $msgErr;

    var $errorList = array(
        1 => 'Database connection error',
        2 => 'Channel does not exist',
        3 => 'Database inconsistency'
    );
    public $flagErr;
    public $numErr;

    /**
     * Channel constructor.
     * @param null $nodeID
     */
    function __construct($nodeID = null)
    {
        $errorList[1] = _('Database connection error');
        $errorList[2] = _('Channel does not exist');
        $errorList[3] = _('Database inconsistency');

        parent::__construct($nodeID);
    }

    /**
     * @param $idNode
     * @return array|null
     */
    function getChannelsForNode($idNode)
    {

        $node = new Node($idNode);
        $channels = array();
        $channs = $node->getProperty('channel');

        if (!is_array($channs)) {
            // Inherits the system properties
            $channs = array();
            $systemChannels = $this->find('IdChannel');
            foreach ($systemChannels as $channel) {
                $channs[] = $channel['IdChannel'];
            }
        }

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

    /**
     * @param null $order
     * @return array|null
     */

    function GetAllChannels($order = NULL)
    {
        $salida = null;
        $validDirs = array('ASC', 'DESC');
        $sql = "SELECT IdChannel FROM Channels";
        if (!empty($order) && is_array($order) && isset($order['FIELD'])) {
            $sql .= sprintf(" ORDER BY %s %s", $order['FIELD'],
                isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
        }

        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->GetValue("IdChannel");
            $dbObj->Next();
        }

        return $salida ? $salida : NULL;
    }

    /**
     * @return bool|string
     */
    function GetID()
    {
        if ($this->get('IdChannel') > 0) {
            return $this->get('IdChannel');
        } else {
            $this->SetError(2);
            return false;
        }
    }

    /**
     * Load a channel with a given id //TODO try to delete this functions in the code
     *

     */
    /**
     * @param $nodeID
     * @return null
     */
    function SetID($nodeID)
    {
        parent::__construct($nodeID);
        if (!($this->IdChannel > 0)) {
            $this->SetError(2);
            return null;
        }
        return $this->IdChannel;
    }

    /**
     * Changes the current channel id.
     */
    /**
     * @param $name
     * @return null
     */
    function SetByName($name)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT IdChannel FROM Channels WHERE Name='%s'", $dbObj->sqlEscapeString($name)));
        parent::__construct($dbObj->GetValue('IdChannel'));

        if (!($this->IdChannel > 0)) {
            $this->SetError(2);
            return null;
        }
        return $this->IdChannel;
    }

    /**
     * @return bool|string
     */
    function GetName()
    {
        return $this->get('Name');
    }

    /**
     * Changes the current channel name.
     */
    /**
     * @param $name
     * @return bool|int|null|string
     */
    function SetName($name)
    {
        if (!($this->get('IdChannel') > 0)) {
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
     * Returns the current channel description.
     * @return string (description)
     */
    function GetDescription()
    {
        return $this->get('Description');
    }

    /**
     * Changes the current channel description.
     * @param $description
     * @return int (status)
     */
    function SetDescription($description)
    {
        if (!($this->get('IdChannel') > 0)) {
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
     * @return string (description)
     */
    function GetExtension()
    {
        return $this->get('DefaultExtension');
    }

    /**
     * Changes the default extension for current channel.
     * @param $ext
     * @return int (status)
     */
    function SetExtension($ext)
    {
        if (!($this->get('IdChannel') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('DefaultExtension', $ext);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * @return bool|string
     */
    function GetFormat()
    {
        return $this->get('Format');
    }

    /**
     * Changes the default format for the current channel.
     */
    /**
     * @param $format
     * @return bool|int|null|string
     */
    function SetFormat($format)
    {
        if (!($this->get('IdChannel') > 0)) {
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
     * Creates a new channel and loads its id in the class docID.
     */
    /**
     * @param $name
     * @param $defaultExtension
     * @param $format
     * @param $description
     * @param $idChannel
     * @param string $filter
     * @param string $renderMode
     * @param string $outputType
     * @return bool|string
     */
    function CreateNewChannel($name, $defaultExtension, $format, $description, $idChannel, $filter = "", $renderMode = "", $outputType = "")
    {
        $this->set('IdChannel', (int)$idChannel);
        $this->set('Name', $name);
        $this->set('Description', $description);
        $this->set('DefaultExtension', $defaultExtension);
        $this->set('Format', $format);
        $this->set('Filter', $filter);
        $this->set('RenderMode', $renderMode);
        $this->set('OutputType', $outputType);
        $this->set('Default_Channel', 0);
        if ($this->add()) {
            return $this->get('IdChannel');
        }
        return false;
    }

    /**
     * Deletes current channel
     */
    /**
     * @return bool|int|string
     */
    function DeleteChannel()
    {
        if (!($this->get('IdChannel') > 0)) {
            $this->SetError(2);
            return false;
        }

        return $this->delete();
    }

    /**
     * @return bool|string
     */
    function GetFilter()
    {
        return $this->get('Filter');
    }

    /**
     * @param $filter
     * @return bool|int|null|string
     */
    function SetFilter($filter)
    {
        if (!($this->get('IdChannel') > 0)) {
            $this->SetError(2);
            return false;
        }

        $result = $this->set('Filter', $filter);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     *
     */
    function ClearError()
    {
        $this->flagErr = FALSE;
    }

    /**
     * @param $code
     */
    function SetError($code)
    {
        $this->flagErr = TRUE;
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    /**
     * @return mixed
     */
    function HasError()
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr)
            $this->ClearError();
        return $aux;
    }

    /**
     * Set property Default_Channel to 0 for all
     * Channels except the channel with id $idchannel
     */
    /**
     * @param $idchannel
     * @return bool
     */
    function setDefaultChannelToZero($idchannel)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        return $dbObj->Execute(sprintf("UPDATE Channels SET Default_Channel=0 WHERE IdChannel<>%s;", $idchannel));
    }
}
